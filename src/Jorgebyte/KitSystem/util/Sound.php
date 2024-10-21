<?php

declare(strict_types=1);

/*
 *    KitSystem
 *    Api: 5.3.0
 *    Version: 1.0.0
 *    Author: Jorgebyte
 */

namespace Jorgebyte\KitSystem\util;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;

class Sound
{
    public static function addSound(Player $player, string $soundName, float $volume = 1.0, float $pitch = 1.0): void
    {
        $player->getNetworkSession()->sendDataPacket(
            self::createPacket($player->getPosition(), $soundName, $volume, $pitch)
        );
    }

    private static function createPacket(Vector3 $vec, string $soundName, float $volume = 1.0, float $pitch = 1.0): PlaySoundPacket
    {
        return PlaySoundPacket::create($soundName, $vec->x, $vec->y, $vec->z, $volume, $pitch);
    }
}
