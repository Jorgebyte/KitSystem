<?php

declare(strict_types=1);

/*
 *    KitSystem
 *    Api: 5.3.0
 *    Version: 1.0.0
 *    Author: Jorgebyte
 */

namespace Jorgebyte\KitSystem\form;

use EasyUI\Form;
use InvalidArgumentException;
use Jorgebyte\KitSystem\form\types\{CategoryForm,
    CreateCategoryForm,
    CreateKitForm,
    DeleteCategoryForm,
    DeleteKitForm,
    GiveKitAllForm,
    GiveKitForm,
    KitsForm,
    subforms\DeleteCategorySubForm,
    subforms\DeleteKitSubForm};
use Jorgebyte\KitSystem\util\Sound;
use Jorgebyte\KitSystem\util\SoundNames;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use Throwable;

class FormManager
{
    private static array $formMap = [
        // [Kit] \\
        FormTypes::CREATE_KIT->value => CreateKitForm::class,
        FormTypes::DELETE_KIT->value => DeleteKitForm::class,
        FormTypes::GIVEKIT->value => GiveKitForm::class,
        FormTypes::GIVEKITALL->value => GiveKitAllForm::class,
        FormTypes::KITS->value => KitsForm::class,
        // [Categories] \\
        FormTypes::CATEGORY->value => CategoryForm::class,
        FormTypes::CREATE_CATEGORY->value => CreateCategoryForm::class,
        FormTypes::DELETE_CATEGORY->value => DeleteCategoryForm::class,
        //  [SubForms] \\
        FormTypes::DELETE_KIT_SUBFORM->value => DeleteKitSubForm::class,
        FormTypes::DELETE_CATEGORY_SUBFORM->value => DeleteCategorySubForm::class,
    ];
    private static function sendFormWithSound(Player $player, Form $form): void
    {
        Sound::addSound($player, SoundNames::OPEN_FORM->value);
        $player->sendForm($form);
    }

    public static function sendForm(Player $player, string $formType, array $args = []): void
    {
        if (!isset(self::$formMap[$formType])) {
            throw new InvalidArgumentException("ERROR: Form type " . $formType . " is not recognized");
        }

        $formClass = self::$formMap[$formType];
        if (!is_subclass_of($formClass, Form::class)) {
            throw new InvalidArgumentException("ERROR: The class " . $formClass . " is not a valid form type");
        }

        try {
            $form = new $formClass(...$args);
            self::sendFormWithSound($player, $form);

        } catch (Throwable $e) {
            $player->sendMessage(TextFormat::RED . "ERROR: creating form: " . $e->getMessage());
            Server::getInstance()->getLogger()->error($e->getMessage());
        }
    }
}
