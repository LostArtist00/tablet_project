<?php

declare(strict_types=1);

class FailureReport
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function byTablet(int $tabletId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT r.*, t.name as tablet_name, b.name as brand_name
            FROM failure_reports r
            JOIN tablets t ON r.tablet_id = t.id
            JOIN brands b ON t.brand_id = b.id
            WHERE r.tablet_id = ?
            ORDER BY r.created_at DESC
        ');
        $stmt->execute([$tabletId]);
        return $stmt->fetchAll();
    }

    public function recent(int $limit = 20): array
    {
        $stmt = $this->pdo->query('
            SELECT r.*, t.name as tablet_name, b.name as brand_name
            FROM failure_reports r
            JOIN tablets t ON r.tablet_id = t.id
            JOIN brands b ON t.brand_id = b.id
            ORDER BY r.created_at DESC
            LIMIT ' . (int) $limit
        );
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO failure_reports 
                (tablet_id, nickname, status, years_used, warranty_expired, severity, repair_status, failure_reason, extra_comment)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $data['tablet_id'],
                $data['nickname'],
                $data['status'] ?? 'working',
                $data['years_used'] ?? null,
                $data['warranty_expired'] ?? 0,
                $data['severity'] ?? 'moderate',
                $data['repair_status'] ?? 'none',
                $data['failure_reason'] ?? null,
                $data['extra_comment'] ?? null,
            ]);
            $reportId = (int) $this->pdo->lastInsertId();

            if (!empty($data['issues'])) {
                $issueStmt = $this->pdo->prepare('
                    INSERT INTO report_issues (report_id, category, subcategory, severity)
                    VALUES (?, ?, ?, ?)
                ');
                foreach ($data['issues'] as $issue) {
                    $issueStmt->execute([
                        $reportId,
                        $issue['category'],
                        $issue['subcategory'],
                        $issue['severity'] ?? 'moderate',
                    ]);
                }
            }

            $this->pdo->commit();
            return $reportId;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM failure_reports WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function summary(): array
    {
        $stmt = $this->pdo->query('
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = "working" THEN 1 ELSE 0 END) as working,
                SUM(CASE WHEN status = "broken" THEN 1 ELSE 0 END) as broken,
                SUM(CASE WHEN status = "partially_working" THEN 1 ELSE 0 END) as partially_working
            FROM failure_reports
        ');
        return $stmt->fetch();
    }

    public function topIssues(int $limit = 10): array
    {
        $stmt = $this->pdo->query('
            SELECT category, subcategory, COUNT(*) as cnt
            FROM report_issues
            GROUP BY category, subcategory
            ORDER BY cnt DESC
            LIMIT ' . (int) $limit
        );
        return $stmt->fetchAll();
    }

    public function byId(int $id): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT r.*, t.name as tablet_name
            FROM failure_reports r
            JOIN tablets t ON r.tablet_id = t.id
            WHERE r.id = ?
        ');
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function issues(int $reportId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM report_issues WHERE report_id = ?');
        $stmt->execute([$reportId]);
        return $stmt->fetchAll();
    }
}