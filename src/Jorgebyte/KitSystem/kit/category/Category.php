<?php

declare(strict_types=1);

/*
 *    KitSystem
 *    Api: 5.3.0
 *    Version: 1.0.0
 *    Author: Jorgebyte
 */

namespace Jorgebyte\KitSystem\kit\category;

use Jorgebyte\KitSystem\kit\Kit;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;

class Category
{
    private string $name;
    private string $prefix;
    private ?string $permission;
    private ?string $icon;
    private array $kits = [];

    public function __construct(string $name, string $prefix, ?string $permission = null, ?string $icon = null)
    {
        $this->name = $name;
        $this->prefix = $prefix;
        $this->permission = $permission;
        $this->icon = $icon;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getPermission(): ?string
    {
        return $this->permission;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getKit(string $kitName): ?Kit
    {
        return $this->kits[$kitName] ?? null;
    }

    public function getKits(): array
    {
        return array_values($this->kits);
    }

    public function addKit(Kit $kit): void
    {
        $this->kits[$kit->getName()] = $kit;
    }

    public function removeKit(Kit $kit): void
    {
        unset($this->kits[$kit->getName()]);
    }

    public function hasKit(string $kitName): bool
    {
        return isset($this->kits[$kitName]);
    }

    public function canUseCategory(Player $player): bool
    {
        return $this->permission === null ||
            $player->hasPermission($this->permission) ||
            $player->hasPermission(DefaultPermissions::ROOT_OPERATOR);
    }
}
