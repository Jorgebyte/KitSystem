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
use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\util\Sound;
use Jorgebyte\KitSystem\util\SoundNames;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function array_filter;

class EditCategoryForm extends CustomForm{
	protected string $categoryName;

	public function __construct(string $categoryName){
		$this->categoryName = $categoryName;
		parent::__construct("KitSystem - Edit Category");
	}

	public function onCreation() : void{
		$categoryManager = Main::getInstance()->getCategoryManager();
		$kitManager = Main::getInstance()->getKitManager();
		$category = $categoryManager->getCategory($this->categoryName);

		if($category === null){
			return;
		}

		$this->addElement("categoryPrefix", new Input("Category Prefix", $category->getPrefix()));
		$this->addElement("categoryPermission", new Input("Permission (optional)", null, $category->getPermission() ?? ""));
		$this->addElement("categoryIcon", new Input("Icon URL (optional)", null, $category->getIcon() ?? ""));

		$kitsToAddDropdown = new Dropdown("Add Kit to Category");
		$kitsToAddDropdown->addOption(new Option("None", "None"));

		$kitsNotInCategory = array_filter(
			$kitManager->getAllKits(),
			fn ($kit) => !$category->hasKit($kit->getName())
		);

		foreach($kitsNotInCategory as $kit){
			$kitsToAddDropdown->addOption(new Option($kit->getName(), $kit->getName()));
		}

		$this->addElement("addKit", $kitsToAddDropdown);

		$kitsToRemoveDropdown = new Dropdown("Remove Kit from Category");
		$kitsToRemoveDropdown->addOption(new Option("None", "None"));

		foreach($category->getKits() as $kit){
			$kitsToRemoveDropdown->addOption(new Option($kit->getName(), $kit->getName()));
		}

		$this->addElement("removeKit", $kitsToRemoveDropdown);
	}

	protected function onSubmit(Player $player, FormResponse $response) : void{
		$categoryManager = Main::getInstance()->getCategoryManager();
		$kitManager = Main::getInstance()->getKitManager();
		$category = $categoryManager->getCategory($this->categoryName);

		if($category === null){
			return;
		}

		$prefix = $response->getInputSubmittedText("categoryPrefix");
		$permission = $response->getInputSubmittedText("categoryPermission");
		$icon = $response->getInputSubmittedText("categoryIcon");
		$kitToAdd = $response->getDropdownSubmittedOptionId("addKit");
		$kitToRemove = $response->getDropdownSubmittedOptionId("removeKit");

		$category->setPrefix($prefix);
		$category->setPermission($permission !== '' ? $permission : null);
		$category->setIcon($icon !== '' ? $icon : null);

		if($kitToAdd !== "None"){
			$kit = $kitManager->getKit($kitToAdd);
			if($kit !== null){
				$category->addKit($kit);
				$player->sendMessage(TextFormat::GREEN . "The kit: " . TextFormat::MINECOIN_GOLD . $kit->getName() . TextFormat::GREEN . "Successfully added from the category");
				Sound::addSound($player, SoundNames::GOOD_TONE->value);
			}
		}

		if($kitToRemove !== "None"){
			$kit = $kitManager->getKit($kitToRemove);
			if($kit !== null){
				$category->removeKit($kit->getName());
				$player->sendMessage(TextFormat::GREEN . "The kit: " . TextFormat::MINECOIN_GOLD . $kit->getName() . TextFormat::GREEN . "Successfully removed from the category");
				Sound::addSound($player, SoundNames::GOOD_TONE->value);
			}
		}
		$player->sendMessage(TextFormat::GREEN . "The data has been updated successfully!!!");
		Sound::addSound($player, SoundNames::GOOD_TONE->value);
		$categoryManager->saveCategory($category);
	}
}
