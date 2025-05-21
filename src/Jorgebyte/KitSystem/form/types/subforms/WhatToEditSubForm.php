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

namespace Jorgebyte\KitSystem\form\types\subforms;

use EasyUI\element\Button;
use EasyUI\variant\SimpleForm;
use IvanCraft623\languages\Translator;
use Jorgebyte\KitSystem\form\FormManager;
use Jorgebyte\KitSystem\form\FormTypes;
use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\menu\MenuManager;
use Jorgebyte\KitSystem\menu\MenuTypes;
use Jorgebyte\KitSystem\util\LangKey;
use pocketmine\player\Player;

/**
 * Form that lets the user decide whether to edit the items or metadata of a kit.
 *
 * This is part of the kit editing flow, invoked from {@see SelectKitForm} when editing.
 */
class WhatToEditSubForm extends SimpleForm{
	private Player $player;
	private string $kitName;
	private Translator $translator;
	private \Closure $t;

	public function __construct(Player $player, string $kitName){
		$this->player = $player;
		$this->kitName = $kitName;
		$this->translator = Main::getInstance()->getTranslator();
		$this->t = function(string $key, array $replacements = []) : string{
			return $this->translator->translate($this->player, $key, $replacements);
		};

		parent::__construct(
			($this->t)(LangKey::TITLE_WHAT_TO_EDIT->value)
		);
	}

	protected function onCreation() : void{
		$t = $this->t;

		$editItems = new Button($t(LangKey::BUTTON_LABEL_EDIT_ITEMS->value));
		$editItems->setSubmitListener(fn(Player $sender) =>
		MenuManager::sendMenu($sender, MenuTypes::EDIT_KIT->value, [$this->kitName])
		);

		$editData = new Button($t(LangKey::BUTTON_LABEL_EDIT_DATA->value));
		$editData->setSubmitListener(fn(Player $sender) =>
		FormManager::sendForm($sender, FormTypes::EDIT_KIT_DATA->value, [$sender, $this->kitName])
		);

		$this->addButton($editItems);
		$this->addButton($editData);
	}
}
