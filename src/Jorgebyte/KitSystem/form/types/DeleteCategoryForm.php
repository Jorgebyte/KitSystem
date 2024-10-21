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
use pocketmine\player\Player;

class DeleteCategoryForm extends SimpleForm
{
    public function __construct()
    {
        parent::__construct("CategorySystem - Delete a Category");
    }

    protected function onCreation(): void
    {
        $categories = Main::getInstance()->getCategoryManager()->getAllCategories();

        foreach ($categories as $category) {
            $categoryName = $category->getName();
            $categoryPrefix = $category->getPrefix();

            $icon = $category->getIcon();
            $button = new Button($categoryPrefix);

            if ($icon !== null) {
                $button->setIcon(new ButtonIcon($icon));
            }

            $button->setSubmitListener(function (Player $player) use ($categoryName): void {
                FormManager::sendForm($player, FormTypes::DELETE_CATEGORY_SUBFORM->value, [$categoryName]);
            });

            $this->addButton($button);
        }
    }
}
