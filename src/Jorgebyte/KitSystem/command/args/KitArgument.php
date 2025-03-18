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

final class KitArgument extends StringEnumArgument{
	public function getTypeName() : string{
		return "kit";
	}

	public function canParse(string $testString, CommandSender $sender) : bool{
		return $this->getValue($testString) instanceof Kit;
	}

	public function parse(string $argument, CommandSender $sender) : ?Kit{
		return $this->getValue($argument);
	}

	public function getValue(string $string) : ?Kit{
		return Main::getInstance()->getKitManager()->getKit($string);
	}

	public function getEnumValues() : array{
		return array_map(fn (Kit $kit) => $kit->getName(), Main::getInstance()->getKitManager()->getAllKits());
	}
}
