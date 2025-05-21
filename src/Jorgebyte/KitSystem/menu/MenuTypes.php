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

/**
 * Enum representing the types of menus available.
 * These values are used as keys in the MenuManager::$menuMap array.
 */
enum MenuTypes : string{
	case EDIT_KIT = 'editkit';
	case PREVIEW_KIT = 'previewkit';
}
