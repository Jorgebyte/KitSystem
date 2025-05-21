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
use Jorgebyte\KitSystem\util\LangKey;
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

/**
 * InvMenu used to edit a kit's contents (inventory and armor).
 */
final class EditKitMenu extends InvMenu{

	protected string $kitName;

	public function __construct(string $kitName){
		$this->kitName = $kitName;
		parent::__construct(InvMenuHandler::getTypeRegistry()->get(InvMenuTypeIds::TYPE_DOUBLE_CHEST));
		$this->setName("Editing Kit: " . $kitName);

		$kit = Main::getInstance()->getKitManager()->getKit($this->kitName);
		if($kit === null)return;

		$inventory = $this->getInventory();
		$redGlass = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::RED())->asItem()->setCustomName("");

		// Fill kit items
		foreach($kit->getItems() as $slot => $item){
			$inventory->setItem($slot, $item);
		}

		// Fill armor slots
		$armorSlots = [47, 48, 49, 50];
		foreach($kit->getArmor() as $i => $armorItem){
			if(isset($armorSlots[$i])){
				$inventory->setItem($armorSlots[$i], $armorItem);
			}
		}

		// Confirm action
		$confirm = VanillaBlocks::EMERALD()->asItem()->setCustomName(TextFormat::GREEN . "UPDATE");
		$inventory->setItem(40, $confirm);

		// Fill borders
		for($i = 36; $i < 54; $i++){
			if(!in_array($i, [40, ...$armorSlots], true) && $inventory->getItem($i)->isNull()){
				$inventory->setItem($i, clone $redGlass);
			}
		}

		$this->setListener(function (InvMenuTransaction $transaction) : InvMenuTransactionResult{
			$player = $transaction->getPlayer();
			$item = $transaction->getItemClicked();
			$inventory = $transaction->getAction()->getInventory();

			$redGlass = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::RED())->asItem();
			$confirm = VanillaBlocks::EMERALD()->asItem()->setCustomName(TextFormat::GREEN . "UPDATE");

			if($item->equals($redGlass)){
				Sound::addSound($player, SoundNames::BAD_TONE->value);
				return $transaction->discard();
			}

			if($item->equals($confirm)){
				$newItems = [];
				for($i = 0; $i < 36; $i++){
					$currentItem = $inventory->getItem($i);
					if(!$currentItem->equals(VanillaItems::AIR())){
						$newItems[$i] = $currentItem;
					}
				}

				$newArmor = [];
				for($i = 47; $i <= 50; $i++){
					$currentItem = $inventory->getItem($i);
					if(!$currentItem->equals(VanillaItems::AIR())){
						$newArmor[] = $currentItem;
					}
				}

				$kit = Main::getInstance()->getKitManager()->getKit($this->kitName);
				if($kit !== null){
					$kit->setItems($newItems);
					$kit->setArmor($newArmor);
					Main::getInstance()->getKitManager()->saveKit($kit);

					$player->sendMessage(Main::getInstance()->getTranslator()->translate($player, LangKey::KIT_UPDATE->value));
					Sound::addSound($player, SoundNames::GOOD_TONE->value);
					$this->onClose($player);
				}
				return $transaction->discard();
			}

			return $transaction->continue();
		});
	}
}
