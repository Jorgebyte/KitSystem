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

namespace Jorgebyte\KitSystem;

use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use DaPigGuy\libPiggyEconomy\libPiggyEconomy;
use DaPigGuy\libPiggyEconomy\providers\EconomyProvider;
use Exception;
use IvanCraft623\languages\Language;
use IvanCraft623\languages\Translator;
use Jorgebyte\KitSystem\command\KitSystemCommand;
use Jorgebyte\KitSystem\cooldown\CooldownManager;
use Jorgebyte\KitSystem\kit\category\CategoryManager;
use Jorgebyte\KitSystem\kit\KitManager;
use Jorgebyte\KitSystem\listener\ClaimListener;
use Jorgebyte\KitSystem\listener\JoinListener;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;
use function array_map;
use function basename;
use function glob;
use function is_array;
use function is_dir;
use function parse_ini_file;
use function pathinfo;
use function scandir;
use const DIRECTORY_SEPARATOR;
use const INI_SCANNER_RAW;
use const PATHINFO_EXTENSION;

/**
 * Main plugin class for KitSystem.
 * Handles bootstrap of configuration, language, economy, managers, and events.
 */
final class Main extends PluginBase{
	use SingletonTrait;

	private const DEFAULT_LANGUAGE = "en_US";

	private DataConnector $database;
	private KitManager $kitManager;
	private CategoryManager $categoryManager;
	private CooldownManager $cooldownManager;
	private EconomyProvider $economyProvider;
	private Config $config;
	private Translator $translator;

	public function onLoad() : void{
		self::setInstance($this);
	}

	/**
	 * Plugin startup sequence.
	 *
	 * @throws Exception
	 */
	public function onEnable() : void{
		$this->initializeHooks();
		$this->saveResources();
		$this->initializeConfig();
		$this->loadTranslations();
		$this->initializeDatabase();
		$this->initializeManagers();
		$this->registerCommandsAndEvents();
	}

	/**
	 * Registers any required hooks like PacketHooker and InvMenu.
	 * @throws HookAlreadyRegistered
	 */
	private function initializeHooks() : void{
		if(!PacketHooker::isRegistered()){
			PacketHooker::register($this);
		}
		if(!InvMenuHandler::isRegistered()){
			InvMenuHandler::register($this);
		}
	}

	/**
	 * Loads or generates the default config with fallback defaults.
	 */
	private function initializeConfig() : void{
		$defaults = [
			"default-language" => self::DEFAULT_LANGUAGE,
			"database" => [
				"type" => "sqlite",
				"sqlite" => ["file" => "data.sqlite"],
				"mysql" => [
					"host" => "127.0.0.1",
					"username" => "root",
					"password" => "",
					"schema" => "your_schema"
				],
				"worker-limit" => 1
			],
			"economy" => ["provider" => "bedrockeconomy"],
			"chest-kit" => "chest",
			"enable-starterkit" => false,
			"starterkit" => "StarterKit"
		];

		$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, $defaults);
	}

	/**
	 * Initializes libasynql and runs schema setup.
	 */
	private function initializeDatabase() : void{
		$this->database = libasynql::create(
			$this,
			$this->config->get("database"),
			[
				"sqlite" => "database/sqlite.sql",
				"mysql" => "database/mysql.sql"
			]
		);

		foreach(["kits", "categories", "cooldowns", "category_kits"] as $table){
			$this->database->executeGeneric("{$table}.table");
		}
	}

	/**
	 * Initializes core plugin managers and economy provider.
	 *
	 * @throws Exception
	 */
	private function initializeManagers() : void{
        $this->kitManager = new KitManager();
        $this->categoryManager = new CategoryManager();
        $this->cooldownManager = new CooldownManager();

        libPiggyEconomy::init();

        $providerInfo = $this->config->get("economy");
        if (!is_array($providerInfo)) {
            throw new \RuntimeException("Economy configuration must be an array.");
        }

        $this->economyProvider = libPiggyEconomy::getProvider($providerInfo);
	}

	/**
	 * Registers plugin commands and event listeners.
	 */
	private function registerCommandsAndEvents() : void{
		$this->getServer()->getCommandMap()->register("KitSystem", new KitSystemCommand($this));
		$this->getServer()->getPluginManager()->registerEvents(new ClaimListener(), $this);
		$this->getServer()->getPluginManager()->registerEvents(new JoinListener(), $this);
	}

	/**
	 * Loads all language translations from the /languages folder.
	 *
	 * @throws Exception
	 */
	private function loadTranslations() : void{
        $this->translator = new Translator($this);

        $files = glob($this->getDataFolder() . "languages" . DIRECTORY_SEPARATOR . "*.ini");
        if (!is_array($files)) {
            throw new \RuntimeException("Failed to read language directory");
        }

        foreach ($files as $file) {
            $locale = basename($file, ".ini");
            $content = parse_ini_file($file, false, INI_SCANNER_RAW);

            if (!is_array($content)) {
                throw new \RuntimeException("Invalid language file: {$file}");
            }

            $this->translator->registerLanguage(new Language($locale, array_map('stripcslashes', $content)));
        }

        $defaultLocale = (string) $this->config->get("default-language", self::DEFAULT_LANGUAGE);
        $defaultLang = $this->translator->getLanguage($defaultLocale);

        if ($defaultLang !== null) {
            $this->translator->setDefaultLanguage($defaultLang);
        } else {
            $this->getLogger()->warning("Default language '{$defaultLocale}' not found");
        }
	}

	/**
	 * Saves default plugin resources like config and language files.
	 */
	public function saveResources() : void{
		$this->saveResource("config.yml");

		$languageDir = $this->getFile() . "resources" . DIRECTORY_SEPARATOR . "languages";
		if(!is_dir($languageDir)){
			$this->getLogger()->warning("No language resource directory found");
			return;
		}

        $resourceLanguages = scandir($languageDir);
        if (!is_array($resourceLanguages)) {
            $this->getLogger()->warning("Failed to read language resource directory");
            return;
        }

        foreach($resourceLanguages as $file){
			if(pathinfo($file, PATHINFO_EXTENSION) !== "ini")continue;

			$this->saveResource("languages/" . $file, true);
		}
	}

	public function onDisable() : void{
		if(isset($this->database))$this->database->close();
	}

	// Accessors

	public function getKitManager() : KitManager{
		return $this->kitManager;
	}

	public function getCategoryManager() : CategoryManager{
		return $this->categoryManager;
	}

	public function getCooldownManager() : CooldownManager{
		return $this->cooldownManager;
	}

	public function getEconomyProvider() : EconomyProvider{
		return $this->economyProvider;
	}

	public function getTranslator() : Translator{
		return $this->translator;
	}

	public function getDatabase() : DataConnector{
		return $this->database;
	}
}
