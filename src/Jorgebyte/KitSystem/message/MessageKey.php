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

namespace Jorgebyte\KitSystem\message;

class MessageKey{
	public const PREFIX = "prefix";
	public const KIT_CLAIMED = "kit_claimed";
	public const STARTERKIT_RECEIVED = "starterkit_received";
	public const OPEN_KIT = "open_kit";
	public const COOLDOWN_ACTIVE = "cooldown_active";
	public const FULL_INV = "full_inv";
	public const FULL_INV_CHEST = "full_inv_chest";

	public const FAILED_MONEY = "failed_money";
	public const LACK_OF_MONEY = "lack_of_money";

	// [BROADCAST] \\
	public const GIVEALL_KIT_BROADCAST = "giveall_kit_broadcast";

	public const WITHOUT_PERMISSIONS = "without_permissions";
}
