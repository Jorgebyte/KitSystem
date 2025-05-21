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
use Jorgebyte\KitSystem\form\FormManager;
use Jorgebyte\KitSystem\form\FormTypes;
use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\util\LangKey;
use Jorgebyte\KitSystem\util\PlayerUtil;
use Jorgebyte\KitSystem\util\ResolveIcon;
use Jorgebyte\KitSystem\util\TimeUtil;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function array_filter;

/**
 * Displays all available kits and categories in a main UI.
 * This form serves as the home menu for claiming kits.
 */

class KitsForm extends SimpleForm{
	private Player $player;
	private Translator $translator;
	private \Closure $t;

	public function __construct(Player $player){
		$this->player = $player;
		$this->translator = Main::getInstance()->getTranslator();
		$this->t = function(string $key, array $r = []) : string{
			return $this->translator->translate($this->player, $key, $r);
		};
		parent::__construct(
			($this->t)(LangKey::TITLE_KITS_AVAILABLE->value)
		);
	}

	protected function onCreation() : void{
		$t = $this->t;
		$economyProvider = Main::getInstance()->getEconomyProvider();
		$kits = Main::getInstance()->getKitManager()->getAllKits();
		$categories = Main::getInstance()->getCategoryManager()->getAllCategories();

		foreach($categories as $cat){
			$label = $cat->getPrefix() . "\n" .
				TextFormat::GRAY .
				$t(LangKey::BUTTON_LABEL_VIEW_KITS->value, ['%category%' => $cat->getName()]);
			$button = new Button($label);
			if($icon = ResolveIcon::resolveIcon($cat->getIcon())){
				$button->setIcon($icon);
			}
			$button->setSubmitListener(function() use ($cat, $t) : void{
				if(!$cat->canUseCategory($this->player)){
					$this->player->sendMessage($t(
						LangKey::WITHOUT_PERMISSIONS->value
					));
					return;
				}
				FormManager::sendForm(
					$this->player,
					FormTypes::CATEGORY->value,
					[$this->player, $cat->getName()]
				);
			});
			$this->addButton($button);
		}

		foreach($kits as $kit){
			if(array_filter($categories, fn($c) => $c->hasKit($kit->getName()))){
				continue;
			}
			if(!$kit->canUseKit($this->player)){
				continue;
			}

			$label = $kit->getPrefix() . "\n";
			$cd = Main::getInstance()->getCooldownManager()->getCooldown($this->player, $kit->getName());
			if($cd !== null){
				$label .= $t(
					LangKey::BUTTON_LABEL_COOLDOWN->value,
					['%cooldown%' => TimeUtil::formatCooldown($cd)]
				);
			} elseif($kit->getPrice() > 0){
				$label .= $t(
					LangKey::BUTTON_LABEL_PRICE->value,
					['%price%' => $kit->getPrice()]
				);
			} else{
				$label .= $t(LangKey::BUTTON_LABEL_FREE->value);
			}

			$button = new Button($label);
			if($icon = ResolveIcon::resolveIcon($kit->getIcon())){
				$button->setIcon($icon);
			}

			$button->setSubmitListener(function() use ($kit, $t, $economyProvider) : void{
				$kitName = $kit->getName();
				$cd = Main::getInstance()->getCooldownManager()->getCooldown($this->player, $kitName);
				if($cd !== null){
					$this->player->sendMessage($t(
						LangKey::COOLDOWN_ACTIVE->value,
						['%time%' => TimeUtil::formatCooldown($cd)]
					));
					return;
				}
				if(!$kit->shouldStoreInChest() && !PlayerUtil::hasEnoughSpace($this->player, $kit)){
					$this->player->sendMessage($t(LangKey::FULL_INV->value));
					return;
				}
				$give = function() use ($kit, $t) : void{
					if($kit->shouldStoreInChest()){
						Main::getInstance()->getKitManager()->giveKitChest($this->player, $kit);
					} else{
						Main::getInstance()->getKitManager()->giveKitItems($this->player, $kit);
					}
					$this->player->sendMessage($t(
						LangKey::KIT_CLAIMED->value,
						['%kitname%' => $kit->getName()]
					));
					$cdur = $kit->getCooldown();
					if($cdur > 0){
						Main::getInstance()->getCooldownManager()->setCooldown($this->player, $kit->getName(), $cdur);
					}
				};

				if($kit->getPrice() > 0){
					$economyProvider->getMoney($this->player, function($balance) use ($economyProvider, $kit, $give, $t) : void{
						if($balance < $kit->getPrice()){
							$this->player->sendMessage($t(
								LangKey::LACK_OF_MONEY->value,
								['%kitprice%' => (string) $kit->getPrice()]
							));
							return;
						}
						$economyProvider->takeMoney($this->player, $kit->getPrice(), function(bool $ok) use ($give, $t) : void{
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
