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

namespace Jorgebyte\KitSystem\menu;

use InvalidArgumentException;
use Jorgebyte\KitSystem\menu\types\EditKitMenu;
use Jorgebyte\KitSystem\menu\types\PreviewKitMenu;
use Jorgebyte\KitSystem\util\Sound;
use Jorgebyte\KitSystem\util\SoundNames;
use muqsit\invmenu\InvMenu;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use Throwable;
use function is_subclass_of;

class MenuManager{
	private static array $menuMap = [
		MenuTypes::EDIT_KIT->value => EditKitMenu::class,
		MenuTypes::PREVIEW_KIT->value => PreviewKitMenu::class,
	];

	private static function sendMenuWithSound(Player $player, InvMenu $menu) : void{
		Sound::addSound($player, SoundNames::OPEN_MENU->value);
		$menu->send($player);
	}

	public static function sendMenu(Player $player, string $menuType, array $args = []) : void{
		if(!isset(self::$menuMap[$menuType])){
			throw new InvalidArgumentException("ERROR: Menu type " . $menuType . " is not recognized");
		}

		$menuClass = self::$menuMap[$menuType];
		if(!is_subclass_of($menuClass, InvMenu::class)){
			throw new InvalidArgumentException("ERROR: The class " . $menuClass . " is not a valid menu type");
		}

		try{
			/** @var InvMenu $menu */
			$menu = new $menuClass(...$args);
			self::sendMenuWithSound($player, $menu);
		} catch(Throwable $e){
			$player->sendMessage(TextFormat::RED . "ERROR: creating menu: " . $e->getMessage());
			Server::getInstance()->getLogger()->error($e->getMessage());
		}
	}
}
