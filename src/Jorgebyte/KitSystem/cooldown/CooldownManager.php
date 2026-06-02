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

namespace Jorgebyte\KitSystem\cooldown;

use Jorgebyte\KitSystem\Main;
use pocketmine\player\Player;
use function time;
use const PHP_INT_MAX;

/**
 * Handles cooldown logic for kits per player.
 * Responsible for setting, retrieving, clearing, and loading cooldowns.
 */
final class CooldownManager{

	/** @var array<string, array<string, int>> In-memory cooldown data (uuid => [kit => expiryTimestamp]) */
	private array $cooldowns = [];
	private bool $isLoaded = false;

	/**
	 * Initializes the cooldown manager and loads saved cooldowns from the database.
	 */
	public function __construct(private Main $plugin){
		$this->loadCooldowns();
	}

	/**
	 * Sets a cooldown for a specific kit and player.
	 * Also persists it to the database.
	 */
	public function setCooldown(Player $player, string $kitName, int $cooldownSeconds) : void{
		$uuid = $player->getUniqueId()->toString();
		$expiryTime = time() + $cooldownSeconds;
		if($cooldownSeconds > 0){
			$this->cooldowns[$uuid][$kitName] = $expiryTime;
			$this->plugin->getDatabase()->executeChange("cooldowns.set", [
				"uuid" => $uuid,
				"kit" => $kitName,
				"cooldown" => $expiryTime
			]);
		}
	}

	/**
	 * Gets the active cooldown expiry timestamp for the player and kit.
	 *
	 * @return int|null Null if no cooldown is active or expired
	 */
	public function getCooldown(Player $player, string $kitName) : ?int{
		if(!$this->isLoaded){
			return PHP_INT_MAX; // Prevent claiming until cooldowns are loaded
		}

		$uuid = $player->getUniqueId()->toString();
		if(isset($this->cooldowns[$uuid][$kitName])){
			$currentTime = time();
			if($this->cooldowns[$uuid][$kitName] <= $currentTime){
				$this->clearCooldown($player, $kitName);
				return null;
			}
			return $this->cooldowns[$uuid][$kitName];
		}
		return null;
	}

	/**
	 * Clears the cooldown for a specific player and kit.
	 */
	private function clearCooldown(Player $player, string $kitName) : void{
		$uuid = $player->getUniqueId()->toString();
		unset($this->cooldowns[$uuid][$kitName]);
		if(empty($this->cooldowns[$uuid])){
			unset($this->cooldowns[$uuid]);
		}

		$this->plugin->getDatabase()->executeChange("cooldowns.remove", [
			"uuid" => $uuid,
			"kit" => $kitName
		]);
	}

	/**
	 * Loads all active (non-expired) cooldowns from the database into memory.
	 */
	private function loadCooldowns() : void{
		$this->plugin->getDatabase()->executeSelect("cooldowns.get_all", [], function(array $rows) : void{
			$currentTime = time();
			foreach($rows as $row){
				if((int) $row["cooldown"] > $currentTime){
					$this->cooldowns[$row["uuid"]][$row["kit"]] = (int) $row["cooldown"];
				}
			}
			$this->isLoaded = true;
		});
	}
}
