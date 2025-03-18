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
use Jorgebyte\KitSystem\form\FormManager;
use Jorgebyte\KitSystem\form\FormTypes;
use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\util\LangKey;
use Jorgebyte\KitSystem\util\PlayerUtil;
use Jorgebyte\KitSystem\util\TimeUtil;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function strval;

class KitsForm extends SimpleForm{
	protected Player $player;

	public function __construct(Player $player){
		$this->player = $player;
		parent::__construct("KitSystem - Kits Available");
	}

	protected function onCreation() : void{
		$economyProvider = Main::getInstance()->getEconomyProvider();
		$translator = Main::getInstance()->getTranslator();
		$kits = Main::getInstance()->getKitManager()->getAllKits();
		$categories = Main::getInstance()->getCategoryManager()->getAllCategories();

		foreach($categories as $category){
			$categoryPrefix = $category->getPrefix();

			$buttonLabel = $categoryPrefix . "\n" . TextFormat::GRAY . "View Kits in: " . $categoryPrefix;
			$button = new Button($buttonLabel);
			$icon = $category->getIcon();

			if($icon !== null){
				$button->setIcon(new ButtonIcon($icon));
			}

			$button->setSubmitListener(function () use ($category, $translator){
				if(!$category->canUseCategory($this->player)){
					$this->player->sendMessage($translator->translate($this->player, LangKey::WITHOUT_PERMISSIONS->value,
						["{%category}" => $category->getName()]));
					return;
				}

				FormManager::sendForm($this->player, FormTypes::CATEGORY->value, [$this->player, $category->getName()]);
			});

			$this->addButton($button);
		}

		foreach($kits as $kit){
			foreach($categories as $category){
				if($category->hasKit($kit->getName())){
					continue 2;
				}
			}

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

			$button->setSubmitListener(function () use ($translator, $kit, $kitName, $kitPrice, $economyProvider){
				$currentCooldown = Main::getInstance()->getCooldownManager()->getCooldown($this->player, $kitName);
				if($currentCooldown !== null){
					$formattedCooldown = TimeUtil::formatCooldown($currentCooldown);
					$this->player->sendMessage($translator->translate($this->player, LangKey::COOLDOWN_ACTIVE->value,
						["{%time}" => $formattedCooldown]));
					return;
				}
				if(!$kit->shouldStoreInChest() && !PlayerUtil::hasEnoughSpace($this->player, $kit)){
					$this->player->sendMessage($translator->translate($this->player, LangKey::FULL_INV->value));
					return;
				}
				$processKit = function () use ($translator, $kit, $kitName) : void{
					if($kit->shouldStoreInChest()){
						Main::getInstance()->getKitManager()->giveKitChest($this->player, $kit);
					} else{
						Main::getInstance()->getKitManager()->giveKitItems($this->player, $kit);
					}

					$this->player->sendMessage($translator->translate($this->player, LangKey::KIT_CLAIMED->value,
						["{%kitname}" => $kitName]));

					$cooldownDuration = $kit->getCooldown();
					if($cooldownDuration > 0){
						Main::getInstance()->getCooldownManager()->setCooldown($this->player, $kitName, $cooldownDuration);
					}
				};
				if($kitPrice > 0){
					$economyProvider->getMoney($this->player, function ($balance) use ($economyProvider, $kitPrice, $processKit, $translator){
						if($balance < $kitPrice){
							$this->player->sendMessage($translator->translate($this->player, LangKey::LACK_OF_MONEY->value,
								["{%kitprice}" => strval($kitPrice)]));
							return;
						}

						$economyProvider->takeMoney($this->player, $kitPrice, function (bool $success) use ($processKit, $translator){
							if(!$success){
								$this->player->sendMessage($translator->translate($this->player, LangKey::FAILED_MONEY->value));
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
