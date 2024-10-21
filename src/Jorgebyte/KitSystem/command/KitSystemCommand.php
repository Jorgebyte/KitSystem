<?php

declare(strict_types=1);

/*
 *    KitSystem
 *    Api: 5.3.0
 *    Version: 1.0.0
 *    Author: Jorgebyte
 */

namespace Jorgebyte\KitSystem\command;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use Jorgebyte\KitSystem\command\subcommands\{CreateCategoryCommand,
    CreateKitCommand,
    DeleteCategoryCommand,
    DeleteKitCommand,
    GiveAllKitCommand,
    GiveKitCommand};
use Jorgebyte\KitSystem\form\FormManager;
use Jorgebyte\KitSystem\form\FormTypes;
use Jorgebyte\KitSystem\Main;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class KitSystemCommand extends BaseCommand
{
    protected Main $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct($this->plugin, "kitsystem", "KitSystem Command", ["kit", "ekit"]);
        $this->setPermission("kitsystem.command");
    }

    protected function prepare(): void
    {
        $this->addConstraint(new InGameRequiredConstraint($this));
        $this->registerSubCommand(new CreateKitCommand());
        $this->registerSubCommand(new DeleteKitCommand());
        $this->registerSubCommand(new GiveKitCommand());
        $this->registerSubCommand(new GiveAllKitCommand());

        $this->registerSubCommand(new CreateCategoryCommand());
        $this->registerSubCommand(new DeleteCategoryCommand());
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        /** @var Player $sender */
        FormManager::sendForm($sender, FormTypes::KITS->value, [$sender]);
    }
}
