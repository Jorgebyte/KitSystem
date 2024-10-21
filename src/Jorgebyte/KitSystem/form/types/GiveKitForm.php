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
use Jorgebyte\KitSystem\util\PlayerUtil;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class GiveKitForm extends CustomForm
{
    public function __construct()
    {
        parent::__construct("KitSystem - Give a Kit");
    }

    protected function onCreation(): void
    {
        $onlinePlayers = Server::getInstance()->getOnlinePlayers();
        $dropdownPlayers = new Dropdown("Select a Player");

        foreach ($onlinePlayers as $player) {
            $dropdownPlayers->addOption(new Option($player->getName(), $player->getName()));
        }

        $this->addElement("selectedPlayer", $dropdownPlayers);

        $kitManager = Main::getInstance()->getKitManager();
        $kits = $kitManager->getAllKits();
        $dropdownKits = new Dropdown("Select a Kit");

        foreach ($kits as $kit) {
            $dropdownKits->addOption(new Option($kit->getName(), $kit->getName()));
        }

        $this->addElement("selectedKit", $dropdownKits);
        $this->addElement("kitQuantity", new Slider("How many kits?", 1, 64, 1, 1));
    }

    protected function onSubmit(Player $player, FormResponse $response): void
    {
        $selectedPlayerName = $response->getDropdownSubmittedOptionId("selectedPlayer");
        // Player selected by the form \\
        $targetPlayer = Server::getInstance()->getPlayerExact($selectedPlayerName);

        if ($targetPlayer === null) {
            $player->sendMessage(TextFormat::RED . "ERROR: The selected player is no longer online.");
            return;
        }

        $selectedKitName = $response->getDropdownSubmittedOptionId("selectedKit");
        $kitManager = Main::getInstance()->getKitManager();
        $kit = $kitManager->getKit($selectedKitName);

        if ($kit === null) {
            $player->sendMessage(TextFormat::RED . "ERROR: The selected kit does not exist.");
            return;
        }

        $quantity = (int) $response->getSliderSubmittedStep("kitQuantity");

        if (!$kit->shouldStoreInChest() && !PlayerUtil::hasEnoughSpace($targetPlayer, $kit)) {
            $player->sendMessage(TextFormat::RED . "ERROR: The player does not have enough space in the inventory.");
            return;
        }

        for ($i = 0; $i < $quantity; $i++) {
            if ($kit->shouldStoreInChest()) {
                $kitManager->giveKitChest($targetPlayer, $kit);
            } else {
                $kitManager->giveKitItems($targetPlayer, $kit);
            }
        }
        $player->sendMessage(TextFormat::GREEN . "Successfully gave " . $quantity . " kit(s) to " . $targetPlayer->getName());
    }
}
