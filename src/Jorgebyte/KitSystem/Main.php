<?php

declare(strict_types=1);

/*
 *    KitSystem
 *    Api: 5.3.0
 *    Version: 1.0.0
 *    Author: Jorgebyte
 */

namespace Jorgebyte\KitSystem;

use CortexPE\Commando\PacketHooker;
use DaPigGuy\libPiggyEconomy\libPiggyEconomy;
use DaPigGuy\libPiggyEconomy\providers\EconomyProvider;
use Exception;
use Jorgebyte\KitSystem\command\KitSystemCommand;
use Jorgebyte\KitSystem\cooldown\CooldownManager;
use Jorgebyte\KitSystem\kit\category\CategoryManager;
use Jorgebyte\KitSystem\kit\KitManager;
use Jorgebyte\KitSystem\listener\ClaimListener;
use Jorgebyte\KitSystem\message\Message;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase
{
    use SingletonTrait;

    protected KitManager $kitManager;
    protected CategoryManager $categoryManager;
    protected CooldownManager $cooldownManager;
    protected EconomyProvider $economyProvider;
    protected Message $message;

    public function onLoad(): void
    {
        self::setInstance($this);
    }

    public function onEnable(): void
    {
        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
        $this->saveDefaultConfig();
        $this->saveResource("message.yml");

        try {
            $this->kitManager = new KitManager($this->getDataFolder());
        } catch (Exception $e) {
            $this->getLogger()->error($e->getMessage());
        }
        $this->categoryManager = new CategoryManager($this->getDataFolder());
        try {
            $this->message = new Message($this->getDataFolder());
        } catch (Exception $e) {
            $this->getLogger()->error($e->getMessage());
        }
        $this->cooldownManager = new CooldownManager($this->getDataFolder());

        libPiggyEconomy::init();
        $providerInfo = $this->getConfig()->get("economy");
        if (!is_array($providerInfo)) {
            throw new Exception("ERROR: Economy provider information must be an array in the configuration");
        }
        $this->economyProvider = libPiggyEconomy::getProvider($providerInfo);

        $this->getServer()->getCommandMap()->register("KitSystem", new KitSystemCommand($this));
        $this->getServer()->getPluginManager()->registerEvents(new ClaimListener(), $this);
    }

    public function getKitManager(): KitManager
    {
        return $this->kitManager;
    }

    public function getCategoryManager(): CategoryManager
    {
        return $this->categoryManager;
    }

    public function getCooldownManager(): CooldownManager
    {
        return $this->cooldownManager;
    }

    public function getEconomyProvider(): EconomyProvider
    {
        return $this->economyProvider;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

    public function onDisable(): void
    {
        $this->cooldownManager->saveAllCooldowns();
        $this->kitManager->saveKits();
    }
}