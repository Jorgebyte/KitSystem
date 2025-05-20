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
use Jorgebyte\KitSystem\util\ResolveIcon;
use pocketmine\player\Player;

class SelectCategoryForm extends SimpleForm{
	private Player $player;
	private Translator $translator;
	private \Closure $t;
	protected string $args;

	public function __construct(Player $player, string $args){
		$this->player = $player;
		$this->translator = Main::getInstance()->getTranslator();
		$this->t = function(string $key, array $r = []) : string{
			return $this->translator->translate($this->player, $key, $r);
		};
		$this->args = $args;
		parent::__construct(
			($this->t)(LangKey::TITLE_SELECT_CATEGORY->value)
		);
	}

	protected function onCreation() : void{
		$t = $this->t;
		$categories = Main::getInstance()->getCategoryManager()->getAllCategories();

		foreach($categories as $cat){
			$button = new Button($cat->getPrefix());
            if($icon = ResolveIcon::resolveIcon($cat->getIcon())){
                $button->setIcon($icon);
            }
			$button->setSubmitListener(function (Player $player) use ($t, $cat) : void{
				switch($this->args){
					case "deletecategory":
						FormManager::sendForm($player, FormTypes::DELETE_CATEGORY_SUBFORM->value, [$player, $cat->getName()]);
						break;
					case "editcategory":
						FormManager::sendForm($player, FormTypes::EDIT_CATEGORY_FORM->value, [$player, $cat->getName()]);
						break;
				}
			});
			$this->addButton($button);
		}
	}
}
