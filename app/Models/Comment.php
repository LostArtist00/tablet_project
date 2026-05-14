<?php

declare(strict_types=1);

namespace App\Models;

class Comment
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function byTablet(int $tabletId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM comments 
            WHERE tablet_id = ? 
            ORDER BY created_at DESC
        ');
        $stmt->execute([$tabletId]);
        return $stmt->fetchAll();
    }

    public function all(): array
    {
        $stmt = $this->pdo->query('
            SELECT c.*, t.name as tablet_name, b.name as brand_name
            FROM comments c
            JOIN tablets t ON c.tablet_id = t.id
            JOIN brands b ON t.brand_id = b.id
            ORDER BY c.created_at DESC
        ');
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO comments (tablet_id, author_name, comment_text)
            VALUES (?, ?, ?)
        ');
        $stmt->execute([
            $data['tablet_id'],
            $data['author_name'],
            $data['comment_text'],
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM comments WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function count(): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) as cnt FROM comments');
        return (int) $stmt->fetch()['cnt'];
    }
}
