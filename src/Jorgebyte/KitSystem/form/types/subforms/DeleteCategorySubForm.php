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

/**
 * Modal confirmation form used to delete a category.
 *
 * If accepted, the category and its associated kits (if any) will be detached and removed from the database.
 * Triggered by {@see SelectCategoryForm} with ActionType::DELETE_CATEGORY.
 */
class DeleteCategorySubForm extends ModalForm{
	private Player $player;
	private string $categoryName;
	private Translator $translator;
	private \Closure $t;

	public function __construct(Player $player, string $categoryName){
		$this->categoryName = $categoryName;
		$this->player = $player;
		$this->translator = Main::getInstance()->getTranslator();
		$this->t = function(string $key, array $r = []) : string{
			return $this->translator->translate($this->player, $key, $r);
		};

		parent::__construct(
			($this->t)(LangKey::TITLE_DELETE_CATEGORY_CONFIRM->value),
			($this->t)(LangKey::CONTENT_DELETE_CATEGORY_CONFIRM->value, ['%category%' => $categoryName])
		);
	}

	protected function onAccept(Player $player) : void{
		$t = $this->t;
		try{
			Main::getInstance()->getCategoryManager()->deleteCategory($this->categoryName);
			$player->sendMessage($t(
				LangKey::SUCCESS_DELETE_CATEGORY->value,
				['%category%' => $this->categoryName]
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
		$player->sendMessage($t(LangKey::CANCEL_DELETE_CATEGORY->value));
		Sound::addSound($player, SoundNames::GOOD_TONE->value);
	}
}
