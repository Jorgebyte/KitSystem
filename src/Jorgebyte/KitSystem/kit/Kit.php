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

namespace Jorgebyte\KitSystem\kit;

use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;

final class Kit{

	public function __construct(
		private string $name,
		private string $prefix,
		private array $armor,
		private array $items,
		private ?int $cooldown = null,
		private ?float $price = null,
		private ?string $permission = null,
		private ?string $icon = null,
		private bool $storeInChest = true
	){}

	public function getName() : string{
		return $this->name;
	}

	public function getPrefix() : string{
		return $this->prefix;
	}

	public function getArmor() : array{
		return $this->armor;
	}

	public function getItems() : array{
		return $this->items;
	}

	public function getCooldown() : ?int{
		return $this->cooldown;
	}

	public function getPrice() : ?float{
		return $this->price;
	}

	public function getPermission() : ?string{
		return $this->permission;
	}

	public function getIcon() : ?string{
		return $this->icon;
	}

	public function shouldStoreInChest() : bool{
		return $this->storeInChest;
	}

	public function setName(string $name) : void{
		$this->name = $name;
	}

	public function setPrefix(string $prefix) : void{
		$this->prefix = $prefix;
	}

	public function setArmor(array $armor) : void{
		$this->armor = $armor;
	}

	public function setItems(array $items) : void{
		$this->items = $items;
	}

	public function setCooldown(?int $cooldown) : void{
		$this->cooldown = $cooldown;
	}

	public function setPrice(?float $price) : void{
		$this->price = $price;
	}

	public function setPermission(?string $permission) : void{
		$this->permission = $permission;
	}

	public function setIcon(?string $icon) : void{
		$this->icon = $icon;
	}

	public function setStoreInChest(bool $storeInChest) : void{
		$this->storeInChest = $storeInChest;
	}

	public function canUseKit(Player $player) : bool{
		return $this->permission === null ||
			$player->hasPermission($this->permission) ||
			$player->hasPermission(DefaultPermissions::ROOT_OPERATOR);
	}
}
