<?php
// Borrower repository: CRUD plus borrower details and trust score maintenance.

class BorrowerRepo
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Fetch all borrowers with current trust scores.
    public function all(): array
    {
        $stmt = $this->db->prepare('SELECT * FROM borrowers ORDER BY name');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Find a borrower by id.
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM borrowers WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // Create a new borrower.
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO borrowers (name, email, phone, trust_score, created_at)
             VALUES (:name, :email, :phone, :trust_score, :created_at)'
        );
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':phone' => $data['phone'],
            ':trust_score' => 100,
            ':created_at' => now_iso(),
        ]);

        return (int) $this->db->lastInsertId();
    }

    // Update borrower fields.
    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE borrowers SET name = :name, email = :email, phone = :phone WHERE id = :id'
        );
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':phone' => $data['phone'],
            ':id' => $id,
        ]);
    }

    // Delete a borrower if they have no loans.
    public function delete(int $id): bool
    {
        if ($this->hasLoans($id)) {
            return false;
        }

        $stmt = $this->db->prepare('DELETE FROM borrowers WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return true;
    }

    // Borrower loan history with item details.
    public function loansForBorrower(int $id): array
    {
        $stmt = $this->db->prepare(
            'SELECT loans.*, items.name AS item_name
             FROM loans
             JOIN items ON items.id = loans.item_id
             WHERE loans.borrower_id = :id
             ORDER BY loans.loan_date DESC, loans.id DESC'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll();
    }

    // Trust events history for a borrower.
    public function trustEventsForBorrower(int $id): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM trust_events WHERE borrower_id = :id ORDER BY created_at DESC, id DESC'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll();
    }

    // Recalculate and update trust score from events.
    public function recomputeTrustScore(int $id): int
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(points_delta), 0) AS total_delta
             FROM trust_events WHERE borrower_id = :id'
        );
        $stmt->execute([':id' => $id]);
        $totalDelta = (int) $stmt->fetchColumn();

        $score = clamp_int(100 + $totalDelta, 0, 100);
        $update = $this->db->prepare('UPDATE borrowers SET trust_score = :score WHERE id = :id');
        $update->execute([':score' => $score, ':id' => $id]);

        return $score;
    }

    // Top trusted borrowers.
    public function topTrusted(int $limit): array
    {
        $stmt = $this->db->prepare('SELECT * FROM borrowers ORDER BY trust_score DESC, name ASC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lowest trusted borrowers.
    public function bottomTrusted(int $limit): array
    {
        $stmt = $this->db->prepare('SELECT * FROM borrowers ORDER BY trust_score ASC, name ASC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function hasLoans(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM loans WHERE borrower_id = :id');
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
