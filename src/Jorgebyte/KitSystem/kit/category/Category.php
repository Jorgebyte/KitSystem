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

namespace Jorgebyte\KitSystem\kit\category;

use Jorgebyte\KitSystem\kit\Kit;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use function array_values;

final class Category{
	/** @var array<string, Kit> */
	private array $kits = [];

	public function __construct(
		private readonly string $name,
		private string          $prefix,
		private ?string         $permission = null,
		private ?string         $icon = null
	){
	}

	public function getName() : string{
		return $this->name;
	}

	public function getPrefix() : string{
		return $this->prefix;
	}

	public function getPermission() : ?string{
		return $this->permission;
	}

	public function getIcon() : ?string{
		return $this->icon;
	}

	public function getKits() : array{
		return array_values($this->kits);
	}

	public function setPrefix(string $prefix) : void{
		$this->prefix = $prefix;
	}

	public function setPermission(?string $permission) : void{
		$this->permission = $permission;
	}

	public function setIcon(?string $icon) : void{
		$this->icon = $icon;
	}

	public function addKit(Kit $kit) : void{
		$this->kits[$kit->getName()] = $kit;
	}

	public function removeKit(string $kitName) : void{
		unset($this->kits[$kitName]);
	}

	public function hasKit(string $kitName) : bool{
		return isset($this->kits[$kitName]);
	}

	public function canUseCategory(Player $player) : bool{
		return $this->permission === null ||
			$player->hasPermission($this->permission) ||
			$player->hasPermission(DefaultPermissions::ROOT_OPERATOR);
	}
}
