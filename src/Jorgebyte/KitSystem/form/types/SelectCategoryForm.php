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
use Jorgebyte\KitSystem\util\LangKey;
use Jorgebyte\KitSystem\util\ResolveIcon;
use pocketmine\player\Player;

/**
 * Presents a list of available categories to the player,
 * allowing them to choose one and dispatch an action based on a predefined enum `ActionType`.
 *
 * This form supports various actions on categories such as deletion or edition.
 * Actions are context-sensitive and passed during instantiation.
 *
 * Example usage:
 * - ActionType::DELETE_CATEGORY → opens category deletion confirmation
 * - ActionType::EDIT_CATEGORY   → opens category edition form
 *
 * @see ActionType for available options
 */
class SelectCategoryForm extends SimpleForm{
	private Player $player;
	private Translator $translator;
	private \Closure $t;
	protected ActionType $args;

	/**
	 * @param Player     $player The player using the form
	 * @param ActionType $args   The action to perform on category selection
	 */
	public function __construct(Player $player, ActionType $args){
		$this->player = $player;
		$this->translator = Main::getInstance()->getTranslator();
		$this->t = function(string $key, array $r = []) : string{
			return $this->translator->translate($this->player, $key, $r);
		};
		$this->args = $args;
		parent::__construct(
			($this->t)(LangKey::TITLE_SELECT_CATEGORY->value)
		);
	}

	protected function onCreation() : void{
		$t = $this->t;
		$categories = Main::getInstance()->getCategoryManager()->getAllCategories();

		foreach($categories as $cat){
			$button = new Button($cat->getPrefix());
			$icon = ResolveIcon::resolveIcon($cat->getIcon());
			if($icon !== null){
				$button->setIcon($icon);
			}

			$button->setSubmitListener(function (Player $player) use ($cat) : void{
				switch($this->args){
					case ActionType::DELETE_CATEGORY:
						FormManager::sendForm($player, FormTypes::DELETE_CATEGORY_SUBFORM->value, [$player, $cat->getName()]);
						break;
					case ActionType::EDIT_CATEGORY:
						FormManager::sendForm($player, FormTypes::EDIT_CATEGORY_FORM->value, [$player, $cat->getName()]);
						break;
				}
			});
			$this->addButton($button);
		}
	}
}
