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
use Jorgebyte\KitSystem\menu\MenuManager;
use Jorgebyte\KitSystem\menu\MenuTypes;
use Jorgebyte\KitSystem\util\LangKey;
use Jorgebyte\KitSystem\util\ResolveIcon;
use pocketmine\player\Player;

class SelectKitForm extends SimpleForm{
	private Player $player;
	private Translator $translator;
	private \Closure $t;
	protected string $args;

	public function __construct(Player $player, string $args){
		$this->player = $player;
		$this->translator = Main::getInstance()->getTranslator();
		$this->t = function(string $key, array $r = []) : string{
			return $this->translator->translate($this->player, $key, $r);
		};
		$this->args = $args;
		parent::__construct(
			($this->t)(LangKey::TITLE_SELECT_KIT->value)
		);
	}

	protected function onCreation() : void{
		$t = $this->t;
		$kits = Main::getInstance()->getKitManager()->getAllKits();

		foreach($kits as $kit){
			$button = new Button($kit->getPrefix());
			if($icon = ResolveIcon::resolveIcon($kit->getIcon())){
				$button->setIcon($icon);
			}
			$button->setSubmitListener(function (Player $player) use ($t, $kit) : void{
				switch($this->args){
					case "deletekit":
						FormManager::sendForm($player, FormTypes::DELETE_KIT_SUBFORM->value, [$player, $kit->getName()]);
						break;
					case "editkit":
						FormManager::sendForm($player, FormTypes::WHAT_TO_EDIT_SUBFORM->value, [$player, $kit->getName()]);
						break;
					case "previewkit":
						MenuManager::sendMenu($player, MenuTypes::PREVIEW_KIT->value, [$kit->getName()]);
						break;
				}
			});
			$this->addButton($button);
		}
	}
}
