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

namespace Jorgebyte\KitSystem\kit\category;

use Jorgebyte\KitSystem\kit\Kit;
use Jorgebyte\KitSystem\Main;
use RuntimeException;
use function array_values;

final class CategoryManager{
	/** @var array<string, Category> */
	private array $categories = [];

	public function __construct(){
		$this->loadCategories();
	}

	public function addCategory(Category $category) : void{
		$this->categories[$category->getName()] = $category;
		$this->persistCategory($category);
	}

	public function categoryExists(string $name) : bool{
		return isset($this->categories[$name]);
	}

	/**
	 * @throws RuntimeException If category already exists
	 */
	public function createCategory(string $name, string $prefix, ?string $permission = null, ?string $icon = null) : void{
		if($this->categoryExists($name)){
			throw new RuntimeException("Category '$name' already exists");
		}

		$category = new Category($name, $prefix, $permission, $icon);
		$this->addCategory($category);
	}

	/**
	 * @throws RuntimeException If category doesn't exist
	 */
	public function addKitToCategory(Kit $kit, string $categoryName) : void{
		$category = $this->getCategory($categoryName) ?? throw new RuntimeException("Category '$categoryName' not found");
		$category->addKit($kit);
		$this->persistCategory($category);

		Main::getInstance()->getDatabase()->executeChange(
			'category_kits.insert',
			[
				'category_name' => $category->getName(),
				'kit_name' => $kit->getName()
			]
		);
	}

	public function getCategory(string $name) : ?Category{
		return $this->categories[$name] ?? null;
	}

	public function getKitsByCategory(string $categoryName) : array{
		if(!isset($this->categories[$categoryName])){
			return [];
		}

		$category = $this->categories[$categoryName];
		return $category->getKits();
	}

	/** @return array<Category> */
	public function getAllCategories() : array{
		return array_values($this->categories);
	}

	/**
	 * @throws RuntimeException If category doesn't exist
	 */
	public function deleteCategory(string $name) : void{
		if(!isset($this->categories[$name])){
			throw new RuntimeException("Category '$name' not found");
		}

		unset($this->categories[$name]);
		Main::getInstance()->getDatabase()->executeChange('categories.delete', ['name' => $name]);
	}

	public function persistCategory(Category $category) : void{
		Main::getInstance()->getDatabase()->executeChange('categories.insert', [
			'name' => $category->getName(),
			'prefix' => $category->getPrefix(),
			'permission' => $category->getPermission(),
			'icon' => $category->getIcon()
		]);
	}

	public function saveCategory(Category $category) : void{
		$data = [
			'name' => $category->getName(),
			'prefix' => $category->getPrefix(),
			'permission' => $category->getPermission(),
			'icon' => $category->getIcon()
		];
		Main::getInstance()->getDatabase()->executeChange('categories.insert', $data);
		foreach($category->getKits() as $kit){
			Main::getInstance()->getDatabase()->executeChange('category_kits.insert', [
				'category_name' => $category->getName(),
				'kit_name' => $kit->getName()
			]);
		}
	}

	private function loadCategories() : void{
		Main::getInstance()->getDatabase()->executeSelect('categories.get_all', [], function(array $rows) : void{
			$categoryMap = [];
			foreach($rows as $row){
				$category = new Category(
					$row['name'],
					$row['prefix'],
					$row['permission'] ?? null,
					$row['icon'] ?? null
				);
				$categoryMap[$category->getName()] = $category;
			}
			Main::getInstance()->getDatabase()->executeSelect('category_kits.get_all', [], function(array $kitRows) use (&$categoryMap) : void{
				$kitManager = Main::getInstance()->getKitManager();
				foreach($kitRows as $row){
					$catName = $row['category_name'];
					$kitName = $row['kit_name'];
					if(isset($categoryMap[$catName])){
						$kit = $kitManager->getKit($kitName);
						if($kit !== null){
							$categoryMap[$catName]->addKit($kit);
						}
					}
				}
			});

			$this->categories = $categoryMap;
		});
	}
}
