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

namespace Jorgebyte\KitSystem;

use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use DaPigGuy\libPiggyEconomy\exceptions\MissingProviderDependencyException;
use DaPigGuy\libPiggyEconomy\exceptions\UnknownProviderException;
use DaPigGuy\libPiggyEconomy\libPiggyEconomy;
use DaPigGuy\libPiggyEconomy\providers\EconomyProvider;
use Exception;
use Jorgebyte\KitSystem\command\KitSystemCommand;
use Jorgebyte\KitSystem\cooldown\CooldownManager;
use Jorgebyte\KitSystem\kit\category\CategoryManager;
use Jorgebyte\KitSystem\kit\KitManager;
use Jorgebyte\KitSystem\listener\ClaimListener;
use Jorgebyte\KitSystem\listener\JoinListener;
use Jorgebyte\KitSystem\message\Message;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use function is_array;
use function is_string;

class Main extends PluginBase{
	use SingletonTrait;

	protected KitManager $kitManager;
	protected CategoryManager $categoryManager;
	protected CooldownManager $cooldownManager;
	protected EconomyProvider $economyProvider;
	protected Message $message;
	protected Config $config;

	public function onLoad() : void{
		self::setInstance($this);
	}

	/**
	 * @throws UnknownProviderException
	 * @throws HookAlreadyRegistered
	 * @throws MissingProviderDependencyException
	 * @throws Exception
	 */
	public function onEnable() : void{
		if(!PacketHooker::isRegistered()){
			PacketHooker::register($this);
		}
		if(!InvMenuHandler::isRegistered()){
			InvMenuHandler::register($this);
		}
		$this->saveDefaultConfig();
		$this->saveResource("message.yml");
		$defaults = [
			"economy" => [
				"provider" => "bedrockeconomy"
			],
			"chest-kit" => "chest",
			"enable-starterkit" => false,
			"starterkit" => "StarterKit"
		];

		$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, $defaults);

		try{
			$this->kitManager = new KitManager($this->getDataFolder());
		} catch(Exception $e){
			$this->getLogger()->error($e->getMessage());
		}
		$this->categoryManager = new CategoryManager($this->getDataFolder());
		try{
			$this->message = new Message($this->getDataFolder());
		} catch(Exception $e){
			$this->getLogger()->error($e->getMessage());
		}
		$this->cooldownManager = new CooldownManager($this->getDataFolder());

		libPiggyEconomy::init();
		$providerInfo = $this->getConfig()->get("economy");
		if(!is_array($providerInfo)){
			throw new Exception("ERROR: Economy provider information must be an array in the configuration");
		}
		$this->economyProvider = libPiggyEconomy::getProvider($providerInfo);

		$starterKitName = $this->getConfig()->get("starterkit");
		if((bool) $this->getConfig()->get("enable-starterkit")){
			if(!is_string($starterKitName)){
				throw new Exception("ERROR: Starter kit name must be a string");
			}
			if(!$this->kitManager->kitExists($starterKitName)){
				throw new Exception("ERROR: The starter kit: " . $starterKitName . " does not exist.");
			}
		}

		$this->getServer()->getCommandMap()->register("KitSystem", new KitSystemCommand($this));
		$this->getServer()->getPluginManager()->registerEvents(new ClaimListener(), $this);
		$this->getServer()->getPluginManager()->registerEvents(new JoinListener(), $this);
	}

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

	public function getMessage() : Message{
		return $this->message;
	}

	public function onDisable() : void{
		$this->getCooldownManager()->saveAllCooldowns();
		$this->getKitManager()->saveKits();
	}
}
