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
use const SQLITE3_TEXT;

class RemoveCooldownTask extends AsyncTask{
	private string $filePath;
	private string $uuid;
	private string $kitName;

	public function __construct(string $filePath, string $uuid, string $kitName){
		$this->filePath = $filePath;
		$this->uuid = $uuid;
		$this->kitName = $kitName;
	}

	/**
	 * @throws \Exception
	 */
	public function onRun() : void{
		$db = new \SQLite3($this->filePath);
		$stmt = $db->prepare("DELETE FROM cooldowns WHERE uuid = :uuid AND kit = :kit");

		if($stmt === false){
			throw new \Exception("Failed to prepare statement: " . $db->lastErrorMsg());
		}

		$stmt->bindValue(":uuid", $this->uuid, SQLITE3_TEXT);
		$stmt->bindValue(":kit", $this->kitName, SQLITE3_TEXT);
		$stmt->close();
		$db->close();
	}
}
