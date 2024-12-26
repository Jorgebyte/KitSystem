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

namespace Jorgebyte\KitSystem\menu\types;

use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\util\Sound;
use Jorgebyte\KitSystem\util\SoundNames;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;
use function in_array;

class EditKitMenu extends InvMenu{
	protected string $kitName;

	public function __construct(string $kitName){
		$this->kitName = $kitName;
		parent::__construct(InvMenuHandler::getTypeRegistry()->get(InvMenuTypeIds::TYPE_DOUBLE_CHEST));
		$this->setName("Editing Kit: " . $kitName);

		$kit = Main::getInstance()->getKitManager()->getKit($this->kitName);
		if($kit === null){
			return;
		}

		$inventory = $this->getInventory();
		$redGlass = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::RED())->asItem()->setCustomName("");

		foreach($kit->getItems() as $slot => $item){
			$inventory->setItem($slot, $item);
		}

		for($i = 37; $i <= 40; $i++){
			$inventory->setItem($i, $redGlass);
		}

		$armorItems = $kit->getArmor();
		$armorSlots = [47, 48, 49, 50];
		foreach($armorItems as $i => $armorItem){
			if(isset($armorSlots[$i])){
				$inventory->setItem($armorSlots[$i], $armorItem);
			}
		}

		$confirmBlock = VanillaBlocks::EMERALD()->asItem()->setCustomName(TextFormat::GREEN . "UPDATE");
		$inventory->setItem(40, $confirmBlock);

		for($i = 36; $i < 54; $i++){
			if(!in_array($i, [40, 47, 48, 49, 50], true) && $inventory->getItem($i)->isNull()){
				$inventory->setItem($i, $redGlass);
			}
		}

		$this->setListener(function (InvMenuTransaction $transaction) : InvMenuTransactionResult{
			$player = $transaction->getPlayer();
			$clickedItem = $transaction->getItemClicked();

			$redGlass = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::RED())->asItem();
			if($clickedItem->equals($redGlass)){
				Sound::addSound($player, SoundNames::BAD_TONE->value);
				return $transaction->discard();
			}

			if($clickedItem->equals(VanillaBlocks::EMERALD()->asItem()->setCustomName(TextFormat::GREEN . "UPDATE"))){
				$inventory = $transaction->getAction()->getInventory();
				$newItems = [];
				for($i = 0; $i < 36; $i++){
					$item = $inventory->getItem($i);
					if(!$item->equals(VanillaItems::AIR())){
						$newItems[$i] = $item;
					}
				}

				$newArmor = [];
				for($i = 47; $i <= 50; $i++){
					$armorItem = $inventory->getItem($i);
					if(!$armorItem->equals(VanillaItems::AIR())){
						$newArmor[] = $armorItem;
					}
				}
				$kit = Main::getInstance()->getKitManager()->getKit($this->kitName);
				if($kit !== null){
					$kit->setItems($newItems);
					$kit->setArmor($newArmor);
					Main::getInstance()->getKitManager()->saveKit($kit);
					$player->sendMessage(TextFormat::GREEN . "Kit updated successfully!!!");
					Sound::addSound($player, SoundNames::GOOD_TONE->value);
					$this->onClose($player);
				}
				return $transaction->discard();
			}
			return $transaction->continue();
		});
	}
}
