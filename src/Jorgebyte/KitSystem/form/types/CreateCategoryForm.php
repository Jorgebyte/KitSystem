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

namespace Jorgebyte\KitSystem\form\types;

use EasyUI\element\Input;
use EasyUI\utils\FormResponse;
use EasyUI\variant\CustomForm;
use Exception;
use IvanCraft623\languages\Translator;
use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\util\LangKey;
use Jorgebyte\KitSystem\util\Sound;
use Jorgebyte\KitSystem\util\SoundNames;
use pocketmine\player\Player;

/**
 * Custom form that allows the creation of a new category.
 */
class CreateCategoryForm extends CustomForm{
	private Player $player;
	private Translator $translator;
	private \Closure $t;

	public function __construct(Player $player){
		$this->player = $player;
		$this->translator = Main::getInstance()->getTranslator();
		$this->t = function(string $key, array $replacements = []) : string{
			return $this->translator->translate($this->player, $key, $replacements);
		};

		parent::__construct(
			($this->t)(LangKey::TITLE_CREATE_CATEGORY->value)
		);
	}

	public function onCreation() : void{
		$t = $this->t;
		$this->addElement("categoryName", new Input(
			$t(LangKey::LABEL_CATEGORY_NAME->value),
			null,
			$t(LangKey::PLACEHOLDER_CATEGORY_NAME->value)
		));
		$this->addElement("categoryPrefix", new Input(
			$t(LangKey::LABEL_CATEGORY_PREFIX->value),
			null,
			$t(LangKey::PLACEHOLDER_CATEGORY_PREFIX->value)
		));
		$this->addElement("permission", new Input(
			$t(LangKey::LABEL_PERMISSION->value),
			null,
			$t(LangKey::PLACEHOLDER_PERMISSION->value)
		));
		$this->addElement("icon", new Input(
			$t(LangKey::LABEL_ICON->value),
			null,
			$t(LangKey::PLACEHOLDER_ICON->value)
		));
	}

	protected function onSubmit(Player $player, FormResponse $response) : void{
		$t = $this->t;

		$name = $response->getInputSubmittedText("categoryName");
		$prefix = $response->getInputSubmittedText("categoryPrefix");
		$perm = $response->getInputSubmittedText("permission") ?: null;
		$icon = $response->getInputSubmittedText("icon")       ?: null;

		if($name === '' || $prefix === ''){
			$player->sendMessage($t(LangKey::ERROR_CATEGORY_REQUIRED->value));
			Sound::addSound($player, SoundNames::BAD_TONE->value);
			return;
		}

		try{
			Main::getInstance()
				->getCategoryManager()
				->createCategory($name, $prefix, $perm, $icon);

			$player->sendMessage(
				$t(
					LangKey::CATEGORY_CREATED_SUCCESS->value,
					['%category%' => $name]
				)
			);
			Sound::addSound($player, SoundNames::GOOD_TONE->value);

		} catch(Exception $e){
			$player->sendMessage(
				$t(
					LangKey::ERROR_GENERIC->value,
					['%error%' => $e->getMessage()]
				)
			);
			Sound::addSound($player, SoundNames::BAD_TONE->value);
		}
	}
}
