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
use Jorgebyte\KitSystem\menu\MenuManager;
use Jorgebyte\KitSystem\menu\MenuTypes;
use pocketmine\player\Player;

class SelectKitForm extends SimpleForm{
	protected string $args;

	public function __construct(string $args){
		$this->args = $args;
		parent::__construct("KitSystem - Select Kit");
	}

	protected function onCreation() : void{
		$kits = Main::getInstance()->getKitManager()->getAllKits();

		foreach($kits as $kit){
			$kitName = $kit->getName();
			$kitPrefix = $kit->getPrefix();

			$button = new Button($kitPrefix);
			$icon = $kit->getIcon();

			if($icon !== null){
				$button->setIcon(new ButtonIcon($icon));
			}

			$button->setSubmitListener(function (Player $player) use ($kitName) : void{
				switch($this->args){
					case "deletekit":
						FormManager::sendForm($player, FormTypes::DELETE_KIT_SUBFORM->value, [$kitName]);
						break;
					case "editkit":
						FormManager::sendForm($player, FormTypes::WHAT_TO_EDIT_SUBFORM->value, [$kitName]);
						break;
					case "previewkit":
						MenuManager::sendMenu($player, MenuTypes::PREVIEW_KIT->value, [$kitName]);
				}
			});

			$this->addButton($button);
		}
	}
}
