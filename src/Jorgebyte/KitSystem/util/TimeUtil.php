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

namespace Jorgebyte\KitSystem\util;

use function intdiv;
use function max;
use function time;

/**
 * Utility class for time formatting and cooldown handling.
 */
final class TimeUtil{

	/**
	 * Converts a duration in seconds into a human-readable format.
	 * Example outputs: "45s", "2m 30s", "1h 15m"
	 */
	public static function formatTime(int $seconds) : string{
		if($seconds < 60){
			return "{$seconds}s";
		} elseif($seconds < 3600){
			$minutes = intdiv($seconds, 60);
			$remainingSeconds = $seconds % 60;
			return "{$minutes}m {$remainingSeconds}s";
		} else{
			$hours = intdiv($seconds, 3600);
			$remainingMinutes = intdiv($seconds % 3600, 60);
			return "{$hours}h {$remainingMinutes}m";
		}
	}

	/**
	 * Returns a formatted string representing the remaining time until the given expiry timestamp.
	 */
	public static function formatCooldown(int $expiryTime) : string{
		$remainingTime = $expiryTime - time();
		return self::formatTime(max($remainingTime, 0));
	}
}
