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

namespace Jorgebyte\KitSystem\kit;

use Exception;
use Jorgebyte\KitSystem\kit\category\Category;
use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\message\MessageKey;
use kim\present\utils\itemserialize\ItemSerializeUtils;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use function array_values;
use function count;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function glob;
use function is_array;
use function is_readable;
use function is_string;
use function json_decode;
use function json_encode;
use function unlink;
use const DIRECTORY_SEPARATOR;
use const JSON_PRETTY_PRINT;

class KitManager{
	private array $kits = [];
	private string $directory;

	/**
	 * @throws Exception
	 */
	public function __construct(string $kitsDirectory){
		$this->directory = $kitsDirectory;
		$this->loadKits();
	}

	public function addKit(Kit $kit, ?Category $category = null) : void{
		$this->kits[$kit->getName()] = $kit;
		$category?->addKit($kit);
		$this->saveKit($kit);
	}

	public function getKit(string $name) : ?Kit{
		return $this->kits[$name] ?? null;
	}

	public function deleteKit(string $name) : void{
		foreach(Main::getInstance()->getCategoryManager()->getAllCategories() as $category){
			if($category->hasKit($name)){
				$category->removeKit($name);
				Main::getInstance()->getCategoryManager()->saveCategory($category);
			}
		}

		if(isset($this->kits[$name])){
			unset($this->kits[$name]);
			unlink($this->directory . $name . '.json');
		}
	}

	/**
	 * @throws Exception
	 */
	public function createKit(string $name, string $prefix, array $armorContents, array $inventoryContents, ?int $cooldown = null, ?float $price = null, ?string $permission = null, ?string $icon = null, bool $storeInChest = true, ?string $categoryName = null) : void{
		if($this->kitExists($name)){
			throw new Exception("A kit with this name already exists!");
		}

		$kit = new Kit($name, $prefix, $armorContents, $inventoryContents, $cooldown, $price, $permission, $icon, $storeInChest);
		$this->addKit($kit);

		if($categoryName !== null){
			Main::getInstance()->getCategoryManager()->addKitToCategory($kit, $categoryName);
		}
	}

	public function giveKitChest(Player $player, Kit $kit) : void{
		$kitChestString = Main::getInstance()->getConfig()->get("chest-kit");

		if(!is_string($kitChestString)){
			$player->sendMessage(TextFormat::RED . "ERROR: Invalid item configuration for kit chest!");
			return;
		}

		$item = StringToItemParser::getInstance()->parse($kitChestString);

		if($item === null){
			$player->sendMessage(TextFormat::RED . "ERROR: Invalid item for kit chest!");
			return;
		}

		$item->setCustomName($kit->getPrefix());

		$namedTag = $item->getNamedTag();
		if($namedTag->getTag("kitName") === null){
			$namedTag->setString("kitName", $kit->getName());
		}
		$item->setNamedTag($namedTag);

		if($player->getInventory()->canAddItem($item)){
			$player->getInventory()->addItem($item);
		} else{
			$position = $player->getPosition();
			$player->getWorld()->dropItem($position, $item);
			$player->sendMessage(Main::getInstance()->getMessage()->getMessage(MessageKey::FULL_INV_CHEST));
		}
	}

	public function giveKitItems(Player $player, Kit $kit) : void{
		$inventory = $player->getInventory();
		$armorInventory = $player->getArmorInventory();
		$items = $kit->getItems();
		$armor = $kit->getArmor();

		foreach($items as $item){
			if($item !== null){
				$inventory->addItem($item);
			}
		}

		foreach($armor as $i => $armorPiece){
			if($armorPiece !== null){
				$armorInventory->setItem($i, $armorPiece);
			}
		}
	}

	/**
	 * @throws Exception
	 */
	private function loadKits() : void{
		$files = glob($this->directory . DIRECTORY_SEPARATOR . '*.json');

		if(!is_array($files) || count($files) === 0){
			Server::getInstance()->getLogger()->info("No JSON files found in directory: " . $this->directory . " You can create new kits");
			return;
		}

		foreach($files as $file){
			$this->processFile($file);
		}
	}

	public function saveKit(Kit $kit) : void{
		$data = $this->serializeKit($kit);
		file_put_contents($this->directory . DIRECTORY_SEPARATOR . $kit->getName() . '.json', json_encode($data, JSON_PRETTY_PRINT));
	}

	public function saveKits() : void{
		foreach($this->getAllKits() as $kit){
			$this->saveKit($kit);
		}
	}

	public function getAllKits() : array{
		return array_values($this->kits);
	}

	public function kitExists(string $name) : bool{
		return isset($this->kits[$name]);
	}

	private function serializeKit(Kit $kit) : array{
		return [
			'name' => $kit->getName(),
			'prefix' => $kit->getPrefix(),
			'armor' => $this->serializeItems($kit->getArmor()),
			'items' => $this->serializeItems($kit->getItems()),
			'cooldown' => $kit->getCooldown(),
			'price' => $kit->getPrice(),
			'permission' => $kit->getPermission(),
			'icon' => $kit->getIcon(),
			'storeInChest' => $kit->shouldStoreInChest(),
		];
	}

	private function deserializeKit(array $data) : Kit{
		return new Kit(
			$data['name'],
			$data['prefix'],
			$this->deserializeItems($data['armor']),
			$this->deserializeItems($data['items']),
			$data['cooldown'] ?? null,
			$data['price'] ?? null,
			$data['permission'] ?? null,
			$data['icon'] ?? null,
			$data['storeInChest'] ?? true
		);
	}

	private function serializeItems(array $items) : string{
		return ItemSerializeUtils::snbtSerializeList($items);
	}

	private function deserializeItems(string $serializedItems) : array{
		return ItemSerializeUtils::snbtDeserializeList($serializedItems);
	}

	/**
	 * @throws Exception
	 */
	private function processFile(string $file) : void{
		if(!$this->isFileReadable($file)){
			return;
		}

		$content = file_get_contents($file);
		if($content === false){
			throw new Exception("ERROR: reading file: " . $file);
		}

		$data = json_decode($content, true);
		if(!is_array($data)){
			throw new Exception("ERROR: decoding JSON from file: " . $file);
		}

		$kit = $this->deserializeKit($data);
		$this->kits[$kit->getName()] = $kit;
	}

	private function isFileReadable(string $file) : bool{
		return file_exists($file) && is_readable($file);
	}
}
