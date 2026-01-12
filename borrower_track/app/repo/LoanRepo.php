<?php
// Loan repository: loan creation, listings, and return state updates.

class LoanRepo
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // List loans with borrower and item context, optionally filtered by status.
    public function all(?string $status = null): array
    {
        $sql =
            'SELECT loans.*, borrowers.name AS borrower_name, items.name AS item_name
             FROM loans
             JOIN borrowers ON borrowers.id = loans.borrower_id
             JOIN items ON items.id = loans.item_id';

        $params = [];
        if ($status && in_array($status, ['open', 'returned', 'lost'], true)) {
            $sql .= ' WHERE loans.status = :status';
            $params[':status'] = $status;
        }

        $sql .= ' ORDER BY loans.loan_date DESC, loans.id DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Fetch one loan with related borrower and item names.
    public function findWithDetails(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT loans.*, borrowers.name AS borrower_name, items.name AS item_name
             FROM loans
             JOIN borrowers ON borrowers.id = loans.borrower_id
             JOIN items ON items.id = loans.item_id
             WHERE loans.id = :id'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // Create a loan if the item is available.
    public function create(array $data): bool
    {
        if (!$this->isItemAvailable((int) $data['item_id'])) {
            return false;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO loans (borrower_id, item_id, loan_date, due_date, status, notes)
             VALUES (:borrower_id, :item_id, :loan_date, :due_date, :status, :notes)'
        );
        $stmt->execute([
            ':borrower_id' => $data['borrower_id'],
            ':item_id' => $data['item_id'],
            ':loan_date' => $data['loan_date'],
            ':due_date' => $data['due_date'],
            ':status' => 'open',
            ':notes' => $data['notes'],
        ]);

        return true;
    }

    // Update a loan to returned or lost.
    public function updateReturn(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE loans
             SET returned_at = :returned_at,
                 status = :status,
                 return_condition = :return_condition,
                 notes = :notes
             WHERE id = :id'
        );
        $stmt->execute([
            ':returned_at' => $data['returned_at'],
            ':status' => $data['status'],
            ':return_condition' => $data['return_condition'],
            ':notes' => $data['notes'],
            ':id' => $id,
        ]);
    }

    // Loan counts for dashboard.
    public function countOpen(): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM loans WHERE status = "open"');
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function countOverdue(string $today): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM loans WHERE status = "open" AND due_date < :today'
        );
        $stmt->execute([':today' => $today]);
        return (int) $stmt->fetchColumn();
    }

    public function overdueList(string $today): array
    {
        $stmt = $this->db->prepare(
            'SELECT loans.*, borrowers.name AS borrower_name, items.name AS item_name
             FROM loans
             JOIN borrowers ON borrowers.id = loans.borrower_id
             JOIN items ON items.id = loans.item_id
             WHERE loans.status = "open" AND loans.due_date < :today
             ORDER BY loans.due_date ASC'
        );
        $stmt->execute([':today' => $today]);
        return $stmt->fetchAll();
    }

    private function isItemAvailable(int $itemId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM loans WHERE item_id = :item_id AND status = "open"'
        );
        $stmt->execute([':item_id' => $itemId]);
        return (int) $stmt->fetchColumn() === 0;
    }
}
