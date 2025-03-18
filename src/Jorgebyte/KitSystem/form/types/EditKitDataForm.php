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

use EasyUI\element\Input;
use EasyUI\element\Toggle;
use EasyUI\utils\FormResponse;
use EasyUI\variant\CustomForm;
use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\util\LangKey;
use Jorgebyte\KitSystem\util\Sound;
use Jorgebyte\KitSystem\util\SoundNames;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function is_numeric;

class EditKitDataForm extends CustomForm{
	protected string $kitName;

	public function __construct(string $kitName){
		$this->kitName = $kitName;
		parent::__construct("KitSystem - Edit Kit Data");
	}

	public function onCreation() : void{
		$kit = Main::getInstance()->getKitManager()->getKit($this->kitName);
		if($kit === null){
			return;
		}

		$this->addElement("kitPrefix", new Input("Prefix", $kit->getPrefix()));
		$this->addElement("cooldown", new Input("Cooldown (optional, in seconds)", (string) $kit->getCooldown()));
		$this->addElement("price", new Input("Price (optional)", (string) $kit->getPrice()));
		$this->addElement("permission", new Input("Permission (optional)", $kit->getPermission() ?? ""));
		$this->addElement("icon", new Input("Icon URL (optional)", $kit->getIcon() ?? ""));
		$this->addElement("storeInChest", new Toggle("Store in chest?", $kit->shouldStoreInChest()));
	}

	protected function onSubmit(Player $player, FormResponse $response) : void{
		$translator = Main::getInstance()->getTranslator();
		$kit = Main::getInstance()->getKitManager()->getKit($this->kitName);
		if($kit === null){
			return;
		}

		$kitPrefix = $response->getInputSubmittedText("kitPrefix");

		$cooldownInput = $response->getInputSubmittedText("cooldown");
		$cooldown = is_numeric($cooldownInput) ? (int) $cooldownInput : null;
		$priceInput = $response->getInputSubmittedText("price");
		$price = is_numeric($priceInput) ? (float) $priceInput : null;

		$permission = $response->getInputSubmittedText("permission");
		$permission = $permission === "" ? null : $permission;
		$icon = $response->getInputSubmittedText("icon");
		$icon = $icon === "" ? null : $icon;

		$storeInChest = $response->getToggleSubmittedChoice("storeInChest");

		if($kitPrefix === ''){
			$player->sendMessage(TextFormat::RED . "ERROR: The prefix is REQUIRED");
			Sound::addSound($player, SoundNames::BAD_TONE->value);
			return;
		}

		$kit->setPrefix($kitPrefix);
		$kit->setCooldown($cooldown);
		$kit->setPrice($price);
		$kit->setPermission($permission);
		$kit->setIcon($icon);
		$kit->setStoreInChest($storeInChest);
		Main::getInstance()->getKitManager()->saveKit($kit);
		$player->sendMessage($translator->translate($player, LangKey::KIT_UPDATE->value));
		Sound::addSound($player, SoundNames::GOOD_TONE->value);
	}
}
