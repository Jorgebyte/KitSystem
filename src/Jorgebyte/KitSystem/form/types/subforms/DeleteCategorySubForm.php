<?php

declare(strict_types=1);

/*
 *    KitSystem
 *    Api: 5.3.0
 *    Version: 1.0.0
 *    Author: Jorgebyte
 */

namespace Jorgebyte\KitSystem\form\types\subforms;

use EasyUI\variant\ModalForm;
use Exception;
use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\util\Sound;
use Jorgebyte\KitSystem\util\SoundNames;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class DeleteCategorySubForm extends ModalForm
{
    protected string $categoryName;

    public function __construct(string $categoryName)
    {
        parent::__construct("Confirmation of deleting category", "Are you sure you want to delete the category: " .  $categoryName . "?");
        $this->categoryName = $categoryName;
    }

    protected function onAccept(Player $player): void
    {
        try {
            Main::getInstance()->getCategoryManager()->deleteCategory($this->categoryName);
        } catch (Exception $e) {
            $player->sendMessage(TextFormat::RED . $e->getMessage());
            Sound::addSound($player, SoundNames::BAD_TONE->value);
        }
        $player->sendMessage(TextFormat::GREEN . "the kit has been removed");
        Sound::addSound($player, SoundNames::GOOD_TONE->value);
    }

    protected function onDeny(Player $player): void
    {
        $player->sendMessage(TextFormat::YELLOW . "Category removal cancelled.");
        Sound::addSound($player, SoundNames::GOOD_TONE->value);
    }
}
