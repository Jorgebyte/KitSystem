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

namespace Jorgebyte\KitSystem\cooldown\async;

use pocketmine\scheduler\AsyncTask;
use const SQLITE3_INTEGER;

class SaveCooldownTask extends AsyncTask{
	private string $filePath;
	private string $uuid;
	private string $kitName;
	private int $expiryTime;

	public function __construct(string $filePath, string $uuid, string $kitName, int $expiryTime){
		$this->filePath = $filePath;
		$this->uuid = $uuid;
		$this->kitName = $kitName;
		$this->expiryTime = $expiryTime;
	}

	/**
	 * @throws \Exception
	 */
	public function onRun() : void{
		$db = new \SQLite3($this->filePath);
		$db->exec("CREATE TABLE IF NOT EXISTS cooldowns (uuid TEXT, kit TEXT, expiry INTEGER, PRIMARY KEY (uuid, kit))");
		$stmt = $db->prepare("INSERT OR REPLACE INTO cooldowns (uuid, kit, expiry) VALUES (:uuid, :kit, :expiry)");

		if($stmt === false){
			throw new \Exception("Failed to prepare statement: " . $db->lastErrorMsg());
		}

		$stmt->bindValue(":uuid", $this->uuid);
		$stmt->bindValue(":kit", $this->kitName);
		$stmt->bindValue(":expiry", $this->expiryTime, SQLITE3_INTEGER);
		$stmt->execute();
		$stmt->close();
		$db->close();
	}
}
