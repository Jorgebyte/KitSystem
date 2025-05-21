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

namespace Jorgebyte\KitSystem\util;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;

/**
 * Utility class to handle sound playback for players.
 */
final class Sound{

	/**
	 * Plays a sound at the player's position with optional volume and pitch.
	 */
	public static function addSound(Player $player, string $soundName, float $volume = 1.0, float $pitch = 1.0) : void{
		$packet = self::createPacket($player->getPosition(), $soundName, $volume, $pitch);
		$player->getNetworkSession()->sendDataPacket($packet);
	}

	/**
	 * Constructs a PlaySoundPacket for the specified parameters.
	 */
	private static function createPacket(Vector3 $vec, string $soundName, float $volume = 1.0, float $pitch = 1.0) : PlaySoundPacket{
		return PlaySoundPacket::create($soundName, $vec->x, $vec->y, $vec->z, $volume, $pitch);
	}
}
