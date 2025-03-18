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
use Jorgebyte\KitSystem\form\FormManager;
use Jorgebyte\KitSystem\form\FormTypes;
use Jorgebyte\KitSystem\Main;
use pocketmine\player\Player;

class SelectCategoryForm extends SimpleForm{
	protected string $args;

	public function __construct(string $args){
		$this->args = $args;
		parent::__construct("KitSystem - Select Category");
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
				switch($this->args){
					case "deletecategory":
						FormManager::sendForm($player, FormTypes::DELETE_CATEGORY_SUBFORM->value, [$categoryName]);
						break;
					case "editcategory":
						FormManager::sendForm($player, FormTypes::EDIT_CATEGORY_FORM->value, [$categoryName]);
						break;
				}
			});

			$this->addButton($button);
		}
	}
}
