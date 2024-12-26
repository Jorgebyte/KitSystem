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

namespace Jorgebyte\KitSystem\cooldown;

use Jorgebyte\KitSystem\cooldown\async\LoadCooldownsTask;
use Jorgebyte\KitSystem\cooldown\async\RemoveCooldownTask;
use Jorgebyte\KitSystem\cooldown\async\SaveCooldownTask;
use pocketmine\player\Player;
use pocketmine\Server;
use function file_exists;
use function time;
use const SQLITE3_INTEGER;

class CooldownManager{
	private array $cooldowns = [];
	private string $dataPath;

	public function __construct(string $dataPath){
		$this->dataPath = $dataPath . "cooldowns.db";
		$this->loadCooldowns();
	}

	public function setCooldown(Player $player, string $kitName, int $cooldownSeconds) : void{
		$uuid = $player->getUniqueId()->toString();
		$expiryTime = time() + $cooldownSeconds;

		if($cooldownSeconds > 0){
			$this->cooldowns[$uuid][$kitName] = $expiryTime;
			$this->saveCooldownAsync($uuid, $kitName, $expiryTime);
		}
	}

	public function getCooldown(Player $player, string $kitName) : ?int{
		$uuid = $player->getUniqueId()->toString();
		if(isset($this->cooldowns[$uuid][$kitName])){
			if($this->cooldowns[$uuid][$kitName] <= time()){
				$this->clearCooldown($player, $kitName);
				return null;
			}
			return $this->cooldowns[$uuid][$kitName];
		}
		return null;
	}

	private function clearCooldown(Player $player, string $kitName) : void{
		$uuid = $player->getUniqueId()->toString();
		unset($this->cooldowns[$uuid][$kitName]);
		Server::getInstance()->getAsyncPool()->submitTask(new RemoveCooldownTask($this->dataPath, $uuid, $kitName));
	}

	public function saveAllCooldowns() : void{
		$db = new \SQLite3($this->dataPath);
		$db->exec("CREATE TABLE IF NOT EXISTS cooldowns (uuid TEXT, kit TEXT, expiry INTEGER, PRIMARY KEY (uuid, kit))");

		foreach($this->cooldowns as $uuid => $kits){
			foreach($kits as $kitName => $expiryTime){
				if($expiryTime > time()){
					$stmt = $db->prepare("INSERT OR REPLACE INTO cooldowns (uuid, kit, expiry) VALUES (:uuid, :kit, :expiry)");
					if($stmt === false){
						Server::getInstance()->getLogger()->info("ERROR: Failed to prepare SQLite statement for saving cooldowns");
						continue;
					}
					$stmt->bindValue(":uuid", $uuid);
					$stmt->bindValue(":kit", $kitName);
					$stmt->bindValue(":expiry", $expiryTime, SQLITE3_INTEGER);
					$result = $stmt->execute();
					if($result === false){
						Server::getInstance()->getLogger()->info("ERROR: Failed to execute SQLite statement for UUID: " . $uuid . "Kit: " . $kitName);
					}
					$stmt->close();
				}
			}
		}
		$db->close();
	}

	public function addCooldownDirectly(string $uuid, string $kitName, int $expiryTime) : void{
		if($expiryTime > time()){
			$this->cooldowns[$uuid][$kitName] = $expiryTime;
		}
	}

	private function loadCooldowns() : void{
		$dbFilePath = $this->dataPath;
		if(!file_exists($dbFilePath)){
			$db = new \SQLite3($dbFilePath);
			$db->exec("CREATE TABLE IF NOT EXISTS cooldowns (uuid TEXT, kit TEXT, expiry INTEGER)");
			$db->close();
		}

		Server::getInstance()->getAsyncPool()->submitTask(new LoadCooldownsTask($dbFilePath));
	}

	private function saveCooldownAsync(string $uuid, string $kitName, int $expiryTime) : void{
		if($expiryTime > time()){
			Server::getInstance()->getAsyncPool()->submitTask(new SaveCooldownTask($this->dataPath, $uuid, $kitName, $expiryTime));
		}
	}
}
