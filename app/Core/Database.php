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
            Helper::log('DB connection failed: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            throw $e;
        }

        return self::$connection;
    }
}
