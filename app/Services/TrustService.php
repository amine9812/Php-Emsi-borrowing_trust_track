<?php
// Trust scoring service: applies return rules and logs events.

namespace App\Services;

use App\Models\Borrower;
use App\Models\Loan;
use App\Models\TrustEvent;
use Illuminate\Support\Facades\DB;

class TrustService
{
    public function processReturn(Loan $loan, string $returnedAt, string $condition, string $notes = ''): array
    {
        if ($loan->status !== 'open') {
            return ['ok' => false, 'error' => 'This loan is already closed.'];
        }

        return DB::transaction(function () use ($loan, $returnedAt, $condition, $notes): array {
            $status = $condition === 'lost' ? 'lost' : 'returned';

            $loan->update([
                'returned_at' => $returnedAt,
                'status' => $status,
                'return_condition' => $condition,
                'notes' => $notes,
            ]);

            $daysLate = max(0, date_diff_days($returnedAt, $loan->due_date));
            $borrower = $loan->borrower;

            if ($daysLate === 0 && $condition !== 'lost') {
                $this->addTrustEvent($borrower, $loan, 'on_time_bonus', 1, 'On-time return');
            }

            if ($daysLate > 0) {
                $delta = -2 * $daysLate;
                $this->addTrustEvent(
                    $borrower,
                    $loan,
                    'late_penalty',
                    $delta,
                    'Late return: '.$daysLate.' day(s)'
                );
            }

            if ($condition === 'damaged_minor') {
                $this->addTrustEvent($borrower, $loan, 'damage_penalty', -15, 'Damage penalty: minor');
            }

            if ($condition === 'damaged_major') {
                $this->addTrustEvent($borrower, $loan, 'damage_penalty', -30, 'Damage penalty: major');
            }

            if ($condition === 'lost') {
                $this->addTrustEvent($borrower, $loan, 'lost_penalty', -50, 'Lost item');
            }

            $borrower->recomputeTrustScore();

            return ['ok' => true, 'error' => null];
        });
    }

    private function addTrustEvent(Borrower $borrower, Loan $loan, string $type, int $delta, string $reason): void
    {
        TrustEvent::create([
            'borrower_id' => $borrower->id,
            'loan_id' => $loan->id,
            'event_type' => $type,
            'points_delta' => $delta,
            'reason' => $reason,
            'created_at' => now_iso(),
        ]);
    }
}
