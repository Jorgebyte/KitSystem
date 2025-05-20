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

use EasyUI\element\Button;
use EasyUI\icon\ButtonIcon;
use EasyUI\variant\SimpleForm;
use IvanCraft623\languages\Translator;
use Jorgebyte\KitSystem\form\FormManager;
use Jorgebyte\KitSystem\form\FormTypes;
use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\util\LangKey;
use pocketmine\player\Player;

class DeleteCategoryForm extends SimpleForm{
	private Player $player;
	private Translator $translator;
	private \Closure $t;

	public function __construct(Player $player){
		$this->player = $player;
		$this->translator = Main::getInstance()->getTranslator();
		$this->t = function(string $key, array $r = []) : string{
			return $this->translator->translate($this->player, $key, $r);
		};
		parent::__construct(
			($this->t)(LangKey::TITLE_DELETE_CATEGORY->value)
		);
	}

	protected function onCreation() : void{
		$categories = Main::getInstance()->getCategoryManager()->getAllCategories();
		foreach($categories as $category){
			$categoryName = $category->getName();
			$categoryPrefix = $category->getPrefix();
			$icon = $category->getIcon();
			$button = new Button($categoryPrefix);
			if($icon !== null){
				$button->setIcon(new ButtonIcon($icon));
			}
			$button->setSubmitListener(function (Player $player) use ($categoryName) : void{
				FormManager::sendForm($player, FormTypes::DELETE_CATEGORY_SUBFORM->value, [$player, $categoryName]);
			});
			$this->addButton($button);
		}
	}
}
