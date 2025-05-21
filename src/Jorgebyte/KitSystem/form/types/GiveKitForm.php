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
use EasyUI\element\Option;
use EasyUI\element\Slider;
use EasyUI\utils\FormResponse;
use EasyUI\variant\CustomForm;
use IvanCraft623\languages\Translator;
use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\util\LangKey;
use Jorgebyte\KitSystem\util\PlayerUtil;
use pocketmine\player\Player;
use pocketmine\Server;

/**
 * Custom form to give a kit to a specific online player.
 * Allows kit selection, quantity, and validates space/inventory.
 */
class GiveKitForm extends CustomForm{
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
			($this->t)(LangKey::TITLE_GIVE_KIT->value)
		);
	}

	protected function onCreation() : void{
		$t = $this->t;
		$onlinePlayers = Server::getInstance()->getOnlinePlayers();
		$dropPlayer = new Dropdown($t(LangKey::LABEL_SELECT_PLAYER->value));
		foreach($onlinePlayers as $p){
			$dropPlayer->addOption(new Option($p->getName(), $p->getName()));
		}
		$this->addElement("selectedPlayer", $dropPlayer);

		$kitManager = Main::getInstance()->getKitManager();
		$dropKit = new Dropdown($t(LangKey::LABEL_SELECT_KIT->value));
		foreach($kitManager->getAllKits() as $kit){
			$dropKit->addOption(new Option($kit->getName(), $kit->getName()));
		}
		$this->addElement("selectedKit", $dropKit);
		$this->addElement("kitQuantity", new Slider(
			$t(LangKey::LABEL_KIT_QUANTITY->value),
			1, 64, 1, 1
		));
	}

	protected function onSubmit(Player $player, FormResponse $response) : void{
		$t = $this->t;
		$kitManager = Main::getInstance()->getKitManager();

		$targetName = $response->getDropdownSubmittedOptionId("selectedPlayer");
		$target = Server::getInstance()->getPlayerExact($targetName);
		if($target === null){
			$player->sendMessage($t(LangKey::PLAYER_NOT_ONLINE->value));
			return;
		}

		$kitName = $response->getDropdownSubmittedOptionId("selectedKit");
		$kit = $kitManager->getKit($kitName);
		if($kit === null){
			$player->sendMessage($t(LangKey::ERROR_KIT_NOT_EXIST->value));
			return;
		}

		$quantity = (int) $response->getSliderSubmittedStep("kitQuantity");
		if(!$kit->shouldStoreInChest() && !PlayerUtil::hasEnoughSpace($target, $kit)){
			$player->sendMessage($t(
				LangKey::ERROR_INVENTORY_SPACE->value,
				['%player%' => $target->getName()]
			));
			return;
		}

		for($i = 0; $i < $quantity; $i++){
			if($kit->shouldStoreInChest()){
				$kitManager->giveKitChest($target, $kit);
			} else{
				$kitManager->giveKitItems($target, $kit);
			}
		}

		$player->sendMessage($t(
			LangKey::GIVE_KIT_SUCCESS->value,
			[
				'%quantity%' => (string) $quantity,
				'%player%' => $target->getName()
			]
		));
	}
}
