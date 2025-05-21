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
use EasyUI\variant\SimpleForm;
use IvanCraft623\languages\Translator;
use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\util\LangKey;
use Jorgebyte\KitSystem\util\PlayerUtil;
use Jorgebyte\KitSystem\util\ResolveIcon;
use Jorgebyte\KitSystem\util\TimeUtil;
use pocketmine\player\Player;

/**
 * Displays all kits under a specific category, and allows players to claim them.
 */
class CategoryForm extends SimpleForm{
	private Player $player;
	private string $categoryName;
	private Translator $translator;
	private \Closure $t;

	/**
	 * @param Player $player       The player viewing the category
	 * @param string $categoryName The name of the category to show kits from
	 */
	public function __construct(Player $player, string $categoryName){
		$this->player = $player;
		$this->categoryName = $categoryName;
		$this->translator = Main::getInstance()->getTranslator();
		$this->t = function(string $key, array $r = []) : string{
			return $this->translator->translate($this->player, $key, $r);
		};

		parent::__construct(
			($this->t)(LangKey::TITLE_CATEGORY->value, ['%category%' => $this->categoryName])
		);
	}

	protected function onCreation() : void{
		$t = $this->t;
		$economyProvider = Main::getInstance()->getEconomyProvider();
		$categoryManager = Main::getInstance()->getCategoryManager();
		$kitManager = Main::getInstance()->getKitManager();

		$kits = $categoryManager->getKitsByCategory($this->categoryName);
		foreach($kits as $kit){
			if(!$kit->canUseKit($this->player)){
				continue;
			}
			$name = $kit->getName();
			$prefix = $kit->getPrefix();
			$price = $kit->getPrice() ?? 0;
			$cd = Main::getInstance()->getCooldownManager()->getCooldown($this->player, $name);
			$label = $prefix . "\n";
			if($cd !== null){
				$label .= $t(
					LangKey::BUTTON_LABEL_COOLDOWN->value,
					['%cooldown%' => TimeUtil::formatCooldown($cd)]
				);
			} elseif($price > 0){
				$label .= $t(
					LangKey::BUTTON_LABEL_PRICE->value,
					['%price%' => $price]
				);
			} else{
				$label .= $t(LangKey::BUTTON_LABEL_FREE->value);
			}

			$button = new Button($label);
            $icon = ResolveIcon::resolveIcon($kit->getIcon());
            if ($icon !== null) {
                $button->setIcon($icon);
            }

            $button->setSubmitListener(function() use ($kit, $t, $economyProvider, $name, $price) : void{
				$current = Main::getInstance()->getCooldownManager()->getCooldown($this->player, $name);
				if($current !== null){
					$this->player->sendMessage($t(
						LangKey::COOLDOWN_ACTIVE->value,
						['%time%' => TimeUtil::formatCooldown($current)]
					));
					return;
				}
				if(!$kit->shouldStoreInChest() && !PlayerUtil::hasEnoughSpace($this->player, $kit)){
					$this->player->sendMessage($t(LangKey::FULL_INV->value));
					return;
				}
				$give = function() use ($kit, $t, $name) : void{
					if($kit->shouldStoreInChest()){
						Main::getInstance()->getKitManager()->giveKitChest($this->player, $kit);
					} else{
						Main::getInstance()->getKitManager()->giveKitItems($this->player, $kit);
					}
					$this->player->sendMessage($t(
						LangKey::KIT_CLAIMED->value,
						['%kitname%' => $name]
					));
					$dur = $kit->getCooldown();
					if($dur > 0){
						Main::getInstance()->getCooldownManager()->setCooldown($this->player, $name, $dur);
					}
				};
				if($price > 0){
					$economyProvider->getMoney($this->player, function(float $balance) use ($economyProvider, $price, $give, $t) : void{
						if($balance < $price){
							$this->player->sendMessage($t(
								LangKey::LACK_OF_MONEY->value,
								['%kitprice%' => (string) $price]
							));
							return;
						}
						$economyProvider->takeMoney($this->player, $price, function(bool $ok) use ($give, $t) : void{
							if(!$ok){
								$this->player->sendMessage($t(LangKey::FAILED_MONEY->value));
								return;
							}
							$give();
						});
					});
				} else{
					$give();
				}
			});

			$this->addButton($button);
		}
	}
}
