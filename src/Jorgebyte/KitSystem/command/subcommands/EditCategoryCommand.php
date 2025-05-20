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
use Jorgebyte\KitSystem\command\args\CategoryArgument;
use Jorgebyte\KitSystem\form\FormManager;
use Jorgebyte\KitSystem\form\FormTypes;
use Jorgebyte\KitSystem\kit\category\Category;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class EditCategoryCommand extends BaseSubCommand{
	public function __construct(){
		parent::__construct("editcategory", "KitSystem - Edit a category");
		$this->setPermission("kitsystem.command.editcategory");
	}

	protected function prepare() : void{
		$this->registerArgument(0, new CategoryArgument("category", true));
		$this->addConstraint(new InGameRequiredConstraint($this));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		/** @var Player $sender */
		if(isset($args["category"])){
			/** @var Category $category */
			$category = $args["category"];
			FormManager::sendForm($sender, FormTypes::EDIT_CATEGORY_FORM->value, [$sender, $category->getName()]);
			return;
		}
		FormManager::sendForm($sender, FormTypes::SELECT_CATEGORY->value, [$sender, "editcategory"]);
	}
}
