<?php
// Trust scoring service: calculates penalties/bonuses and logs trust events.

class TrustService
{
    private PDO $db;
    private BorrowerRepo $borrowerRepo;
    private LoanRepo $loanRepo;

    public function __construct(PDO $db, BorrowerRepo $borrowerRepo, LoanRepo $loanRepo)
    {
        $this->db = $db;
        $this->borrowerRepo = $borrowerRepo;
        $this->loanRepo = $loanRepo;
    }

    // Process a return and apply trust events, ensuring idempotency for closed loans.
    public function processReturn(int $loanId, string $returnedAt, string $condition, string $notes = ''): array
    {
        $loan = $this->loanRepo->findWithDetails($loanId);
        if (!$loan) {
            return ['ok' => false, 'error' => 'Loan not found.'];
        }
        if ($loan['status'] !== 'open') {
            return ['ok' => false, 'error' => 'This loan is already closed.'];
        }

        $status = $condition === 'lost' ? 'lost' : 'returned';
        $this->loanRepo->updateReturn($loanId, [
            'returned_at' => $returnedAt,
            'status' => $status,
            'return_condition' => $condition,
            'notes' => $notes,
        ]);

        $daysLate = max(0, date_diff_days($returnedAt, $loan['due_date']));

        if ($daysLate === 0 && $condition !== 'lost') {
            $this->addTrustEvent(
                (int) $loan['borrower_id'],
                $loanId,
                'on_time_bonus',
                1,
                'On-time return'
            );
        }

        if ($daysLate > 0) {
            $delta = -2 * $daysLate;
            $this->addTrustEvent(
                (int) $loan['borrower_id'],
                $loanId,
                'late_penalty',
                $delta,
                'Late return: ' . $daysLate . ' day(s)'
            );
        }

        if ($condition === 'damaged_minor') {
            $this->addTrustEvent(
                (int) $loan['borrower_id'],
                $loanId,
                'damage_penalty',
                -15,
                'Damage penalty: minor'
            );
        }

        if ($condition === 'damaged_major') {
            $this->addTrustEvent(
                (int) $loan['borrower_id'],
                $loanId,
                'damage_penalty',
                -30,
                'Damage penalty: major'
            );
        }

        if ($condition === 'lost') {
            $this->addTrustEvent(
                (int) $loan['borrower_id'],
                $loanId,
                'lost_penalty',
                -50,
                'Lost item'
            );
        }

        $this->borrowerRepo->recomputeTrustScore((int) $loan['borrower_id']);

        return ['ok' => true, 'error' => null];
    }

    private function addTrustEvent(int $borrowerId, int $loanId, string $type, int $delta, string $reason): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO trust_events (borrower_id, loan_id, event_type, points_delta, reason, created_at)
             VALUES (:borrower_id, :loan_id, :event_type, :points_delta, :reason, :created_at)'
        );
        $stmt->execute([
            ':borrower_id' => $borrowerId,
            ':loan_id' => $loanId,
            ':event_type' => $type,
            ':points_delta' => $delta,
            ':reason' => $reason,
            ':created_at' => now_iso(),
        ]);
    }
}
