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
use DaPigGuy\libPiggyEconomy\exceptions\MissingProviderDependencyException;
use DaPigGuy\libPiggyEconomy\exceptions\UnknownProviderException;
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
use function is_array;

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

    public function onLoad(): void {
        self::setInstance($this);
    }

    /**
     * @throws UnknownProviderException
     * @throws HookAlreadyRegistered
     * @throws MissingProviderDependencyException
     * @throws Exception
     */
    public function onEnable(): void {
        $this->initializeHooks();
        $this->saveResources();
        $this->initializeConfig();
        $this->loadTranslations();
        $this->initializeDatabase();
        $this->initializeManagers();
        $this->registerCommandsAndEvents();
    }


    /**
     * @throws HookAlreadyRegistered
     */
    private function initializeHooks(): void {
        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
    }

    private function initializeConfig(): void {
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

    private function initializeDatabase(): void {
        $this->database = libasynql::create(
            $this,
            $this->config->get("database"),
            [
                "sqlite" => "database/sqlite.sql",
                "mysql" => "database/mysql.sql"
            ]
        );

        foreach (["kits", "categories", "cooldowns", "category_kits"] as $table) {
            $this->database->executeGeneric("{$table}.table");
        }
    }

    /**
     * @throws UnknownProviderException
     * @throws MissingProviderDependencyException
     * @throws Exception
     */
    private function initializeManagers(): void {
        try {
            $this->kitManager = new KitManager();
        } catch (Exception $e) {
            $this->getLogger()->error($e->getMessage());
        }
        $this->categoryManager = new CategoryManager();
        $this->cooldownManager = new CooldownManager();

        libPiggyEconomy::init();
        $providerInfo = $this->config->get("economy");
        if (!is_array($providerInfo)) {
            throw new Exception("ERROR: Economy provider information must be an array in the configuration");
        }
        $this->economyProvider = libPiggyEconomy::getProvider($providerInfo);
    }

    private function registerCommandsAndEvents(): void {
        $this->getServer()->getCommandMap()->register("KitSystem", new KitSystemCommand($this));
        $this->getServer()->getPluginManager()->registerEvents(new ClaimListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new JoinListener(), $this);
    }

    /**
     * @throws Exception
     */
    private function loadTranslations(): void {
        $this->translator = new Translator($this);
        foreach (glob($this->getDataFolder() . "languages" . DIRECTORY_SEPARATOR . "*.ini") as $file) {
            $locale = basename($file, ".ini");
            $content = parse_ini_file($file, false, INI_SCANNER_RAW);
            if (!is_array($content)) {
                throw new Exception("Missing or inaccessible required resource files");
            }
            $this->translator->registerLanguage(new Language($locale, array_map('stripcslashes', $content)));
        }
        $this->translator->setDefaultLanguage(
            $this->translator->getLanguage($this->config->get("default-language", self::DEFAULT_LANGUAGE))
        );
    }

    public function saveResources(): void {
        $this->saveResource("config.yml");
        foreach (["en_US", "es_MX", "fr_FR", "id_ID", "it_IT", "ja_JP", "pt_BR"] as $lang) {
            $this->saveResource("languages/{$lang}.ini", true);
        }
    }

    public function onDisable(): void {
        if (isset($this->database)) $this->database->close();
    }

    public function getKitManager(): KitManager {
        return $this->kitManager;
    }

    public function getCategoryManager(): CategoryManager {
        return $this->categoryManager;
    }

    public function getCooldownManager(): CooldownManager {
        return $this->cooldownManager;
    }

    public function getEconomyProvider(): EconomyProvider {
        return $this->economyProvider;
    }

    public function getTranslator(): Translator {
        return $this->translator;
    }

    public function getDatabase(): DataConnector {
        return $this->database;
    }
}
