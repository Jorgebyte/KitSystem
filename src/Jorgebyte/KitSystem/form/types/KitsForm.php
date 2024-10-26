<?php

declare(strict_types=1);

/*
 *    KitSystem
 *    Api: 5.3.0
 *    Version: 1.0.0
 *    Author: Jorgebyte
 */

namespace Jorgebyte\KitSystem\form\types;

use EasyUI\element\Button;
use EasyUI\icon\ButtonIcon;
use EasyUI\variant\SimpleForm;
use Jorgebyte\KitSystem\form\FormManager;
use Jorgebyte\KitSystem\form\FormTypes;
use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\message\MessageKey;
use Jorgebyte\KitSystem\util\PlayerUtil;
use Jorgebyte\KitSystem\util\TimeUtil;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class KitsForm extends SimpleForm
{
    protected Player $player;

    public function __construct(Player $player)
    {
        $this->player = $player;
        parent::__construct("KitSystem - Kits Available");
    }

    protected function onCreation(): void
    {
        $economyProvider = Main::getInstance()->getEconomyProvider();
        $message = Main::getInstance()->getMessage();
        $kits = Main::getInstance()->getKitManager()->getAllKits();
        $categories = Main::getInstance()->getCategoryManager()->getAllCategories();

        foreach ($categories as $category) {
            $categoryPrefix = $category->getPrefix();

            $buttonLabel = $categoryPrefix . "\n" . TextFormat::GRAY . "View Kits in: " . $categoryPrefix;
            $button = new Button($buttonLabel);
            $icon = $category->getIcon();

            if ($icon !== null) {
                $button->setIcon(new ButtonIcon($icon));
            }

            $button->setSubmitListener(function () use ($category, $message) {
                if (!$category->canUseCategory($this->player)) {
                    $this->player->sendMessage($message->getMessage(MessageKey::WITHOUT_PERMISSIONS, ["category" => $category->getName()]));
                    return;
                }

                FormManager::sendForm($this->player, FormTypes::CATEGORY->value, [$this->player, $category->getName()]);
            });

            $this->addButton($button);
        }

        foreach ($kits as $kit) {
            foreach ($categories as $category) {
                if ($category->hasKit($kit->getName())) {
                    continue 2;
                }
            }

            if (!$kit->canUseKit($this->player)) {
                continue;
            }

            $kitName = $kit->getName();
            $kitPrefix = $kit->getPrefix();
            $kitPrice = $kit->getPrice() ?? 0;
            $cooldown = Main::getInstance()->getCooldownManager()->getCooldown($this->player, $kitName);

            $buttonLabel = $kitPrefix . "\n";
            if ($cooldown !== null) {
                $formattedCooldown = TimeUtil::formatCooldown($cooldown);
                $buttonLabel .= TextFormat::RED . "Cooldown: " . $formattedCooldown;
            } else {
                $buttonLabel .= TextFormat::MINECOIN_GOLD . "Price: " . ($kitPrice > 0 ? $kitPrice : "FREE!");
            }

            $button = new Button($buttonLabel);
            $icon = $kit->getIcon();

            if ($icon !== null) {
                $button->setIcon(new ButtonIcon($icon));
            }

            $button->setSubmitListener(function () use ($message, $kit, $kitName, $kitPrice, $economyProvider) {
                $currentCooldown = Main::getInstance()->getCooldownManager()->getCooldown($this->player, $kitName);
                if ($currentCooldown !== null) {
                    $formattedCooldown = TimeUtil::formatCooldown($currentCooldown);
                    $this->player->sendMessage($message->getMessage(MessageKey::COOLDOWN_ACTIVE, ["time" => $formattedCooldown]));
                    return;
                }
                if (!$kit->shouldStoreInChest() && !PlayerUtil::hasEnoughSpace($this->player, $kit)) {
                    $this->player->sendMessage($message->getMessage(MessageKey::FULL_INV));
                    return;
                }
                $processKit = function () use ($message, $kit, $kitName): void {
                    if ($kit->shouldStoreInChest()) {
                        Main::getInstance()->getKitManager()->giveKitChest($this->player, $kit);
                    } else {
                        Main::getInstance()->getKitManager()->giveKitItems($this->player, $kit);
                    }

                    $this->player->sendMessage($message->getMessage(MessageKey::KIT_CLAIMED, ["kitname" => $kitName]));

                    $cooldownDuration = $kit->getCooldown();
                    if ($cooldownDuration > 0) {
                        Main::getInstance()->getCooldownManager()->setCooldown($this->player, $kitName, $cooldownDuration);
                    }
                };
                if ($kitPrice > 0) {
                    $economyProvider->getMoney($this->player, function ($balance) use ($economyProvider, $kitPrice, $processKit, $message) {
                        if ($balance < $kitPrice) {
                            $this->player->sendMessage($message->getMessage(MessageKey::LACK_OF_MONEY, ["kitprice" => strval($kitPrice)]));
                            return;
                        }

                        $economyProvider->takeMoney($this->player, $kitPrice, function (bool $success) use ($processKit, $message) {
                            if (!$success) {
                                $this->player->sendMessage($message->getMessage(MessageKey::FAILED_MONEY));
                                return;
                            }
                            $processKit();
                        });
                    });
                } else {
                    $processKit();
                }
            });
            $this->addButton($button);
        }
    }
}
