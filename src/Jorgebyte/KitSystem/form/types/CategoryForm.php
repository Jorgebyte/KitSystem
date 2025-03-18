<?php

/*
 *    -- KitSystem --
 *
 *    Author: Jorgebyte
 *    Discord Contact: jorgess__
 *
 *   https://github.com/Jorgebyte/KitSystem
 */

declare(strict_types=1);

namespace Jorgebyte\KitSystem\form\types;

use EasyUI\element\Button;
use EasyUI\icon\ButtonIcon;
use EasyUI\variant\SimpleForm;
use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\util\LangKey;
use Jorgebyte\KitSystem\util\PlayerUtil;
use Jorgebyte\KitSystem\util\TimeUtil;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class CategoryForm extends SimpleForm{
	protected string $categoryName;
	protected Player $player;

	public function __construct(Player $player, string $categoryName){
		$this->player = $player;
		$this->categoryName = $categoryName;

		parent::__construct("KitSystem - Category: " . $this->categoryName);
	}

	protected function onCreation() : void{
		$economyProvider = Main::getInstance()->getEconomyProvider();
		$translator = Main::getInstance()->getTranslator();
		$categoryManager = Main::getInstance()->getCategoryManager();
		$kitManager = Main::getInstance()->getKitManager();

		$kits = $categoryManager->getKitsByCategory($this->categoryName);

		foreach($kits as $kit){
			if(!$kit->canUseKit($this->player)){
				continue;
			}

			$kitName = $kit->getName();
			$kitPrefix = $kit->getPrefix();
			$kitPrice = $kit->getPrice() ?? 0;
			$cooldown = Main::getInstance()->getCooldownManager()->getCooldown($this->player, $kitName);
			$buttonLabel = $kitPrefix . "\n";
			if($cooldown !== null){
				$formattedCooldown = TimeUtil::formatCooldown($cooldown);
				$buttonLabel .= TextFormat::RED . "Cooldown: " . $formattedCooldown;
			} else{
				$buttonLabel .= TextFormat::MINECOIN_GOLD . "Price: " . ($kitPrice > 0 ? $kitPrice : "FREE!");
			}

			$button = new Button($buttonLabel);
			$icon = $kit->getIcon();
			if($icon !== null){
				$button->setIcon(new ButtonIcon($icon));
			}

			$button->setSubmitListener(function () use ($translator, $economyProvider, $kit, $kitName, $kitPrice) : void{
				// Obtiene el cooldown actual
				$currentCooldown = Main::getInstance()->getCooldownManager()->getCooldown($this->player, $kitName);
				if($currentCooldown !== null){
					$formattedCooldown = TimeUtil::formatCooldown($currentCooldown);
					$this->player->sendMessage(
						$translator->translate($this->player, LangKey::COOLDOWN_ACTIVE->value, ['{%time}' => $formattedCooldown])
					);
					return;
				}
				if(!$kit->shouldStoreInChest() && !PlayerUtil::hasEnoughSpace($this->player, $kit)){
					$this->player->sendMessage(
						$translator->translate($this->player, LangKey::FULL_INV->value)
					);
					return;
				}
				$processKit = function () use ($translator, $kit, $kitName) : void{
					if($kit->shouldStoreInChest()){
						Main::getInstance()->getKitManager()->giveKitChest($this->player, $kit);
					} else{
						Main::getInstance()->getKitManager()->giveKitItems($this->player, $kit);
					}
					$this->player->sendMessage(
						$translator->translate($this->player, LangKey::KIT_CLAIMED->value, ['{%kitname}' => $kitName])
					);
					$cooldownDuration = $kit->getCooldown();
					if($cooldownDuration > 0){
						Main::getInstance()->getCooldownManager()->setCooldown($this->player, $kitName, $cooldownDuration);
					}
				};
				if($kitPrice > 0){
					$economyProvider->getMoney($this->player, function ($balance) use ($economyProvider, $kitPrice, $processKit, $translator) : void{
						if($balance < $kitPrice){
							$this->player->sendMessage(
								$translator->translate($this->player, LangKey::LACK_OF_MONEY->value, ['{%kitprice}' => (string) $kitPrice])
							);
							return;
						}
						$economyProvider->takeMoney($this->player, $kitPrice, function (bool $success) use ($processKit, $translator) : void{
							if(!$success){
								$this->player->sendMessage(
									$translator->translate($this->player, LangKey::FAILED_MONEY->value)
								);
								return;
							}
							$processKit();
						});
					});
				} else{
					$processKit();
				}
			});
			$this->addButton($button);
		}
	}
}
