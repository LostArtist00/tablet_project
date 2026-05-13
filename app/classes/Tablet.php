<?php

declare(strict_types=1);

class Tablet
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(): array
    {
        $stmt = $this->pdo->query('
            SELECT t.*, b.name as brand_name 
            FROM tablets t 
            JOIN brands b ON t.brand_id = b.id 
            ORDER BY b.name, t.name
        ');
        return $stmt->fetchAll();
    }

    public function byId(int $id): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT t.*, b.name as brand_name 
            FROM tablets t 
            JOIN brands b ON t.brand_id = b.id 
            WHERE t.id = ?
        ');
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function featured(): array
    {
        $stmt = $this->pdo->query('
            SELECT t.*, b.name as brand_name 
            FROM tablets t 
            JOIN brands b ON t.brand_id = b.id 
            ORDER BY t.id DESC 
            LIMIT 6
        ');
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO tablets (brand_id, name, size, has_display, price, release_date, pressure_levels, connection_type, notes, image_path, image_fit)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $data['brand_id'],
            $data['name'],
            $data['size'] ?? null,
            $data['has_display'] ?? 0,
            $data['price'] ?? null,
            $data['release_date'] ?? null,
            $data['pressure_levels'] ?? null,
            $data['connection_type'] ?? null,
            $data['notes'] ?? null,
            $data['image_path'] ?? null,
            $data['image_fit'] ?? 'cover',
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE tablets SET 
                brand_id = ?, name = ?, size = ?, has_display = ?, price = ?,
                release_date = ?, pressure_levels = ?, connection_type = ?, notes = ?,
                image_path = ?, image_fit = ?
            WHERE id = ?
        ');
        return $stmt->execute([
            $data['brand_id'],
            $data['name'],
            $data['size'] ?? null,
            $data['has_display'] ?? 0,
            $data['price'] ?? null,
            $data['release_date'] ?? null,
            $data['pressure_levels'] ?? null,
            $data['connection_type'] ?? null,
            $data['notes'] ?? null,
            $data['image_path'] ?? null,
            $data['image_fit'] ?? 'cover',
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM tablets WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function stats(int $id): array
    {
        $stmt = $this->pdo->prepare('
            SELECT 
                COUNT(*) as total_reports,
                SUM(CASE WHEN status = "working" THEN 1 ELSE 0 END) as working,
                SUM(CASE WHEN status = "broken" THEN 1 ELSE 0 END) as broken,
                SUM(CASE WHEN status = "partially_working" THEN 1 ELSE 0 END) as partially_working,
                AVG(CASE WHEN status = "working" AND years_used IS NOT NULL THEN years_used END) as avg_years_working,
                AVG(CASE WHEN status != "working" AND years_used IS NOT NULL THEN years_used END) as avg_years_broken
            FROM failure_reports 
            WHERE tablet_id = ?
        ');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function search(string $query, ?int $brandId = null): array
    {
        $sql = '
            SELECT t.*, b.name as brand_name 
            FROM tablets t 
            JOIN brands b ON t.brand_id = b.id 
            WHERE (t.name LIKE ? OR b.name LIKE ?)
        ';
        $params = ['%' . $query . '%', '%' . $query . '%'];

        if ($brandId) {
            $sql .= ' AND t.brand_id = ?';
            $params[] = $brandId;
        }

        $sql .= ' ORDER BY b.name, t.name';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function byBrand(int $brandId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM tablets WHERE brand_id = ? ORDER BY name
        ');
        $stmt->execute([$brandId]);
        return $stmt->fetchAll();
    }

    public function count(): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) as cnt FROM tablets');
        return (int) $stmt->fetch()['cnt'];
    }
}