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

namespace Jorgebyte\KitSystem\menu\types;

use Jorgebyte\KitSystem\Main;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use function in_array;

/**
 * InvMenu that allows the player to preview the contents of a kit.
 */
final class PreviewKitMenu extends InvMenu{

	protected string $kitName;

	public function __construct(string $kitName){
		$this->kitName = $kitName;
		parent::__construct(InvMenuHandler::getTypeRegistry()->get(InvMenuTypeIds::TYPE_DOUBLE_CHEST));
		$this->setName("Previewing Kit: " . $kitName);

		$kit = Main::getInstance()->getKitManager()->getKit($this->kitName);
		if($kit === null)return;

		$inventory = $this->getInventory();
		$redGlass = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::RED())->asItem()->setCustomName("");

		// Fill kit items
		foreach($kit->getItems() as $slot => $item){
			$inventory->setItem($slot, $item);
		}

		// Fill armor
		$armorSlots = [47, 48, 49, 50];
		foreach($kit->getArmor() as $i => $armorItem){
			if(isset($armorSlots[$i])){
				$inventory->setItem($armorSlots[$i], $armorItem);
			}
		}

		// Block confirm slot
		$inventory->setItem(40, clone $redGlass);

		// Fill borders
		for($i = 36; $i < 54; $i++){
			if(!in_array($i, [40, ...$armorSlots], true) && $inventory->getItem($i)->isNull()){
				$inventory->setItem($i, clone $redGlass);
			}
		}

		// Read-only
		$this->setListener(fn(InvMenuTransaction $t) => $t->discard());
	}
}
