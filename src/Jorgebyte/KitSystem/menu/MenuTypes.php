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

namespace Jorgebyte\KitSystem\menu;

enum MenuTypes : string{
	case EDIT_KIT = 'editkit';
	case PREVIEW_KIT = 'previewkit';
}
