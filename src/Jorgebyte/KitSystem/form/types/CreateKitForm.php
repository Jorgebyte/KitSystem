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
use EasyUI\element\Toggle;
use EasyUI\utils\FormResponse;
use EasyUI\variant\CustomForm;
use Exception;
use IvanCraft623\languages\Translator;
use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\util\LangKey;
use Jorgebyte\KitSystem\util\Sound;
use Jorgebyte\KitSystem\util\SoundNames;
use pocketmine\player\Player;
use function is_numeric;
use function trim;

/**
 * Custom form that allows players to create a new kit from their inventory and armor.
 */
class CreateKitForm extends CustomForm{
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
			($this->t)(LangKey::TITLE_CREATE_KIT->value)
		);
	}

	public function onCreation() : void{
		$t = $this->t;
		$this->addElement("kitName", new Input(
			$t(LangKey::LABEL_KIT_NAME->value),
			null,
			$t(LangKey::PLACEHOLDER_KIT_NAME->value)
		));
		$this->addElement("kitPrefix", new Input(
			$t(LangKey::LABEL_KIT_PREFIX->value),
			null,
			$t(LangKey::PLACEHOLDER_KIT_PREFIX->value)
		));
		$this->addElement("cooldown", new Input(
			$t(LangKey::LABEL_COOLDOWN->value),
			null,
			$t(LangKey::PLACEHOLDER_COOLDOWN->value)
		));
		$this->addElement("price", new Input(
			$t(LangKey::LABEL_PRICE->value),
			null,
			$t(LangKey::PLACEHOLDER_PRICE->value)
		));
		$this->addElement("permission", new Input(
			$t(LangKey::LABEL_PERMISSION->value),
			null,
			$t(LangKey::PLACEHOLDER_PERMISSION->value)
		));
		$this->addElement("icon", new Input(
			$t(LangKey::LABEL_ICON->value),
			null,
			$t(LangKey::PLACEHOLDER_ICON->value)
		));
		$this->addElement("storeInChest", new Toggle(
			$t(LangKey::LABEL_STORE_IN_CHEST->value),
			true
		));

		$dropdown = new Dropdown($t(LangKey::LABEL_SELECT_CATEGORY->value));
		$dropdown->addOption(new Option("None", "None"));

		foreach(Main::getInstance()->getCategoryManager()->getAllCategories() as $cat){
			$dropdown->addOption(new Option($cat->getName(), $cat->getName()));
		}
		$this->addElement("selectedCategory", $dropdown);
	}

	protected function onSubmit(Player $player, FormResponse $response) : void{
		$t = $this->t;

		$name = $response->getInputSubmittedText("kitName");
		$prefix = $response->getInputSubmittedText("kitPrefix");
		$cdInput = $response->getInputSubmittedText("cooldown");
		$prInput = $response->getInputSubmittedText("price");
		$perm = trim($response->getInputSubmittedText("permission"));
		$perm = $perm !== '' ? $perm : null;
		$icon = trim($response->getInputSubmittedText("icon"));
		$icon = $icon !== '' ? $icon : null;
		$store = $response->getToggleSubmittedChoice("storeInChest");
		$catSel = $response->getDropdownSubmittedOptionId("selectedCategory");
		$cat = $catSel !== "None" ? $catSel : null;

		if($name === "" || $prefix === ""){
			$player->sendMessage($t(LangKey::ERROR_KIT_REQUIRED->value));
			Sound::addSound($player, SoundNames::BAD_TONE->value);
			return;
		}

		$cooldown = is_numeric($cdInput) ? (int) $cdInput : null;
		$price = is_numeric($prInput) ? (float) $prInput : null;

		$armor = $player->getArmorInventory()->getContents();
		$inv = $player->getInventory()->getContents();

		try{
			Main::getInstance()->getKitManager()->createKit(
				$name, $prefix, $armor, $inv,
				$cooldown, $price, $perm,
				$icon, $store, $cat
			);
			$player->sendMessage(
				$t(LangKey::KIT_CREATED_SUCCESS->value, ['%kit%' => $name])
			);
			Sound::addSound($player, SoundNames::GOOD_TONE->value);
		} catch(Exception $e){
			$player->sendMessage(
				$t(LangKey::ERROR_GENERIC->value, ['%error%' => $e->getMessage()])
			);
			Sound::addSound($player, SoundNames::BAD_TONE->value);
		}
	}
}
