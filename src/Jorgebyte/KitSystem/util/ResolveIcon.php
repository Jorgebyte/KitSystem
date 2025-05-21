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

use EasyUI\icon\ButtonIcon;
use function preg_match;
use function strtolower;
use function trim;

/**
 * Utility class for resolving valid ButtonIcon instances from icon paths or URLs.
 */
final class ResolveIcon{

	/**
	 * Resolves a ButtonIcon from a given string address.
	 * Returns null for "default", empty, or null input.
	 */
	public static function resolveIcon(?string $address) : ?ButtonIcon{
		if($address === null || strtolower(trim($address)) === "default"){
			return null;
		}

		$address = trim($address);

        if (preg_match('#^https?://#i', $address) === 1) {
            return new ButtonIcon($address, ButtonIcon::TYPE_URL);
        }

        return new ButtonIcon($address, ButtonIcon::TYPE_PATH);
	}
}
