<?php

declare(strict_types=1);

/*
 *    KitSystem
 *    Api: 5.3.0
 *    Version: 1.0.0
 *    Author: Jorgebyte
 */

namespace Jorgebyte\KitSystem\message;

use Exception;
use pocketmine\utils\Config;

class Message
{
    private Config $messages;
    private string $prefix;

    /**
     * @throws Exception
     */
    public function __construct(string $dataFolder)
    {
        $this->messages = new Config($dataFolder . "message.yml", Config::YAML);
        $value = $this->messages->get(MessageKey::PREFIX, null);

        if (is_scalar($value) || is_null($value)) {
            $this->prefix = (string) $value;
        } else {
            throw new Exception("Invalid prefix value in configuration. Expected a string or null.");
        }
    }

    /**
     * @throws Exception
     */
    public function getMessage(string $key, array $replacements = [], bool $includePrefix = true): string
    {
        $message = $this->messages->get($key, "Message not found");

        if (!is_string($message)) {
            throw new Exception("Message for key '{$key}' is not a string.");
        }

        if ($includePrefix) {
            $message = str_replace("{prefix}", $this->prefix, $message);
        }

        foreach ($replacements as $search => $replace) {
            if (!is_string($replace)) {
                throw new Exception("Replacement for '{$search}' is not a string.");
            }
            $message = str_replace("{" . $search . "}", $replace, $message);
        }
        return $message;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }
}
