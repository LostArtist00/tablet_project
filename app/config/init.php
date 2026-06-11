<?php

declare(strict_types=1);

use App\Core\Database;

require_once __DIR__ . '/../../vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', '1');

$sessionPath = dirname(__DIR__) . '/storage/sessions';

if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}

session_save_path($sessionPath);
session_start();

define('APP_ROOT', dirname(__DIR__, 2));
define('APP_PATH', dirname(__DIR__, 1));
define('BASE_URL', '/tablet_project');
define('ASSET_PATH', BASE_URL . '/app/assets');

$dbConfig = [
    'host' => 'localhost',
    'port' => '3306',
    'dbname' => 'tablet_survey',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
];

require_once __DIR__ . '/helpers.php';