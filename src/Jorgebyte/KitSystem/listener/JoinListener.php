<?php

/*
 *   -- KitSystem --
 *
 *   Author: Jorgebyte
 *   Discord Contact: jorgess__
 *
 *  https://github.com/Jorgebyte/KitSystem
 */

declare(strict_types=1);

namespace Jorgebyte\KitSystem\listener;

use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\message\MessageKey;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use function file_exists;
use function is_string;

class JoinListener implements Listener{
	public function onPlayerJoin(PlayerJoinEvent $event) : void{
		$player = $event->getPlayer();
		$playerName = $player->getName();

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
			$player->sendMessage(TextFormat::RED . "ERROR: Starter kit: " . $starterKitName . " is not available");
			return;
		}

		$kitManager->giveKitChest($player, $starterKit);
		$player->sendMessage(Main::getInstance()->getMessage()->getMessage(
			MessageKey::STARTERKIT_RECEIVED,
			["{kit}" => $starterKit->getName()]
		));
	}
}
