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

class RemoveCooldownTask extends AsyncTask
{
    private string $filePath;
    private string $uuid;
    private string $kitName;

    public function __construct(string $filePath, string $uuid, string $kitName)
    {
        $this->filePath = $filePath;
        $this->uuid = $uuid;
        $this->kitName = $kitName;
    }

    /**
     * @throws \Exception
     */
    public function onRun(): void
    {
        $db = new \SQLite3($this->filePath);
        $stmt = $db->prepare("DELETE FROM cooldowns WHERE uuid = :uuid AND kit = :kit");

        if ($stmt === false) {
            throw new \Exception("Failed to prepare statement: " . $db->lastErrorMsg());
        }

        $stmt->bindValue(":uuid", $this->uuid, SQLITE3_TEXT);
        $stmt->bindValue(":kit", $this->kitName, SQLITE3_TEXT);
        $stmt->close();
        $db->close();
    }
}
