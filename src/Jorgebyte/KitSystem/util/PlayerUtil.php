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

namespace Jorgebyte\KitSystem\util;

use Jorgebyte\KitSystem\kit\Kit;
use pocketmine\player\Player;

class PlayerUtil{
	public static function hasEnoughSpace(Player $player, Kit $kit) : bool{
		$inventory = $player->getInventory();
		$armorInventory = $player->getArmorInventory();

		$items = $kit->getItems();
		foreach($items as $item){
			if(!$inventory->canAddItem($item)){
				return false;
			}
		}

		$armor = $kit->getArmor();
		foreach($armor as $i => $armorPiece){
			if(!$armorInventory->getItem($i)->isNull()){
				return false;
			}
		}
		return true;
	}
}
