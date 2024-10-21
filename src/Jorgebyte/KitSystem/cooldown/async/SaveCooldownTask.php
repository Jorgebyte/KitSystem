<?php

declare(strict_types=1);

/*
 *    KitSystem
 *    Api: 5.3.0
 *    Version: 1.0.0
 *    Author: Jorgebyte
 */

namespace Jorgebyte\KitSystem\cooldown\async;

use pocketmine\scheduler\AsyncTask;

class SaveCooldownTask extends AsyncTask
{
    private string $filePath;
    private string $uuid;
    private string $kitName;
    private int $expiryTime;

    public function __construct(string $filePath, string $uuid, string $kitName, int $expiryTime)
    {
        $this->filePath = $filePath;
        $this->uuid = $uuid;
        $this->kitName = $kitName;
        $this->expiryTime = $expiryTime;
    }

    /**
     * @throws \Exception
     */
    public function onRun(): void
    {
        $db = new \SQLite3($this->filePath);
        $db->exec("CREATE TABLE IF NOT EXISTS cooldowns (uuid TEXT, kit TEXT, expiry INTEGER, PRIMARY KEY (uuid, kit))");
        $stmt = $db->prepare("INSERT OR REPLACE INTO cooldowns (uuid, kit, expiry) VALUES (:uuid, :kit, :expiry)");

        if ($stmt === false) {
            throw new \Exception("Failed to prepare statement: " . $db->lastErrorMsg());
        }

        $stmt->bindValue(":uuid", $this->uuid, SQLITE3_TEXT);
        $stmt->bindValue(":kit", $this->kitName, SQLITE3_TEXT);
        $stmt->bindValue(":expiry", $this->expiryTime, SQLITE3_INTEGER);
        $stmt->close();
        $db->close();
    }
}
