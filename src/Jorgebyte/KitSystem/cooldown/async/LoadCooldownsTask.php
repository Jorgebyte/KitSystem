<?php

declare(strict_types=1);

/*
 *    KitSystem
 *    Api: 5.3.0
 *    Version: 1.0.0
 *    Author: Jorgebyte
 */

namespace Jorgebyte\KitSystem\cooldown\async;

use Jorgebyte\KitSystem\Main;
use pocketmine\scheduler\AsyncTask;

class LoadCooldownsTask extends AsyncTask
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @throws \Exception
     */
    public function onRun(): void
    {
        $db = new \SQLite3($this->filePath);
        $results = $db->query("SELECT uuid, kit, expiry FROM cooldowns");

        if ($results === false) {
            throw new \Exception("Failed to execute query: " . $db->lastErrorMsg());
        }

        $cooldowns = [];
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            if (isset($row['uuid'], $row['kit'], $row['expiry'])) {
                $expiry = (int) $row['expiry'];
                $uuid = (string) $row['uuid'];
                $kit = (string) $row['kit'];
                $cooldowns[$uuid][$kit] = $expiry;
            }
        }
        $this->setResult($cooldowns);
        $db->close();
    }

    public function onCompletion(): void
    {
        $main = Main::getInstance();
        $cooldownManager = $main->getCooldownManager();
        $cooldowns = $this->getResult();
        if (is_array($cooldowns)) {
            foreach ($cooldowns as $uuid => $kits) {
                if (is_array($kits)) {
                    foreach ($kits as $kit => $expiry) {
                        $cooldownManager->addCooldownDirectly($uuid, (string) $kit, (int) $expiry);
                    }
                }
            }
        }
    }
}
