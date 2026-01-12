<?php
// Item repository: CRUD plus availability queries.

class ItemRepo
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Fetch all items with availability flag.
    public function allWithAvailability(): array
    {
        $stmt = $this->db->prepare(
            'SELECT items.*,
                    CASE WHEN EXISTS (
                        SELECT 1 FROM loans WHERE loans.item_id = items.id AND loans.status = "open"
                    ) THEN 0 ELSE 1 END AS is_available
             FROM items
             ORDER BY name'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Fetch active items that are available to loan.
    public function availableItems(): array
    {
        $stmt = $this->db->prepare(
            'SELECT items.*
             FROM items
             WHERE items.is_active = 1
               AND NOT EXISTS (
                 SELECT 1 FROM loans WHERE loans.item_id = items.id AND loans.status = "open"
               )
             ORDER BY name'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Find an item by id.
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM items WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // Create a new item record.
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO items (name, category, serial, notes, is_active, created_at)
             VALUES (:name, :category, :serial, :notes, :is_active, :created_at)'
        );
        $stmt->execute([
            ':name' => $data['name'],
            ':category' => $data['category'],
            ':serial' => $data['serial'],
            ':notes' => $data['notes'],
            ':is_active' => $data['is_active'],
            ':created_at' => now_iso(),
        ]);

        return (int) $this->db->lastInsertId();
    }

    // Update item fields.
    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE items
             SET name = :name,
                 category = :category,
                 serial = :serial,
                 notes = :notes,
                 is_active = :is_active
             WHERE id = :id'
        );
        $stmt->execute([
            ':name' => $data['name'],
            ':category' => $data['category'],
            ':serial' => $data['serial'],
            ':notes' => $data['notes'],
            ':is_active' => $data['is_active'],
            ':id' => $id,
        ]);
    }

    // Delete an item if there are no loans attached.
    public function delete(int $id): bool
    {
        if ($this->hasLoans($id)) {
            return false;
        }
        $stmt = $this->db->prepare('DELETE FROM items WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return true;
    }

    private function hasLoans(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM loans WHERE item_id = :id');
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
