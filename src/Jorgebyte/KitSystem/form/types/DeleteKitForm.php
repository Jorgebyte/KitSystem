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
use IvanCraft623\languages\Translator;
use Jorgebyte\KitSystem\form\FormManager;
use Jorgebyte\KitSystem\form\FormTypes;
use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\util\LangKey;
use pocketmine\player\Player;

class DeleteKitForm extends SimpleForm{
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
			($this->t)(LangKey::TITLE_DELETE_KIT->value)
		);
	}

	protected function onCreation() : void{
		$kits = Main::getInstance()->getKitManager()->getAllKits();
		foreach($kits as $kit){
			$button = new Button($kit->getPrefix());
			if(($icon = $kit->getIcon()) !== null){
				$button->setIcon(new ButtonIcon($icon));
			}
			$button->setSubmitListener(function (Player $player) use ($kit) : void{
				FormManager::sendForm($player, FormTypes::DELETE_KIT_SUBFORM->value, [$player, $kit->getName()]);
			});
			$this->addButton($button);
		}
	}
}
