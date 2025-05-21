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

namespace Jorgebyte\KitSystem\kit;

use Exception;
use Jorgebyte\KitSystem\kit\category\Category;
use Jorgebyte\KitSystem\Main;
use Jorgebyte\KitSystem\util\LangKey;
use kim\present\utils\itemserialize\SnbtItemSerializer;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use pocketmine\promise\Promise;
use pocketmine\promise\PromiseResolver;
use RuntimeException;
use function array_values;
use function is_string;

/**
 * Manages the creation, storage, and retrieval of kits.
 * Handles data persistence and player kit distribution.
 */
final class KitManager{
	/** @var array<string, Kit> In-memory cache of all registered kits. */
	private array $kits = [];

	/**
	 * KitManager constructor.
	 *
	 * Loads all kits asynchronously on startup.
	 *
	 * @throws RuntimeException|Exception
	 */
	public function __construct(){
		$this->loadKits();
	}

	/**
	 * Registers a new kit to the system and persists it.
	 * Optionally associates it with a category.
	 */
	public function addKit(Kit $kit, ?Category $category = null) : void{
		$this->kits[$kit->getName()] = $kit;
		if($category !== null){
			$category->addKit($kit);
			Main::getInstance()->getDatabase()->executeChange(
				'category_kits.insert',
				[
					'category_name' => $category->getName(),
					'kit_name' => $kit->getName()
				]
			);
		}
		$this->saveKit($kit);
	}

	/**
	 * Retrieves a registered kit by name.
	 */
	public function getKit(string $name) : ?Kit{
		return $this->kits[$name] ?? null;
	}

	/**
	 * Deletes a kit and removes it from its category if necessary.
	 */
	public function deleteKit(string $name) : void{
		foreach(Main::getInstance()->getCategoryManager()->getAllCategories() as $category){
			if($category->hasKit($name)){
				$category->removeKit($name);
				Main::getInstance()->getCategoryManager()->persistCategory($category);
			}
		}

		if(isset($this->kits[$name])){
			unset($this->kits[$name]);
			Main::getInstance()->getDatabase()->executeChange('kits.delete', ['name' => $name]);
		}
	}

	/**
	 * Creates and registers a new kit. Throws if a kit with the same name already exists.
	 *
	 * @throws RuntimeException|Exception
	 */
	public function createKit(
		string $name,
		string $prefix,
		array $armorContents,
		array $inventoryContents,
		?int $cooldown = null,
		?float $price = null,
		?string $permission = null,
		?string $icon = null,
		bool $storeInChest = true,
		?string $categoryName = null
	) : void{
		if($this->kitExists($name)){
			throw new RuntimeException("Kit '$name' already exists");
		}
		$kit = new Kit(
			$name,
			$prefix,
			$armorContents,
			$inventoryContents,
			$cooldown,
			$price,
			$permission,
			$icon,
			$storeInChest
		);
		$this->addKit($kit);
		if($categoryName){
			Main::getInstance()->getCategoryManager()->addKitToCategory($kit, $categoryName);
		}
	}

	/**
	 * Gives a kit to a player in the form of a chest item.
	 */
	public function giveKitChest(Player $player, Kit $kit) : void{
		$config = Main::getInstance()->getConfig();
		$translator = Main::getInstance()->getTranslator();
		$kitChestString = $config->get('chest-kit');

		if(!is_string($kitChestString)){
			$player->sendMessage($translator->translate($player, LangKey::INVALID_CHEST_KIT->value));
			return;
		}

		$item = StringToItemParser::getInstance()->parse($kitChestString)
			?? throw new RuntimeException("Invalid chest-kit item");

		$item->setCustomName($kit->getPrefix());
		$namedTag = $item->getNamedTag();
		$namedTag->setString('kitName', $kit->getName());
		$item->setNamedTag($namedTag);

		$this->distributeItemSafely($player, $item, LangKey::FULL_INV_CHEST->value);
	}

	/**
	 * Gives the kit's items and armor directly to a player's inventory.
	 */
	public function giveKitItems(Player $player, Kit $kit) : void{
		$inventory = $player->getInventory();
		$armorInventory = $player->getArmorInventory();

		foreach($kit->getItems() as $item){
			if($item !== null){
				$inventory->addItem(clone $item);
			}
		}

		foreach($kit->getArmor() as $slot => $armorPiece){
			if($armorPiece !== null){
				$armorInventory->setItem($slot, clone $armorPiece);
			}
		}
	}

	/**
	 * Loads all kits asynchronously from the database into memory.
	 *
	 * @return Promise<array<string, Kit>>
	 */
	public function loadKits() : Promise{
		$resolver = new PromiseResolver();

		Main::getInstance()->getDatabase()->executeSelect('kits.get_all', [], function(array $rows) use ($resolver) : void{
			foreach($rows as $row){
				$this->kits[$row['name']] = $this->deserializeKit($row);
			}
			$resolver->resolve($this->kits);
		}, fn($error) => $resolver->reject($error));

		return $resolver->getPromise();
	}
	/**
	 * Saves a kit's current state to the database.
	 */
	public function saveKit(Kit $kit) : void{
		Main::getInstance()->getDatabase()->executeChange('kits.insert', $this->serializeKit($kit));
	}

	/**
	 * Gets all registered kits.
	 *
	 * @return Kit[]
	 */
	public function getAllKits() : array{
		return array_values($this->kits);
	}

	/**
	 * Checks whether a kit exists by name.
	 */
	public function kitExists(string $name) : bool{
		return isset($this->kits[$name]);
	}

	/**
	 * Attempts to add an item to a player's inventory or drops it if full.
	 */
	private function distributeItemSafely(Player $player, Item $item, string $messageKey) : void{
		$inventory = $player->getInventory();
		$translator = Main::getInstance()->getTranslator();

		if($inventory->canAddItem($item)){
			$inventory->addItem($item);
		} else{
			$player->getWorld()->dropItem($player->getPosition(), $item);
			$player->sendMessage($translator->translate($player, $messageKey));
		}
	}

	/**
	 * Converts a Kit object into a serializable array for database storage.
	 */
	private function serializeKit(Kit $kit) : array{
		return [
			'name' => $kit->getName(),
			'prefix' => $kit->getPrefix(),
			'armor' => SnbtItemSerializer::serializeList($kit->getArmor()),
			'items' => SnbtItemSerializer::serializeList($kit->getItems()),
			'cooldown' => $kit->getCooldown(),
			'price' => $kit->getPrice(),
			'permission' => $kit->getPermission(),
			'icon' => $kit->getIcon(),
			'store_in_chest' => $kit->shouldStoreInChest() ? 1 : 0
		];
	}

	/**
	 * Reconstructs a Kit object from database row data.
	 */
	private function deserializeKit(array $data) : Kit{
		return new Kit(
			$data['name'],
			$data['prefix'],
			SnbtItemSerializer::deserializeList($data['armor']),
			SnbtItemSerializer::deserializeList($data['items']),
			$data['cooldown'] ?? null,
			$data['price'] ?? null,
			$data['permission'] ?? null,
			$data['icon'] ?? null,
			(bool) ($data['store_in_chest'] ?? false)
		);
	}
}
