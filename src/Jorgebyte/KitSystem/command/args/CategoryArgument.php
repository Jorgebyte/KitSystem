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

namespace Jorgebyte\KitSystem\command\args;

use CortexPE\Commando\args\StringEnumArgument;
use Jorgebyte\KitSystem\kit\category\Category;
use Jorgebyte\KitSystem\Main;
use pocketmine\command\CommandSender;
use function array_map;

/**
 * Argument parser for category names in command-line usage.
 *
 * Used to resolve a category name string into a `Category` object
 * for use in command execution. This enables auto-completion, validation,
 * and clean parsing of category references.
 *
 * Example usage:
 *   /command <category>
 */
final class CategoryArgument extends StringEnumArgument{

	/**
	 * Returns the name of the argument type used in command usage display.
	 */
	public function getTypeName() : string{
		return "category";
	}

	/**
	 * Checks whether the provided string matches a valid category.
	 *
	 * @param string        $testString The string to validate.
	 * @param CommandSender $sender     The sender of the command.
	 * @return bool True if the string is a valid category name.
	 */
	public function canParse(string $testString, CommandSender $sender) : bool{
		return $this->getValue($testString) instanceof Category;
	}

	/**
	 * Parses the input argument string into a `Category` object.
	 *
	 * @param string        $argument The argument to parse.
	 * @param CommandSender $sender   The command sender (unused).
	 * @return Category|null The matching `Category`, or null if not found.
	 */
	public function parse(string $argument, CommandSender $sender) : ?Category{
		return $this->getValue($argument);
	}

	/**
	 * Fetches the `Category` instance by name.
	 *
	 * @param string $string The name of the category.
	 * @return Category|null The corresponding category, or null if not found.
	 */
	public function getValue(string $string) : ?Category{
		return Main::getInstance()->getCategoryManager()->getCategory($string);
	}

	/**
	 * Provides a list of all valid category names for auto-completion.
	 *
	 * @return string[]
	 */
	public function getEnumValues() : array{
		return array_map(
			fn (Category $category) => $category->getName(),
			Main::getInstance()->getCategoryManager()->getAllCategories()
		);
	}
}
