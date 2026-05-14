<?php

declare(strict_types=1);

namespace App\Models;

class Brand
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM brands ORDER BY name ASC');
        return $stmt->fetchAll();
    }

    public function byId(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM brands WHERE id = ?');
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(string $name): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO brands (name) VALUES (?)');
        $stmt->execute([trim($name)]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, string $name): bool
    {
        $stmt = $this->pdo->prepare('UPDATE brands SET name = ? WHERE id = ?');
        return $stmt->execute([trim($name), $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM brands WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function exists(string $name, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $stmt = $this->pdo->prepare('SELECT id FROM brands WHERE name = ? AND id != ?');
            $stmt->execute([trim($name), $excludeId]);
        } else {
            $stmt = $this->pdo->prepare('SELECT id FROM brands WHERE name = ?');
            $stmt->execute([trim($name)]);
        }
        return (bool) $stmt->fetch();
    }

    public function count(): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) as cnt FROM brands');
        return (int) $stmt->fetch()['cnt'];
    }
}
