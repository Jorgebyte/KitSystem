<?php

declare(strict_types=1);

/*
 *    KitSystem
 *    Api: 5.3.0
 *    Version: 1.0.0
 *    Author: Jorgebyte
 */

namespace Jorgebyte\KitSystem\form\types;

use EasyUI\element\Dropdown;
use EasyUI\element\Input;
use EasyUI\element\Option;
use EasyUI\element\Toggle;
use EasyUI\utils\FormResponse;
use EasyUI\variant\CustomForm;
use Exception;
use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\util\Sound;
use Jorgebyte\KitSystem\util\SoundNames;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class CreateKitForm extends CustomForm
{
    public function __construct()
    {
        parent::__construct("KitSystem - Create Kit");
    }

    public function onCreation(): void
    {
        $this->addElement("kitName", new Input("Kit Name", null, "E.g. Warrior"));
        $this->addElement("kitPrefix", new Input("Prefix", null, "E.g. [Warrior]"));
        $this->addElement("cooldown", new Input("Cooldown (optional, in seconds)", null, "E.g. 3600"));
        $this->addElement("price", new Input("Price (optional)", null, "E.g. 100"));
        $this->addElement("permission", new Input("Permission (optional)", null, "E.g. kit.warrior.use"));
        $this->addElement("icon", new Input("Icon URL (optional)", null, "https://example.com/icon.png"));
        $this->addElement("storeInChest", new Toggle("Store in chest?", true));
        $dropdownCategories = new Dropdown("Select Category (optional)");
        $dropdownCategories->addOption(new Option("None", "None"));
        $categories = Main::getInstance()->getCategoryManager()->getAllCategories();

        foreach ($categories as $category) {
            $dropdownCategories->addOption(new Option($category->getName(), $category->getName()));
        }
        $this->addElement("selectedCategory", $dropdownCategories);
    }

    protected function onSubmit(Player $player, FormResponse $response): void
    {
        $kitName = $response->getInputSubmittedText("kitName");
        $kitPrefix = $response->getInputSubmittedText("kitPrefix");
        $cooldown = $response->getInputSubmittedText("cooldown");
        $price = $response->getInputSubmittedText("price");
        $permission = $response->getInputSubmittedText("permission");
        $icon = $response->getInputSubmittedText("icon");
        $storeInChest = $response->getToggleSubmittedChoice("storeInChest");
        $selectedCategory = $response->getDropdownSubmittedOptionId("selectedCategory");
        $category = $selectedCategory !== "None" ? $selectedCategory : null;

        if ($kitName === '' || $kitPrefix === '') {
            $player->sendMessage(TextFormat::RED . "ERROR: The Kit Name and Prefix are REQUIRED!!!");
            Sound::addSound($player, SoundNames::BAD_TONE->value);
            return;
        }

        $cooldown = $cooldown === '' ? null : (int) $cooldown;
        $price = $price === '' ? null : (float) $price;
        $permission = $permission === '' ? null : $permission;
        $icon = $icon === '' ? null : $icon;
        $armorContents = $player->getArmorInventory()->getContents();
        $inventoryContents = $player->getInventory()->getContents();

        try {
            Main::getInstance()->getKitManager()->createKit(
                $kitName,
                $kitPrefix,
                $armorContents,
                $inventoryContents,
                $cooldown,
                $price,
                $permission,
                $icon,
                $storeInChest,
                $category
            );

            $player->sendMessage(TextFormat::GREEN . "Kit: " . TextFormat::MINECOIN_GOLD . $kitName . TextFormat::GREEN . " created successfully!");
            Sound::addSound($player, SoundNames::GOOD_TONE->value);
        } catch (Exception $e) {
            $player->sendMessage(TextFormat::RED . "Error: " . $e->getMessage());
            Sound::addSound($player, SoundNames::BAD_TONE->value);
        }
    }
}
