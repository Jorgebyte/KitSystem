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
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Server;
use function file_exists;
use function is_string;

/**
 * Listener for player join events.
 * Automatically gives a starter kit if enabled and player is new.
 */
class JoinListener implements Listener{
	public function onPlayerJoin(PlayerJoinEvent $event) : void{
		$player = $event->getPlayer();
		$playerName = $player->getName();
		$translator = Main::getInstance()->getTranslator();

		$dataFolder = Server::getInstance()->getDataPath() . "players/";
		$playerFile = $dataFolder . $playerName . ".dat";

		if(file_exists($playerFile)){
			return;
		}

		$config = Main::getInstance()->getConfig();
		$kitManager = Main::getInstance()->getKitManager();

		if(!(bool) $config->get("enable-starterkit")){
			return;
		}

		$starterKitName = $config->get("starterkit");
		if(!is_string($starterKitName) || $starterKitName === ""){
			Main::getInstance()->getLogger()->warning("Invalid starter kit name in config.");
			return;
		}

		$starterKit = $kitManager->getKit($starterKitName);
		if($starterKit === null){
			$player->sendMessage($translator->translate($player, LangKey::ERROR_KIT_INVALID->value,
				["%kit%" => $starterKitName]));
			return;
		}

		$kitManager->giveKitChest($player, $starterKit);
		$player->sendMessage($translator->translate($player, LangKey::STARTERKIT_RECEIVED->value,
		["%kit%" => $starterKit->getPrefix()]));
	}
}
