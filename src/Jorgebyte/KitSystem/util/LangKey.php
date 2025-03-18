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

enum LangKey : string{
	case PREFIX = 'prefix';
	case KIT_CLAIMED = 'kit.claimed';
	case STARTERKIT_RECEIVED = 'starterkit.received';
	case OPEN_KIT = 'open.kit';
	case COOLDOWN_ACTIVE = 'cooldown.active';
	case FULL_INV = 'full.inv';
	case FULL_INV_CHEST = 'full.inv.chest';
	case FAILED_MONEY = 'failed.money';
	case LACK_OF_MONEY = 'lack.of.money';
	case GIVEALL_KIT_BROADCAST = 'giveall.kit.broadcast';
	case WITHOUT_PERMISSIONS = 'without.permissions';
	case KIT_UPDATE = "kit.update";
}
