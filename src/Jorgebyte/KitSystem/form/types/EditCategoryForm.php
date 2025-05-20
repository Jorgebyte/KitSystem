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

use EasyUI\element\Dropdown;
use EasyUI\element\Input;
use EasyUI\element\Option;
use EasyUI\utils\FormResponse;
use EasyUI\variant\CustomForm;
use IvanCraft623\languages\Translator;
use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\util\LangKey;
use Jorgebyte\KitSystem\util\Sound;
use Jorgebyte\KitSystem\util\SoundNames;
use pocketmine\player\Player;
use function array_filter;

class EditCategoryForm extends CustomForm{
	private Player $player;
	private Translator $translator;
	private \Closure $t;
	protected string $categoryName;

	public function __construct(Player $player, string $categoryName){
		$this->player = $player;
		$this->translator = Main::getInstance()->getTranslator();
		$this->categoryName = $categoryName;
		$this->t = function(string $key, array $r = []) : string{
			return $this->translator->translate($this->player, $key, $r);
		};

		parent::__construct(
			($this->t)(LangKey::TITLE_EDIT_CATEGORY->value)
		);
	}

	public function onCreation() : void{
		$t = $this->t;
		$mgrCat = Main::getInstance()->getCategoryManager();
		$mgrKit = Main::getInstance()->getKitManager();
		$cat = $mgrCat->getCategory($this->categoryName);
		if($cat === null)return;

		$this->addElement("categoryPrefix", new Input(
			$t(LangKey::LABEL_CATEGORY_PREFIX->value),
			$cat->getPrefix()
		));
		$this->addElement("categoryPermission", new Input(
			$t(LangKey::LABEL_PERMISSION->value),
			$cat->getPermission() ?? ""
		));
		$this->addElement("categoryIcon", new Input(
			$t(LangKey::LABEL_ICON->value),
			$cat->getIcon() ?? ""
		));

		$add = new Dropdown($t(LangKey::LABEL_ADD_KIT->value));
		$add->addOption(new Option("None", "None"));
		foreach(array_filter($mgrKit->getAllKits(), fn($k) => !$cat->hasKit($k->getName())) as $k){
			$add->addOption(new Option($k->getName(), $k->getName()));
		}
		$this->addElement("addKit", $add);

		$rem = new Dropdown($t(LangKey::LABEL_REMOVE_KIT->value));
		$rem->addOption(new Option("None", "None"));
		foreach($cat->getKits() as $k){
			$rem->addOption(new Option($k->getName(), $k->getName()));
		}
		$this->addElement("removeKit", $rem);
	}

	protected function onSubmit(Player $player, FormResponse $response) : void{
		$t = $this->t;
		$mgrCat = Main::getInstance()->getCategoryManager();
		$mgrKit = Main::getInstance()->getKitManager();
		$cat = $mgrCat->getCategory($this->categoryName);
		if($cat === null)return;

		$cat->setPrefix(
			$response->getInputSubmittedText("categoryPrefix")
		);
		$cat->setPermission(
			$response->getInputSubmittedText("categoryPermission") ?: null
		);
		$cat->setIcon(
			$response->getInputSubmittedText("categoryIcon") ?: null
		);

		$toAdd = $response->getDropdownSubmittedOptionId("addKit");
		$toRemove = $response->getDropdownSubmittedOptionId("removeKit");

		if($toAdd !== "None" && ($kit = $mgrKit->getKit($toAdd)) !== null){
			$cat->addKit($kit);
			$player->sendMessage(
				$t(LangKey::CATEGORY_KIT_ADDED_SUCCESS->value, ['%kit%' => $kit->getName()])
			);
			Sound::addSound($player, SoundNames::GOOD_TONE->value);
		}
		if($toRemove !== "None" && ($kit = $mgrKit->getKit($toRemove)) !== null){
			$cat->removeKit($kit->getName());
			$player->sendMessage(
				$t(LangKey::CATEGORY_KIT_REMOVE_SUCCESS->value, ['%kit%' => $kit->getName()])
			);
			Sound::addSound($player, SoundNames::GOOD_TONE->value);
		}

		$player->sendMessage($t(LangKey::UPDATE_DATA->value));
		Sound::addSound($player, SoundNames::GOOD_TONE->value);
		$mgrCat->saveCategory($cat);
	}
}
