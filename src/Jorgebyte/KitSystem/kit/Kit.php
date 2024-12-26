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

namespace Jorgebyte\KitSystem\kit;

use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;

class Kit{
	private string $name;
	private string $prefix;
	private array $armor;
	private array $items;
	private ?int $cooldown;
	private ?float $price;
	private ?string $permission;
	private ?string $icon;
	private bool $storeInChest;

	public function __construct(
		string $name,
		string $prefix,
		array $armor,
		array $items,
		?int $cooldown = null,
		?float $price = null,
		?string $permission = null,
		?string $icon = null,
		bool $storeInChest = true,
	){
		$this->name = $name;
		$this->prefix = $prefix;
		$this->armor = $armor;
		$this->items = $items;
		$this->cooldown = $cooldown;
		$this->price = $price;
		$this->permission = $permission;
		$this->icon = $icon;
		$this->storeInChest = $storeInChest;
	}

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

	public function setName(string $name) : void{
		$this->name = $name;
	}

	public function setPrefix(string $prefix) : void{
		$this->prefix = $prefix;
	}

	public function setItems(array $items) : void{
		$this->items = $items;
	}

	public function setArmor(array $armor) : void{
		$this->armor = $armor;
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

	public function shouldStoreInChest() : bool{
		return $this->storeInChest;
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
