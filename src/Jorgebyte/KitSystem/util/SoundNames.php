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

/*
 * Enumeration representing the types of sounds.
 */
enum SoundNames : string{
	case OPEN_FORM = 'random.pop2';
	case OPEN_MENU = 'bubble.pop';
	case GOOD_TONE = 'random.orb';
	case BAD_TONE = 'mob.villager.no';
}
