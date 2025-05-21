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

/**
 * Responsible for managing all category instances.
 * Handles creation, deletion, persistence, and kit assignments to categories.
 */
final class CategoryManager{
	/** @var array<string, Category> Associative map of category name => Category instance */
	private array $categories = [];

	/**
	 * Initializes the category manager by loading categories from the database.
	 */
	public function __construct(){
		$this->loadCategories();
	}

	/**
	 * Adds a category to the internal cache and persists it to the database.
	 */
	public function addCategory(Category $category) : void{
		$this->categories[$category->getName()] = $category;
		$this->persistCategory($category);
	}

	/**
	 * Checks whether a category with the given name exists.
	 */
	public function categoryExists(string $name) : bool{
		return isset($this->categories[$name]);
	}

	/**
	 * Creates a new category and registers it.
	 *
	 * @throws RuntimeException If the category already exists
	 */
	public function createCategory(string $name, string $prefix, ?string $permission = null, ?string $icon = null) : void{
		if($this->categoryExists($name)){
			throw new RuntimeException("Category '$name' already exists");
		}

		$category = new Category($name, $prefix, $permission, $icon);
		$this->addCategory($category);
	}

	/**
	 * Adds a kit to a category by name.
	 *
	 * @throws RuntimeException If the category does not exist
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

	/**
	 * Retrieves a category by name.
	 */
	public function getCategory(string $name) : ?Category{
		return $this->categories[$name] ?? null;
	}

	/**
	 * Retrieves all kits associated with a specific category.
	 *
	 * @return Kit[]
	 */
	public function getKitsByCategory(string $categoryName) : array{
		if(!isset($this->categories[$categoryName])){
			return [];
		}

		$category = $this->categories[$categoryName];
		return $category->getKits();
	}

	/**
	 * Returns all categories currently loaded.
	 *
	 * @return Category[]
	 */
	public function getAllCategories() : array{
		return array_values($this->categories);
	}

	/**
	 * Deletes a category and removes it from the internal cache and database.
	 *
	 * @throws RuntimeException If the category does not exist
	 */
	public function deleteCategory(string $name) : void{
		if(!isset($this->categories[$name])){
			throw new RuntimeException("Category '$name' not found");
		}

		unset($this->categories[$name]);
		Main::getInstance()->getDatabase()->executeChange('categories.delete', ['name' => $name]);
	}

	/**
	 * Persists a single category (without kits) to the database.
	 */
	public function persistCategory(Category $category) : void{
		Main::getInstance()->getDatabase()->executeChange('categories.insert', [
			'name' => $category->getName(),
			'prefix' => $category->getPrefix(),
			'permission' => $category->getPermission(),
			'icon' => $category->getIcon()
		]);
	}

	/**
	 * Saves a full category definition including all assigned kits to the database.
	 */
	public function saveCategory(Category $category) : void{
		$this->persistCategory($category);

		foreach($category->getKits() as $kit){
			Main::getInstance()->getDatabase()->executeChange('category_kits.insert', [
				'category_name' => $category->getName(),
				'kit_name' => $kit->getName()
			]);
		}
	}

	/**
	 * Loads all categories and their assigned kits from the database.
	 * Executes in two steps: category metadata and then category-kit bindings.
	 */
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
