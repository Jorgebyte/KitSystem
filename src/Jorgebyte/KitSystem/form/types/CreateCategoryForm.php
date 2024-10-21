<?php

declare(strict_types=1);

/*
 *    KitSystem
 *    Api: 5.3.0
 *    Version: 1.0.0
 *    Author: Jorgebyte
 */

namespace Jorgebyte\KitSystem\form\types;

use EasyUI\element\Input;
use EasyUI\utils\FormResponse;
use EasyUI\variant\CustomForm;
use Exception;
use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\util\Sound;
use Jorgebyte\KitSystem\util\SoundNames;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class CreateCategoryForm extends CustomForm
{
    public function __construct()
    {
        parent::__construct("KitSystem - Create Category");
    }

    public function onCreation(): void
    {
        $this->addElement("categoryName", new Input("Category Name", null, "E.g. Pay"));
        $this->addElement("categoryPrefix", new Input("Prefix", null, "E.g. [Pay]"));
        $this->addElement("permission", new Input("Permission (optional)", null, "E.g. category.warriors.use"));
        $this->addElement("icon", new Input("Icon URL (optional)", null, "https://example.com/icon.png"));
    }

    protected function onSubmit(Player $player, FormResponse $response): void
    {
        $categoryName = $response->getInputSubmittedText("categoryName");
        $categoryPrefix = $response->getInputSubmittedText("categoryPrefix");
        $permission = $response->getInputSubmittedText("permission");
        $icon = $response->getInputSubmittedText("icon");

        if ($categoryName === '' || $categoryPrefix === '') {
            $player->sendMessage(TextFormat::RED . "ERROR: The Category Name and Prefix are REQUIRED!!!");
            Sound::addSound($player, SoundNames::BAD_TONE->value);
            return;
        }

        $permission = $permission === '' ? null : $permission;
        $icon = $icon === '' ? null : $icon;

        try {
            Main::getInstance()->getCategoryManager()->createCategory($categoryName, $categoryPrefix, $permission, $icon);
            $player->sendMessage(TextFormat::GREEN . "Category: " . TextFormat::MINECOIN_GOLD . $categoryName . TextFormat::GREEN . " created successfully!");
            Sound::addSound($player, SoundNames::GOOD_TONE->value);
        } catch (Exception $e) {
            $player->sendMessage(TextFormat::RED . "ERROR: " . $e->getMessage());
            Sound::addSound($player, SoundNames::BAD_TONE->value);
        }
    }
}
