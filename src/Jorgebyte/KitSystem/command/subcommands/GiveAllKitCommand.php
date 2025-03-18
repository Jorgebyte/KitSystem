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

namespace Jorgebyte\KitSystem\command\subcommands;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use Jorgebyte\KitSystem\form\FormManager;
use Jorgebyte\KitSystem\form\FormTypes;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class GiveAllKitCommand extends BaseSubCommand{
	public function __construct(){
		parent::__construct("giveall", "KitSystem - Give a kit to all online users");
		$this->setPermission("kitsystem.command.giveall");
	}

	protected function prepare() : void{
		$this->addConstraint(new InGameRequiredConstraint($this));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		/** @var Player $sender */
		FormManager::sendForm($sender, FormTypes::GIVEKITALL->value);
	}
}
