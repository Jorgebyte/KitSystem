<?php

declare(strict_types=1);

/*
 *    KitSystem
 *    Api: 5.3.0
 *    Version: 1.0.0
 *    Author: Jorgebyte
 */

namespace Jorgebyte\KitSystem\form;

enum FormTypes: string
{
    case CREATE_KIT = 'createkit';
    case DELETE_KIT = 'deletekit';
    case GIVEKIT = 'givekit';
    case GIVEKITALL = 'givekitall';
    case KITS = 'kits';
    // [Categories] \\
    case CATEGORY = 'category';
    case CREATE_CATEGORY = 'createcategory';
    case DELETE_CATEGORY = 'deletecategory';
    // SubForms \\
    case DELETE_KIT_SUBFORM = 'deletekitsubform';
    case DELETE_CATEGORY_SUBFORM = 'deletecategorysubform';
}
