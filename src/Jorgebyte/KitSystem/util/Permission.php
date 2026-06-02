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

enum Permission : string{
	case COMMAND = 'kitsystem.command';

	case CREATE_KIT = 'kitsystem.command.create';
	case DELETE_KIT = 'kitsystem.command.delete';
	case GIVE_KIT = 'kitsystem.command.give';
	case GIVE_ALL_KIT = 'kitsystem.command.giveall';
	case EDIT_KIT = 'kitsystem.command.editkit';
	case PREVIEW_KIT = 'kitsystem.command.previewkit';

	case CREATE_CATEGORY = 'kitsystem.command.createcategory';
	case DELETE_CATEGORY = 'kitsystem.command.deletecategory';
	case EDIT_CATEGORY = 'kitsystem.command.editcategory';
}
