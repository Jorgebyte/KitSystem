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
use Jorgebyte\KitSystem\form\ActionType;
use Jorgebyte\KitSystem\form\FormManager;
use Jorgebyte\KitSystem\form\FormTypes;
use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\menu\MenuManager;
use Jorgebyte\KitSystem\menu\MenuTypes;
use Jorgebyte\KitSystem\util\LangKey;
use Jorgebyte\KitSystem\util\ResolveIcon;
use pocketmine\player\Player;

/**
 * Dynamic form that displays all available kits and allows the player
 * to perform a contextual action based on the selected kit.
 *
 * The behavior of the form is determined by the provided `ActionType` enum value.
 *
 * Supported actions:
 * - ActionType::DELETE_KIT → Opens a confirmation form for deleting the selected kit.
 * - ActionType::EDIT_KIT   → Navigates to a submenu to choose which kit properties to edit.
 * - ActionType::PREVIEW_KIT → Displays the kit's contents in a preview inventory menu.
 *
 * @see ActionType for valid values.
 */
class SelectKitForm extends SimpleForm{
	private Player $player;
	private Translator $translator;
	private \Closure $t;
	protected ActionType $args;

	/**
	 * @param Player     $player The player who is interacting with the form.
	 * @param ActionType $args   The action to perform when a kit is selected.
	 */
	public function __construct(Player $player, ActionType $args){
		$this->player = $player;
		$this->translator = Main::getInstance()->getTranslator();
		$this->t = fn(string $key, array $r = []) => $this->translator->translate($this->player, $key, $r);
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
					case ActionType::DELETE_KIT:
						FormManager::sendForm($player, FormTypes::DELETE_KIT_SUBFORM->value, [$player, $kit->getName()]);
						break;
					case ActionType::EDIT_KIT:
						FormManager::sendForm($player, FormTypes::WHAT_TO_EDIT_SUBFORM->value, [$player, $kit->getName()]);
						break;
					case ActionType::PREVIEW_KIT:
						MenuManager::sendMenu($player, MenuTypes::PREVIEW_KIT->value, [$kit->getName()]);
						break;
				}
			});
			$this->addButton($button);
		}
	}
}
