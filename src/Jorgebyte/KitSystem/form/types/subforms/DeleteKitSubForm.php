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

namespace Jorgebyte\KitSystem\form\types\subforms;

use EasyUI\variant\ModalForm;
use Exception;
use IvanCraft623\languages\Translator;
use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\util\LangKey;
use Jorgebyte\KitSystem\util\Sound;
use Jorgebyte\KitSystem\util\SoundNames;
use pocketmine\player\Player;

class DeleteKitSubForm extends ModalForm{
	private Player $player;
	private string $kitName;
	private Translator $translator;
	private \Closure $t;

	public function __construct(Player $player, string $kitName){
		$this->player = $player;
		$this->kitName = $kitName;
		$this->translator = Main::getInstance()->getTranslator();
		$this->t = function(string $key, array $replacements = []) : string{
			return $this->translator->translate($this->player, $key, $replacements);
		};

		parent::__construct(
			($this->t)(LangKey::TITLE_DELETE_KIT_CONFIRM->value),
			($this->t)(LangKey::CONTENT_DELETE_KIT_CONFIRM->value, ['%kit%' => $kitName])
		);
	}

	protected function onAccept(Player $player) : void{
		$t = $this->t;
		try{
			Main::getInstance()->getKitManager()->deleteKit($this->kitName);
			$player->sendMessage($t(
				LangKey::SUCCESS_DELETE_KIT->value,
				['%kit%' => $this->kitName]
			));
			Sound::addSound($player, SoundNames::GOOD_TONE->value);
		} catch(Exception $e){
			$player->sendMessage($t(
				LangKey::ERROR_GENERIC->value,
				['%error%' => $e->getMessage()]
			));
			Sound::addSound($player, SoundNames::BAD_TONE->value);
		}
	}

	protected function onDeny(Player $player) : void{
		$t = $this->t;
		$player->sendMessage($t(LangKey::CANCEL_DELETE_KIT->value));
		Sound::addSound($player, SoundNames::GOOD_TONE->value);
	}
}
