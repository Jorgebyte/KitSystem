<?php

declare(strict_types=1);

/*
 *    KitSystem
 *    Api: 5.3.0
 *    Version: 1.0.0
 *    Author: Jorgebyte
 */

namespace Jorgebyte\KitSystem\command\subcommands;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use Jorgebyte\KitSystem\form\FormManager;
use Jorgebyte\KitSystem\form\FormTypes;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class CreateCategoryCommand extends BaseSubCommand
{
    public function __construct()
    {
        parent::__construct("createcategory", "KitSystem - Create a new category");
        $this->setPermission("kitsystem.command.createcategory");
    }

    protected function prepare(): void
    {
        $this->addConstraint(new InGameRequiredConstraint($this));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        /** @var Player $sender */
        FormManager::sendForm($sender, FormTypes::CREATE_CATEGORY->value);
    }
}
