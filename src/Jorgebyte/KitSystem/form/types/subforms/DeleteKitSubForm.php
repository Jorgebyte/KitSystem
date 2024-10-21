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

class DeleteKitSubForm extends ModalForm
{
    protected string $kitName;

    public function __construct(string $kitName)
    {
        parent::__construct("Confirmation of deleting kit", "Are you sure you want to delete the kit: " .  $kitName);
        $this->kitName = $kitName;
    }

    protected function onAccept(Player $player): void
    {
        try {
            Main::getInstance()->getKitManager()->deleteKit($this->kitName);
        } catch (Exception $e) {
            $player->sendMessage(TextFormat::RED . $e->getMessage());
            Sound::addSound($player, SoundNames::BAD_TONE->value);
        }
        $player->sendMessage(TextFormat::GREEN . "the kit has been removed");
        Sound::addSound($player, SoundNames::GOOD_TONE->value);
    }

    protected function onDeny(Player $player): void
    {
        $player->sendMessage(TextFormat::GREEN . "Kit Removal Cancelled");
        Sound::addSound($player, SoundNames::GOOD_TONE->value);
    }
}
