<?php

declare(strict_types=1);

/*
 *    KitSystem
 *    Api: 5.3.0
 *    Version: 1.0.0
 *    Author: Jorgebyte
 */

namespace Jorgebyte\KitSystem\menu\types;

use Jorgebyte\KitSystem\Main;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;

class PreviewKitMenu extends InvMenu
{
    protected string $kitName;

    public function __construct(string $kitName)
    {
        $this->kitName = $kitName;
        parent::__construct(InvMenuHandler::getTypeRegistry()->get(InvMenuTypeIds::TYPE_DOUBLE_CHEST));
        $this->setName("Previewing Kit: " . $kitName);

        $kit = Main::getInstance()->getKitManager()->getKit($this->kitName);
        if ($kit === null) {
            return;
        }

        $inventory = $this->getInventory();
        $redGlass = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::RED())->asItem()->setCustomName("");

        foreach ($kit->getItems() as $slot => $item) {
            $inventory->setItem($slot, $item);
        }

        for ($i = 37; $i <= 40; $i++) {
            $inventory->setItem($i, $redGlass);
        }

        $armorItems = $kit->getArmor();
        $armorSlots = [47, 48, 49, 50];
        foreach ($armorItems as $i => $armorItem) {
            if (isset($armorSlots[$i])) {
                $inventory->setItem($armorSlots[$i], $armorItem);
            }
        }

        $inventory->setItem(40, $redGlass);

        for ($i = 36; $i < 54; $i++) {
            if (!in_array($i, [40, 47, 48, 49, 50], true) && $inventory->getItem($i)->isNull()) {
                $inventory->setItem($i, $redGlass);
            }
        }

        $this->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            return $transaction->discard();
        });
    }
}
