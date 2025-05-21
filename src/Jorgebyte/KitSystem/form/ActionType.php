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

namespace Jorgebyte\KitSystem\form;

enum ActionType : string{
	case DELETE_KIT = "deletekit";
	case EDIT_KIT = "editkit";
	case PREVIEW_KIT = "previewkit";
	case DELETE_CATEGORY = "deletecategory";
	case EDIT_CATEGORY = "editcategory";
}
