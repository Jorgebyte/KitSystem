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

class GiveKitAllForm extends CustomForm{
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
			($this->t)(LangKey::TITLE_GIVE_KIT_ALL->value)
		);
	}

	protected function onCreation() : void{
		$t = $this->t;
		$kitManager = Main::getInstance()->getKitManager();
		$dropdown = new Dropdown($t(LangKey::LABEL_SELECT_KIT->value));
		foreach($kitManager->getAllKits() as $kit){
			$dropdown->addOption(new Option($kit->getName(), $kit->getName()));
		}
		$this->addElement("selectedKit", $dropdown);
		$this->addElement("kitQuantity", new Slider(
			$t(LangKey::LABEL_KIT_QUANTITY_PER_PLAYER->value),
			1, 64, 1, 1
		));
	}

	protected function onSubmit(Player $player, FormResponse $response) : void{
		$t = $this->t;
		$translator = $this->translator;
		$kitManager = Main::getInstance()->getKitManager();

		$kitName = $response->getDropdownSubmittedOptionId("selectedKit");
		$kit = $kitManager->getKit($kitName);
		if($kit === null){
			$player->sendMessage($t(LangKey::ERROR_KIT_NOT_EXIST->value));
			return;
		}

		$quantity = (int) $response->getSliderSubmittedStep("kitQuantity");
		$onlinePlayers = Server::getInstance()->getOnlinePlayers();

		foreach($onlinePlayers as $target){
			if(!$kit->shouldStoreInChest() && !PlayerUtil::hasEnoughSpace($target, $kit)){
				$player->sendMessage($t(
					LangKey::ERROR_INVENTORY_SPACE->value,
					['%player%' => $target->getName()]
				));
				continue;
			}
			for($i = 0; $i < $quantity; $i++){
				if($kit->shouldStoreInChest()){
					$kitManager->giveKitChest($target, $kit);
				} else{
					$kitManager->giveKitItems($target, $kit);
				}
			}
		}

		foreach($onlinePlayers as $target){
			$msg = $translator->translate(
				$target,
				LangKey::GIVEALL_KIT_BROADCAST->value,
				[
					'%player%' => $player->getName(),
					'%quantity%' => (string) $quantity,
					'%kit%' => $kit->getName()
				]
			);
			$target->sendMessage($msg);
		}

		$player->sendMessage($t(
			LangKey::GIVEALL_SUCCESS->value,
			['%quantity%' => (string) $quantity]
		));
	}
}
