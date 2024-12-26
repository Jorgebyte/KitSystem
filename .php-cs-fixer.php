<?php

require __DIR__ . '/vendor/autoload.php';

use pocketmine\codestyle\PocketmineConfig;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src/Jorgebyte');

return (new PocketmineConfig($finder));