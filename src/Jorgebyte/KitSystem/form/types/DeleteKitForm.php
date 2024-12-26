<?php

/*
 *   -- KitSystem --
 *
 *   Author: Jorgebyte
 *   Discord Contact: jorgess__
 *
 *  https://github.com/Jorgebyte/KitSystem
 */

declare(strict_types=1);

namespace Jorgebyte\KitSystem\form\types;

use EasyUI\element\Button;
use EasyUI\icon\ButtonIcon;
use EasyUI\variant\SimpleForm;
use Jorgebyte\KitSystem\form\FormManager;
use Jorgebyte\KitSystem\form\FormTypes;
use Jorgebyte\KitSystem\Main;
use pocketmine\player\Player;

class DeleteKitForm extends SimpleForm{
	public function __construct(){
		parent::__construct("KitSystem - Delete a Kit");
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
				FormManager::sendForm($player, FormTypes::DELETE_KIT_SUBFORM->value, [$kitName]);
			});

			$this->addButton($button);
		}
	}
}
