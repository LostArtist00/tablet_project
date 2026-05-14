<?php

declare(strict_types=1);

namespace App\Models;

class Auth
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function attempt(string $username, string $password): bool
    {
        $stmt = $this->pdo->prepare('SELECT * FROM admins WHERE username = ?');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        return true;
    }

    public function check(): bool
    {
        return !empty($_SESSION['admin_id']);
    }

    public function requireAdmin(): void
    {
        if (!$this->check()) {
            redirect('admin/login.php');
        }
    }

    public function user(): ?array
    {
        if (!$this->check()) {
            return null;
        }
        return [
            'id' => $_SESSION['admin_id'],
            'username' => $_SESSION['admin_username'] ?? null,
        ];
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }
}
