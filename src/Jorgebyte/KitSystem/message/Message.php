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

namespace Jorgebyte\KitSystem\message;

use Exception;
use JsonException;
use pocketmine\utils\Config;
use function is_string;
use function str_replace;

class Message{
	private Config $messages;
	private string $prefix;

	/**
	 * @throws Exception
	 */
	public function __construct(string $dataFolder){
		$this->messages = new Config($dataFolder . "message.yml", Config::YAML);
		$this->loadMessages();
        $this->prefix = is_string($this->messages->get(MessageKey::PREFIX)) ? $this->messages->get(MessageKey::PREFIX) : "§8(§bKitSystem§8)§r ";
    }

	/**
	 * @throws JsonException
	 */
	private function loadMessages() : void{
		$defaults = [
			MessageKey::KIT_CLAIMED => "{prefix}§aYou have claimed the kit:§e {kitname}",
			MessageKey::STARTERKIT_RECEIVED => "{prefix}§aYou have received the starter kit: {kit}.",
			MessageKey::OPEN_KIT => "{prefix}§aYou have opened your kit",
			MessageKey::COOLDOWN_ACTIVE => "{prefix}§cYou have a waiting time of: {time}",
			MessageKey::FULL_INV => "{prefix}§cyou don't have enough space",
			MessageKey::FULL_INV_CHEST => "Your inventory was full, so the kit chest was dropped at your location",
			MessageKey::FAILED_MONEY => "{prefix}§cFailed to deduct money",
			MessageKey::LACK_OF_MONEY => "{prefix}§cYou do not have enough money to claim this kit. Price: {kitprice}",
			MessageKey::GIVEALL_KIT_BROADCAST => "{prefix}§aThe§e {player}§a has given§e {quantity}§a of Kits:§e {kit}",
			MessageKey::WITHOUT_PERMISSIONS => "{prefix}§cYou do not have sufficient permissions"
		];

		$updated = false;
		foreach($defaults as $key => $defaultValue){
			if(!$this->messages->exists($key)){
				$this->messages->set($key, $defaultValue);
				$updated = true;
			}
		}
		if($updated)$this->messages->save();
	}

	public function getMessage(string $key, array $replacements = [], bool $includePrefix = true) : string{
        $message = is_string($message = $this->messages->get($key, "")) ? $message : "";

        if($includePrefix){
			$message = str_replace("{prefix}", $this->prefix, $message);
		}
		foreach($replacements as $search => $replace){
			$message = str_replace("{" . $search . "}", (string) $replace, $message);
		}
		return $message;
	}

	public function getPrefix() : string{
		return $this->prefix;
	}
}
