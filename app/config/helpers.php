<?php

use App\Core\Database;

function db(): PDO
{
    $dbConfig = $GLOBALS['dbConfig'] ?? [];
    return Database::connect($dbConfig);
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function old(string $key, mixed $default = ''): mixed
{
    return $_POST[$key] ?? $default;
}

function asset(string $path): string
{
    return ASSET_PATH . '/' . ltrim($path, '/');
}

function uploadUrl(string $path = ''): string
{
    return BASE_URL . '/uploads/' . ltrim($path, '/');
}

function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    if ($path === '') {
        return BASE_URL . '/';
    }
    return BASE_URL . '/' . $path;
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrfToken()) . '" />';
}

function isValidCsrfToken(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && is_string($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function requireCsrfToken(): void
{
    if (!isValidCsrfToken($_POST['_token'] ?? null)) {
        http_response_code(419);
        exit('Invalid or expired form token.');
    }
}

function flashType(): string
{
    $type = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash_type']);
    return is_string($type) ? $type : 'success';
}

function setFlash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = $message;
    $_SESSION['flash_type'] = $type;
}

function flashMessage(): string
{
    $message = $_SESSION['flash'] ?? '';
    unset($_SESSION['flash']);
    return $message;
}

function isNonEmptyString(mixed $value): bool
{
    return trim((string) $value) !== '';
}

function isValidPositiveNumber(mixed $value): bool
{
    return is_numeric($value) && (float) $value >= 0;
}
