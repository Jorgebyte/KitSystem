<?php

/*
 *   -- KitSystem --
 *
 *   Author: Jorgebyte
 *   Discord Contact: jorgess__
 *
 *  https://github.com/Jorgebyte/KitSystem
 */

declare(strict_types=1);

namespace Jorgebyte\KitSystem\command\args;

use CortexPE\Commando\args\StringEnumArgument;
use Jorgebyte\KitSystem\kit\category\Category;
use Jorgebyte\KitSystem\Main;
use pocketmine\command\CommandSender;
use function array_map;

final class CategoryArgument extends StringEnumArgument{
	public function getTypeName() : string{
		return "category";
	}

	public function canParse(string $testString, CommandSender $sender) : bool{
		return $this->getValue($testString) instanceof Category;
	}

	public function parse(string $argument, CommandSender $sender) : ?Category{
		return $this->getValue($argument);
	}

	public function getValue(string $string) : ?Category{
		return Main::getInstance()->getCategoryManager()->getCategory($string);
	}

	public function getEnumValues() : array{
		return array_map(
			fn (Category $category) => $category->getName(),
			Main::getInstance()->getCategoryManager()->getAllCategories()
		);
	}
}
