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
use Jorgebyte\KitSystem\kit\Kit;
use Jorgebyte\KitSystem\Main;
use pocketmine\command\CommandSender;
use function array_map;

/**
 * Argument parser for kit names in command-line usage.
 *
 * Used to resolve a kit name string into a `Kit` object
 * for use in command execution. Provides auto-completion,
 * validation, and a strongly-typed parsing layer.
 *
 * Example usage:
 *   /kit give <kit>
 */
final class KitArgument extends StringEnumArgument{

	/**
	 * Returns the type name shown in command usage.
	 */
	public function getTypeName() : string{
		return "kit";
	}

	/**
	 * Checks if a given input string can be parsed into a Kit.
	 *
	 * @param string        $testString The string to validate.
	 * @param CommandSender $sender     The command sender (unused).
	 * @return bool True if a matching Kit is found.
	 */
	public function canParse(string $testString, CommandSender $sender) : bool{
		return $this->getValue($testString) instanceof Kit;
	}

	/**
	 * Parses a kit name string into a `Kit` object.
	 *
	 * @param string        $argument The string to parse.
	 * @param CommandSender $sender   The command sender (unused).
	 * @return Kit|null The Kit instance or null if not found.
	 */
	public function parse(string $argument, CommandSender $sender) : ?Kit{
		return $this->getValue($argument);
	}

	/**
	 * Retrieves a Kit object by name.
	 *
	 * @param string $string The kit name.
	 * @return Kit|null The corresponding Kit or null if not found.
	 */
	public function getValue(string $string) : ?Kit{
		return Main::getInstance()->getKitManager()->getKit($string);
	}

	/**
	 * Returns a list of valid kit names for auto-completion.
	 *
	 * @return string[]
	 */
	public function getEnumValues() : array{
		return array_map(fn (Kit $kit) => $kit->getName(), Main::getInstance()->getKitManager()->getAllKits());
	}
}
