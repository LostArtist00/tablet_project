<?php

declare(strict_types=1);

use App\Core\Helper;

require_once __DIR__ . '/../../vendor/autoload.php';

error_reporting(E_ALL);

define('APP_ROOT', dirname(__DIR__, 2));
define('APP_PATH', dirname(__DIR__, 1));
define('BASE_URL', '/tablet_project');
define('ASSET_PATH', BASE_URL . '/app/assets');

set_exception_handler(function (Throwable $e): void {
    Helper::log('Uncaught ' . get_class($e) . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    require APP_PATH . '/errors/500.php';
    exit;
});

set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    Helper::log("PHP error [{$severity}]: {$message} in {$file}:{$line}");
    if (in_array($severity, [E_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR], true)) {
        http_response_code(500);
        require APP_PATH . '/errors/500.php';
        exit;
    }
    return true;
});

$sessionPath = dirname(__DIR__) . '/storage/sessions';

if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}

session_save_path($sessionPath);
session_start();

$dbConfig = [
    'host' => 'localhost',
    'port' => '3306',
    'dbname' => 'tablet_survey',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
];

require_once __DIR__ . '/helpers.php';
