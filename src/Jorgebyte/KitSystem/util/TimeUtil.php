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

namespace Jorgebyte\KitSystem\util;

use function floor;
use function max;
use function time;

class TimeUtil{
	public static function formatTime(int $seconds) : string{
		if($seconds < 60){
			return $seconds . "s";
		} elseif($seconds < 3600){
			$minutes = floor($seconds / 60);
			$remainingSeconds = $seconds % 60;
			return $minutes . "m " . $remainingSeconds . "s";
		} else{
			$hours = floor($seconds / 3600);
			$remainingMinutes = floor(($seconds % 3600) / 60);
			return $hours . "h " . $remainingMinutes . "m";
		}
	}

	public static function formatCooldown(int $expiryTime) : string{
		$remainingTime = $expiryTime - time();
		return self::formatTime(max($remainingTime, 0));
	}
}
