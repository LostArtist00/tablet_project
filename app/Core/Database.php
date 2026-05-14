<?php

declare(strict_types=1);

namespace App\Core;

class Database
{
    private static ?\PDO $connection = null;

    public static function connect(array $config): \PDO
    {
        if (self::$connection instanceof \PDO) {
            return self::$connection;
        }

        $host = $config['host'] ?? 'localhost';
        $port = $config['port'] ?? '3306';
        $charset = $config['charset'] ?? 'utf8mb4';

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $config['dbname'] ?? '', $charset);

        try {
            self::$connection = new \PDO(
                $dsn,
                $config['username'] ?? '',
                $config['password'] ?? '',
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    \PDO::ATTR_TIMEOUT => 5
                ]
            );
        } catch (\PDOException $e) {
            die('<div style="padding:20px;background:#ffebe9;border:1px solid #ffcccc;font-family:sans-serif;"><h2>Database Connection Error</h2><p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p><p><strong>Check:</strong></p><ul><li>MySQL service is running</li><li>Database "tablet_survey" exists</li><li>Credentials are correct</li></ul></div>');
        }

        return self::$connection;
    }
}
