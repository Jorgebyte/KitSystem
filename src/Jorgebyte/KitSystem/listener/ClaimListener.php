<?php

declare(strict_types=1);

/*
 *    KitSystem
 *    Api: 5.3.0
 *    Version: 1.0.0
 *    Author: Jorgebyte
 */

namespace Jorgebyte\KitSystem\listener;

use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\message\MessageKey;
use Jorgebyte\KitSystem\util\PlayerUtil;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;

class ClaimListener implements Listener
{
    public function onPlayerInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        $message = Main::getInstance()->getMessage();
        $item = $player->getInventory()->getItemInHand();

        if ($item->getNamedTag()->getTag("kitName") !== null) {
            $kitName = $item->getNamedTag()->getString("kitName");

            $kitManager = Main::getInstance()->getKitManager();
            $kit = $kitManager->getKit($kitName);

            if ($kit !== null) {
                $event->cancel();
                if (!PlayerUtil::hasEnoughSpace($player, $kit)) {
                    $player->sendMessage($message->getMessage(MessageKey::FULL_INV));
                    return;
                }
                $kitManager->giveKitItems($player, $kit);
                $player->getInventory()->removeItem($item->setCount(1));
                $player->sendMessage($message->getMessage(MessageKey::OPEN_KIT, ["kitname" => $kitName]));
            }
        }
    }
}
