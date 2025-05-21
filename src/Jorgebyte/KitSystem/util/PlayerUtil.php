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

use Jorgebyte\KitSystem\kit\Kit;
use pocketmine\player\Player;

/**
 * Utility class for player-related logic.
 */
final class PlayerUtil{

	/**
	 * Determines whether the player has enough space in both inventory and armor slots
	 * to receive the specified kit.
	 *
	 * @return bool True if the player has enough space, false otherwise
	 */
	public static function hasEnoughSpace(Player $player, Kit $kit) : bool{
		$inventory = $player->getInventory();
		$armorInventory = $player->getArmorInventory();

		// Check main inventory space
		foreach($kit->getItems() as $item){
			if(!$inventory->canAddItem($item)){
				return false;
			}
		}

		// Check if armor slots are empty
		foreach($kit->getArmor() as $slot => $armorPiece){
			if(!$armorInventory->getItem($slot)->isNull()){
				return false;
			}
		}

		return true;
	}
}
