<?php

declare(strict_types=1);

/*
 *    KitSystem
 *    Api: 5.3.0
 *    Version: 1.0.0
 *    Author: Jorgebyte
 */

namespace Jorgebyte\KitSystem\form\types;

use EasyUI\element\Dropdown;
use EasyUI\element\Option;
use EasyUI\element\Slider;
use EasyUI\utils\FormResponse;
use EasyUI\variant\CustomForm;
use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\message\MessageKey;
use Jorgebyte\KitSystem\util\PlayerUtil;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class GiveKitAllForm extends CustomForm
{
    public function __construct()
    {
        parent::__construct("KitSystem - Give a Kit to All Players");
    }

    protected function onCreation(): void
    {
        $kitManager = Main::getInstance()->getKitManager();
        $kits = $kitManager->getAllKits();
        $dropdownKits = new Dropdown("Select a Kit");

        foreach ($kits as $kit) {
            $dropdownKits->addOption(new Option($kit->getName(), $kit->getName()));
        }

        $this->addElement("selectedKit", $dropdownKits);
        $this->addElement("kitQuantity", new Slider("How many kits per player?", 1, 64, 1, 1));
    }

    protected function onSubmit(Player $player, FormResponse $response): void
    {
        $selectedKitName = $response->getDropdownSubmittedOptionId("selectedKit");
        $kitManager = Main::getInstance()->getKitManager();
        $kit = $kitManager->getKit($selectedKitName);

        if ($kit === null) {
            $player->sendMessage(TextFormat::RED . "ERROR: The selected kit does not exist.");
            return;
        }

        $quantity = (int) $response->getSliderSubmittedStep("kitQuantity");
        $onlinePlayers = Server::getInstance()->getOnlinePlayers();

        foreach ($onlinePlayers as $targetPlayer) {
            if (!$kit->shouldStoreInChest() && !PlayerUtil::hasEnoughSpace($targetPlayer, $kit)) {
                $player->sendMessage(TextFormat::RED . "ERROR: " . $targetPlayer->getName() . " does not have enough space in their inventory.");
                continue;
            }

            for ($i = 0; $i < $quantity; $i++) {
                if ($kit->shouldStoreInChest()) {
                    $kitManager->giveKitChest($targetPlayer, $kit);
                } else {
                    $kitManager->giveKitItems($targetPlayer, $kit);
                }
            }
        }
        Server::getInstance()->broadcastMessage(Main::getInstance()->getMessage()->getMessage(
            MessageKey::GIVEALL_KIT_BROADCAST,
            [
                "player" => $player->getName(),
                "quantity" => strval($quantity),
                "kit" => $kit->getName()
            ]
        ));

        $player->sendMessage(TextFormat::GREEN . "Successfully gave " . $quantity . " kit(s) to all online players!");
    }
}
