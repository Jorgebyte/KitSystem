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
use Jorgebyte\KitSystem\command\args\KitArgument;
use Jorgebyte\KitSystem\form\FormManager;
use Jorgebyte\KitSystem\form\FormTypes;
use Jorgebyte\KitSystem\kit\Kit;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class DeleteKitCommand extends BaseSubCommand{
	public function __construct(){
		parent::__construct("delete", "KitSystem - delete a kit");
		$this->setPermission("kitsystem.command.delete");
	}

	protected function prepare() : void{
		$this->registerArgument(0, new KitArgument("kit", true));
		$this->addConstraint(new InGameRequiredConstraint($this));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		/** @var Player $sender */
		if(isset($args["kit"])){
			/** @var Kit $kit */
			$kit = $args["kit"];
			FormManager::sendForm($sender, FormTypes::DELETE_KIT_SUBFORM->value, [$kit->getName()]);
			return;
		}
		FormManager::sendForm($sender, FormTypes::SELECT_KIT->value, ["deletekit"]);
	}
}
