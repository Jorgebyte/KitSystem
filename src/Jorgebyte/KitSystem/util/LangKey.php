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
 * Enumeration representing LangKey types.
 */
enum LangKey : string{
	// Errors
	case ERROR_GENERIC = 'error.generic';
	case ERROR_KIT_INVALID = 'error.kit.invalid';
	case ERROR_CATEGORY_REQUIRED = 'error.category.require';
	case ERROR_KIT_REQUIRED = 'error.kit.require';
	case ERROR_KIT_NOT_EXIST = 'kit.not.exist';
	case ERROR_INVENTORY_SPACE = 'error.inventory.space';
	case PLAYER_NOT_ONLINE = 'player.not.online';
	case INVALID_CHEST_KIT = 'invalid.chest.kit';

	// Kits
	case KIT_CLAIMED = 'kit.claimed';
	case STARTERKIT_RECEIVED = 'starterkit.received';
	case OPEN_KIT = 'open.kit';
	case COOLDOWN_ACTIVE = 'cooldown.active';
	case FULL_INV = 'full.inv';
	case FULL_INV_CHEST = 'full.inv.chest';
	case KIT_UPDATE = 'kit.update';
	case CATEGORY_KIT_ADDED_SUCCESS = 'category.kit.added';
	case CATEGORY_KIT_REMOVE_SUCCESS = 'category.kit.remove';
	case GIVEALL_KIT_BROADCAST = 'giveall.kit.broadcast';
	case GIVEALL_SUCCESS = 'giveall.success';
	case GIVE_KIT_SUCCESS = 'givekit.success';
	case KIT_CREATED_SUCCESS = 'kit.created';

	// Money
	case FAILED_MONEY = 'failed.money';
	case LACK_OF_MONEY = 'lack.of.money';

	// Permissions
	case WITHOUT_PERMISSIONS = 'without.permissions';

	// Categories
	case CATEGORY_CREATED_SUCCESS = 'category.created';
	case UPDATE_DATA = 'update.data';

	// Buttons and Labels
	case BUTTON_LABEL_COOLDOWN = 'button.label.cooldown';
	case BUTTON_LABEL_PRICE = 'button.label.price';
	case BUTTON_LABEL_FREE = 'button.label.free';
	case BUTTON_LABEL_VIEW_KITS = 'button.label.view.kits';

	// Forms - Utils
	case LABEL_PERMISSION = 'label.permission';
	case PLACEHOLDER_PERMISSION = 'placeholder.permission';
	case LABEL_ICON = 'label.icon';
	case PLACEHOLDER_ICON = 'placeholder.icon';
	case LABEL_COOLDOWN = 'label.cooldown';
	case PLACEHOLDER_COOLDOWN = 'placeholder.cooldown';
	case LABEL_PRICE = 'label.price';
	case PLACEHOLDER_PRICE = 'placeholder.price';
	case LABEL_STORE_IN_CHEST = 'label.store.in.chest';
	case LABEL_SELECT_CATEGORY = 'label.select.category';

	// Forms - Create Category
	case TITLE_CREATE_CATEGORY = 'title.create.category';
	case LABEL_CATEGORY_NAME = 'label.category.name';
	case PLACEHOLDER_CATEGORY_NAME = 'placeholder.category.name';
	case LABEL_CATEGORY_PREFIX = 'label.category.prefix';
	case PLACEHOLDER_CATEGORY_PREFIX = 'placerholder.caregory.prefix';

	// Forms - Create Kit
	case TITLE_CREATE_KIT = 'title.create.kit';
	case LABEL_KIT_NAME = 'label.kit.name';
	case PLACEHOLDER_KIT_NAME = 'placeholder.kit.name';
	case LABEL_KIT_PREFIX = 'label.kit.prefix';
	case PLACEHOLDER_KIT_PREFIX = 'placeholder.kit.prefix';

	// Forms - Edit Category
	case TITLE_EDIT_CATEGORY = 'title.edit.category';
	case LABEL_ADD_KIT = 'label.add.kit';
	case LABEL_REMOVE_KIT = 'label.remove.kit';

	// Forms - Edit Kit
	case TITLE_EDIT_KIT_DATA = 'title.edit.kit.data';

	// Forms - Give All Kit
	case TITLE_GIVE_KIT_ALL = 'title.give.kit.all';
	case LABEL_SELECT_KIT = 'label.select.kit';
	case LABEL_KIT_QUANTITY_PER_PLAYER = 'label.kit.quantity.player';

	// Forms - Give Kit to Player
	case TITLE_GIVE_KIT = 'title.give.kit';
	case LABEL_SELECT_PLAYER = 'label.select.player';
	case LABEL_KIT_QUANTITY = 'label.kit.quantity';

	// Forms - Kits Available
	case TITLE_KITS_AVAILABLE = 'title.kits.available';

	// Forms - Select Category
	case TITLE_SELECT_CATEGORY = 'title.select.category';

	// Forms - Select Kit
	case TITLE_SELECT_KIT = 'title.select.kit';

	// Forms - Delete Kit
	case TITLE_DELETE_KIT = 'title.delete.kit';

	// Forms - Delete Category
	case TITLE_DELETE_CATEGORY = 'title.delete.category';

	// Forms - Category List
	case TITLE_CATEGORY = 'title.category';

	// Forms - Sub - Delete Category
	case TITLE_DELETE_CATEGORY_CONFIRM = 'title.delete.category.confirm';
	case CONTENT_DELETE_CATEGORY_CONFIRM = 'content.delete.category.confirm';
	case SUCCESS_DELETE_CATEGORY = 'success.delete.category';
	case CANCEL_DELETE_CATEGORY = 'cancel.delete.category';

	// Forms - Sub - Delete Kit
	case TITLE_DELETE_KIT_CONFIRM = 'title.delete.kit.confirm';
	case CONTENT_DELETE_KIT_CONFIRM = 'content.delete.kit.confirm';
	case SUCCESS_DELETE_KIT = 'success.delete.kit';
	case CANCEL_DELETE_KIT = 'cancel.delete.kit';

	// Forms - Sub - What to Edit
	case TITLE_WHAT_TO_EDIT = 'label.what.to.edi';
	case BUTTON_LABEL_EDIT_ITEMS = 'button.label.edit.items';
	case BUTTON_LABEL_EDIT_DATA = 'button.label.edit.data';
}
