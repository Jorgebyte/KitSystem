<?php

declare(strict_types=1);

/*
 *    KitSystem
 *    Api: 5.3.0
 *    Version: 1.0.0
 *    Author: Jorgebyte
 */

namespace Jorgebyte\KitSystem\util;

class TimeUtil
{
    public static function formatTime(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds . "s";
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            $remainingSeconds = $seconds % 60;
            return $minutes . "m " . $remainingSeconds . "s";
        } else {
            $hours = floor($seconds / 3600);
            $remainingMinutes = floor(($seconds % 3600) / 60);
            return $hours . "h " . $remainingMinutes . "m";
        }
    }

    public static function formatCooldown(int $expiryTime): string
    {
        $remainingTime = $expiryTime - time();
        return self::formatTime(max($remainingTime, 0));
    }
}
