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

namespace Jorgebyte\KitSystem\command;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use Jorgebyte\KitSystem\command\subcommands\CreateCategoryCommand;
use Jorgebyte\KitSystem\command\subcommands\CreateKitCommand;
use Jorgebyte\KitSystem\command\subcommands\DeleteCategoryCommand;
use Jorgebyte\KitSystem\command\subcommands\DeleteKitCommand;
use Jorgebyte\KitSystem\command\subcommands\EditCategoryCommand;
use Jorgebyte\KitSystem\command\subcommands\EditKitCommand;
use Jorgebyte\KitSystem\command\subcommands\GiveAllKitCommand;
use Jorgebyte\KitSystem\command\subcommands\GiveKitCommand;
use Jorgebyte\KitSystem\command\subcommands\PreviewKitCommand;
use Jorgebyte\KitSystem\form\FormManager;
use Jorgebyte\KitSystem\form\FormTypes;
use Jorgebyte\KitSystem\Main;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

/**
 * Root command handler for KitSystem.
 * Registers all subcommands and opens the main kit UI by default.
 */
class KitSystemCommand extends BaseCommand{
	protected Main $plugin;

	public function __construct(Main $plugin){
		$this->plugin = $plugin;
		parent::__construct($this->plugin, "kitsystem", "KitSystem Command", ["kit", "ekit"]);
		$this->setPermission("kitsystem.command");
	}

	/**
	 * Registers all available subcommands.
	 */
	protected function prepare() : void{
		$this->addConstraint(new InGameRequiredConstraint($this));
		// Kit commands
		$this->registerSubCommand(new CreateKitCommand());
		$this->registerSubCommand(new DeleteKitCommand());
		$this->registerSubCommand(new EditKitCommand());
		$this->registerSubCommand(new GiveKitCommand());
		$this->registerSubCommand(new GiveAllKitCommand());
		$this->registerSubCommand(new PreviewKitCommand());

		// Category commands
		$this->registerSubCommand(new CreateCategoryCommand());
		$this->registerSubCommand(new DeleteCategoryCommand());
		$this->registerSubCommand(new EditCategoryCommand());
	}

	/**
	 * Executes the command and opens the main kits form for the player.
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		/** @var Player $sender */
		FormManager::sendForm($sender, FormTypes::KITS->value, [$sender]);
	}
}
