<?php

/*
 *   -- KitSystem --
 *
 *   Author: Jorgebyte
 *   Discord Contact: jorgess__
 *
 *  https://github.com/Jorgebyte/KitSystem
 */

declare(strict_types=1);

namespace Jorgebyte\KitSystem\kit\category;

use Exception;
use Jorgebyte\KitSystem\kit\Kit;
use Jorgebyte\KitSystem\Main;
use pocketmine\Server;
use function array_map;
use function array_values;
use function count;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function glob;
use function is_array;
use function is_dir;
use function is_string;
use function json_decode;
use function json_encode;
use function mkdir;
use function unlink;
use const DIRECTORY_SEPARATOR;
use const JSON_PRETTY_PRINT;

class CategoryManager{
	private array $categories = [];
	private string $directory;

	public function __construct(string $directory){
		$this->directory = $directory;
		$this->loadCategories();
	}

	public function addCategory(Category $category) : void{
		$this->categories[$category->getName()] = $category;
		$this->saveCategory($category);
	}

	public function categoryExists(string $name) : bool{
		return isset($this->categories[$name]);
	}

	/**
	 * @throws Exception
	 */
	public function createCategory(string $name, string $prefix, ?string $permission = null, ?string $icon = null) : void{
		if($this->categoryExists($name)){
			throw new Exception("A Category with this name already exists!");
		}
		$category = new Category($name, $prefix, $permission, $icon);
		$this->addCategory($category);
	}

	/**
	 * @throws Exception
	 */
	public function addKitToCategory(Kit $kit, string $categoryName) : void{
		$category = $this->getCategory($categoryName);

		if($category === null){
			throw new Exception("The specified category does not exist!");
		}

		$category->addKit($kit);
		$this->saveCategory($category);
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

	public function getAllCategories() : array{
		return array_values($this->categories);
	}

	/**
	 * @throws Exception
	 */
	public function deleteCategory(string $name) : void{
		if(!isset($this->categories[$name])){
			throw new Exception("ERROR: The category does not exist");
		}
		$file = $this->directory . DIRECTORY_SEPARATOR . 'categories' . DIRECTORY_SEPARATOR . $name . '.json';
		if(file_exists($file)){
			unlink($file);
		}

		unset($this->categories[$name]);
	}

	public function saveCategory(Category $category) : void{
		$data = [
			'name' => $category->getName(),
			'prefix' => $category->getPrefix(),
			'permission' => $category->getPermission(),
			'icon' => $category->getIcon(),
			'kits' => array_map(fn ($kit) => $kit->getName(), $category->getKits()),
		];

		file_put_contents($this->directory . DIRECTORY_SEPARATOR . 'categories' . DIRECTORY_SEPARATOR . $category->getName() . '.json', json_encode($data, JSON_PRETTY_PRINT));
	}

	private function loadCategories() : void{
		$categoryDirectory = $this->directory . DIRECTORY_SEPARATOR . 'categories';

		if(!is_dir($categoryDirectory)){
			mkdir($categoryDirectory, 0755, true);
		}

		$files = glob($categoryDirectory . DIRECTORY_SEPARATOR . '*.json');
		$this->categories = [];

		if(is_array($files) && count($files) > 0){
			foreach($files as $file){
				$json = file_get_contents($file);
				if($json === false){
					Server::getInstance()->getLogger()->warning("ERROR: Failed to read file: " . $file);
					continue;
				}

				$data = json_decode($json, true);
				if(!is_array($data)){
					Server::getInstance()->getLogger()->warning("ERROR: Invalid JSON format in file:" . $file);
					continue;
				}

				if(!isset($data['name'], $data['prefix']) || !is_string($data['name']) || !is_string($data['prefix'])){
					Server::getInstance()->getLogger()->warning("ERROR: Missing or invalid 'name' or 'prefix' in file:" . $file);
					continue;
				}

				$permission = isset($data['permission']) && is_string($data['permission']) ? $data['permission'] : null;
				$icon = isset($data['icon']) && is_string($data['icon']) ? $data['icon'] : null;
				$category = new Category($data['name'], $data['prefix'], $permission, $icon);

				if(isset($data['kits']) && is_array($data['kits'])){
					foreach($data['kits'] as $kitName){
						if(!is_string($kitName)){
							continue;
						}
						$kit = Main::getInstance()->getKitManager()->getKit($kitName);
						if($kit === null){
							Server::getInstance()->getLogger()->warning("ERROR: Kit " . $kitName . " not found for category " . $data['name'] . " in file: " . $file);
							continue;
						}

						$category->addKit($kit);
					}
				}
				$this->categories[$category->getName()] = $category;
			}
		}
	}
}
