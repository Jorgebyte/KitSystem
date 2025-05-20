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

class ResolveIcon{
	public static function resolveIcon(?string $address) : ?ButtonIcon{
		if($address === null || strtolower(trim($address)) === "default"){
			return null;
		}
		$address = trim($address);
		if(preg_match('#^https?://#i', $address)){
			return new ButtonIcon($address, ButtonIcon::TYPE_URL);
		}
		return new ButtonIcon($address, ButtonIcon::TYPE_PATH);
	}
}
