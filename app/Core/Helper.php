<?php

declare(strict_types=1);

namespace App\Core;

class Helper
{
    public static function log(string $message): void
    {
        $file = dirname(__DIR__, 2) . '/storage/err.log';
        $timestamp = date('Y-m-d H:i:s');
        $line = "[{$timestamp}] {$message}" . PHP_EOL;
        error_log($line, 3, $file);
    }
}
