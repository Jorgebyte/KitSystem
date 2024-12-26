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

namespace Jorgebyte\KitSystem\form;

use EasyUI\Form;
use InvalidArgumentException;
use Jorgebyte\KitSystem\form\types\CategoryForm;
use Jorgebyte\KitSystem\form\types\CreateCategoryForm;
use Jorgebyte\KitSystem\form\types\CreateKitForm;
use Jorgebyte\KitSystem\form\types\EditCategoryForm;
use Jorgebyte\KitSystem\form\types\EditKitDataForm;
use Jorgebyte\KitSystem\form\types\GiveKitAllForm;
use Jorgebyte\KitSystem\form\types\GiveKitForm;
use Jorgebyte\KitSystem\form\types\KitsForm;
use Jorgebyte\KitSystem\form\types\SelectCategoryForm;
use Jorgebyte\KitSystem\form\types\SelectKitForm;
use Jorgebyte\KitSystem\form\types\subforms\DeleteCategorySubForm;
use Jorgebyte\KitSystem\form\types\subforms\DeleteKitSubForm;
use Jorgebyte\KitSystem\form\types\subforms\WhatToEditSubForm;
use Jorgebyte\KitSystem\util\Sound;
use Jorgebyte\KitSystem\util\SoundNames;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use Throwable;
use function is_subclass_of;

class FormManager{
	private static array $formMap = [
		// [Kit] \\
		FormTypes::CREATE_KIT->value => CreateKitForm::class,
		FormTypes::EDIT_KIT_DATA->value => EditKitDataForm::class,
		FormTypes::GIVEKIT->value => GiveKitForm::class,
		FormTypes::GIVEKITALL->value => GiveKitAllForm::class,
		FormTypes::KITS->value => KitsForm::class,
		FormTypes::SELECT_KIT->value => SelectKitForm::class,
		// [Categories] \\
		FormTypes::CATEGORY->value => CategoryForm::class,
		FormTypes::CREATE_CATEGORY->value => CreateCategoryForm::class,
		FormTypes::EDIT_CATEGORY_FORM->value => EditCategoryForm::class,
		FormTypes::SELECT_CATEGORY->value => SelectCategoryForm::class,
		//  [SubForms] \\
		FormTypes::DELETE_KIT_SUBFORM->value => DeleteKitSubForm::class,
		FormTypes::DELETE_CATEGORY_SUBFORM->value => DeleteCategorySubForm::class,
		FormTypes::WHAT_TO_EDIT_SUBFORM->value => WhatToEditSubForm::class,
	];

	private static function sendFormWithSound(Player $player, Form $form) : void{
		Sound::addSound($player, SoundNames::OPEN_FORM->value);
		$player->sendForm($form);
	}

	public static function sendForm(Player $player, string $formType, array $args = []) : void{
		if(!isset(self::$formMap[$formType])){
			throw new InvalidArgumentException("ERROR: Form type " . $formType . " is not recognized");
		}

		$formClass = self::$formMap[$formType];
		if(!is_subclass_of($formClass, Form::class)){
			throw new InvalidArgumentException("ERROR: The class " . $formClass . " is not a valid form type");
		}

		try{
			$form = new $formClass(...$args);
			self::sendFormWithSound($player, $form);

		} catch(Throwable $e){
			$player->sendMessage(TextFormat::RED . "ERROR: creating form: " . $e->getMessage());
			Server::getInstance()->getLogger()->error($e->getMessage());
		}
	}
}
