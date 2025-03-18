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

namespace Jorgebyte\KitSystem\listener;

use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\util\LangKey;
use Jorgebyte\KitSystem\util\PlayerUtil;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;

class ClaimListener implements Listener{
	public function onPlayerInteract(PlayerInteractEvent $event) : void{
		$player = $event->getPlayer();
		$translator = Main::getInstance()->getTranslator();
		$item = $player->getInventory()->getItemInHand();

		$kitName = $item->getNamedTag()->getString("kitName", "");
		if($kitName === ""){
			return;
		}

		$kitManager = Main::getInstance()->getKitManager();
		$kit = $kitManager->getKit($kitName);
		if($kit === null){
			return;
		}

		$event->cancel();
		if(!PlayerUtil::hasEnoughSpace($player, $kit)){
			$player->sendMessage($translator->translate($player, LangKey::FULL_INV->value));
			return;
		}

		$kitManager->giveKitItems($player, $kit);
		$player->getInventory()->removeItem($item->setCount(1));
		$player->sendMessage($translator->translate($player, LangKey::OPEN_KIT->value,
			["{%kitname}" => $kitName]));
	}
}
