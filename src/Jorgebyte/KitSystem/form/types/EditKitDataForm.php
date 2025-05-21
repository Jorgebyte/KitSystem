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
use IvanCraft623\languages\Translator;
use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\util\LangKey;
use Jorgebyte\KitSystem\util\Sound;
use Jorgebyte\KitSystem\util\SoundNames;
use pocketmine\player\Player;
use function is_numeric;

/**
 * Form to edit metadata of a kit (prefix, cooldown, price, permission, icon, chest mode).
 */
class EditKitDataForm extends CustomForm{
	private Player $player;
	private Translator $translator;
	private \Closure $t;
	protected string $kitName;

	/**
	 * @param Player $player  The player editing the kit
	 * @param string $kitName The name of the kit to edit
	 */
	public function __construct(Player $player, string $kitName){
		$this->player = $player;
		$this->translator = Main::getInstance()->getTranslator();
		$this->kitName = $kitName;
		$this->t = function(string $key, array $r = []) : string{
			return $this->translator->translate($this->player, $key, $r);
		};

		parent::__construct(
			($this->t)(LangKey::TITLE_EDIT_KIT_DATA->value)
		);
	}

	public function onCreation() : void{
		$t = $this->t;
		$kit = Main::getInstance()->getKitManager()->getKit($this->kitName);
		if($kit === null)return;

		$this->addElement("kitPrefix", new Input(
			$t(LangKey::LABEL_KIT_PREFIX->value),
			$kit->getPrefix()
		));
		$this->addElement("cooldown", new Input(
			$t(LangKey::LABEL_COOLDOWN->value),
			(string) $kit->getCooldown()
		));
		$this->addElement("price", new Input(
			$t(LangKey::LABEL_PRICE->value),
			(string) $kit->getPrice()
		));
		$this->addElement("permission", new Input(
			$t(LangKey::LABEL_PERMISSION->value),
			$kit->getPermission() ?? ""
		));
		$this->addElement("icon", new Input(
			$t(LangKey::LABEL_ICON->value),
			$kit->getIcon() ?? ""
		));
		$this->addElement("storeInChest", new Toggle(
			$t(LangKey::LABEL_STORE_IN_CHEST->value),
			$kit->shouldStoreInChest()
		));
	}

	protected function onSubmit(Player $player, FormResponse $response) : void{
		$t = $this->t;
		$kit = Main::getInstance()->getKitManager()->getKit($this->kitName);
		if($kit === null)return;

		$prefix = $response->getInputSubmittedText("kitPrefix");
		$cdInput = $response->getInputSubmittedText("cooldown");
		$prInput = $response->getInputSubmittedText("price");
		$permissionText = $response->getInputSubmittedText("permission");
		$iconText = $response->getInputSubmittedText("icon");
		$storeInChest = $response->getToggleSubmittedChoice("storeInChest");

		if($prefix === ""){
			$player->sendMessage($t(LangKey::ERROR_KIT_REQUIRED->value));
			Sound::addSound($player, SoundNames::BAD_TONE->value);
			return;
		}

		$kit->setPrefix($prefix);
		$kit->setCooldown(is_numeric($cdInput) ? (int) $cdInput : null);
		$kit->setPrice(is_numeric($prInput) ? (float) $prInput : null);
		$kit->setPermission($permissionText !== '' ? $permissionText : null);
		$kit->setIcon($iconText !== '' ? $iconText : null);
		$kit->setStoreInChest($storeInChest);

		Main::getInstance()->getKitManager()->saveKit($kit);

		$player->sendMessage($t(LangKey::KIT_UPDATE->value));
		Sound::addSound($player, SoundNames::GOOD_TONE->value);
	}
}
