<?php
// Database seeder: inserts a small demo dataset for BTS.

namespace Database\Seeders;

use App\Models\Borrower;
use App\Models\Item;
use App\Models\Loan;
use App\Models\TrustEvent;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $alice = Borrower::create([
            'name' => 'Alice Nguyen',
            'email' => 'alice@example.com',
            'phone' => '555-2100',
            'trust_score' => 100,
            'created_at' => '2024-06-01T09:00:00+00:00',
        ]);

        $marcus = Borrower::create([
            'name' => 'Marcus Cole',
            'email' => 'marcus@example.com',
            'phone' => '555-3344',
            'trust_score' => 81,
            'created_at' => '2024-06-02T11:15:00+00:00',
        ]);

        $priya = Borrower::create([
            'name' => 'Priya Shah',
            'email' => 'priya@example.com',
            'phone' => '555-7788',
            'trust_score' => 100,
            'created_at' => '2024-06-03T15:40:00+00:00',
        ]);

        $camera = Item::create([
            'name' => 'DSLR Camera',
            'category' => 'Photography',
            'serial' => 'CAM-8842',
            'notes' => 'Includes lens kit',
            'is_active' => 1,
            'created_at' => '2024-06-01T09:10:00+00:00',
        ]);

        $laptop = Item::create([
            'name' => 'Laptop Dell 14\"',
            'category' => 'Electronics',
            'serial' => 'DL-1422',
            'notes' => 'Charger included',
            'is_active' => 1,
            'created_at' => '2024-06-01T09:12:00+00:00',
        ]);

        $projector = Item::create([
            'name' => 'Mini Projector',
            'category' => 'Presentation',
            'serial' => 'PJ-330',
            'notes' => 'Needs HDMI cable',
            'is_active' => 1,
            'created_at' => '2024-06-01T09:14:00+00:00',
        ]);

        $loan1 = Loan::create([
            'borrower_id' => $alice->id,
            'item_id' => $camera->id,
            'loan_date' => '2024-07-01',
            'due_date' => '2024-07-10',
            'returned_at' => null,
            'status' => 'open',
            'return_condition' => null,
            'notes' => 'Student film project',
        ]);

        $loan2 = Loan::create([
            'borrower_id' => $marcus->id,
            'item_id' => $laptop->id,
            'loan_date' => '2024-05-20',
            'due_date' => '2024-05-27',
            'returned_at' => '2024-05-29',
            'status' => 'returned',
            'return_condition' => 'damaged_minor',
            'notes' => 'Returned with minor scratches',
        ]);

        TrustEvent::create([
            'borrower_id' => $marcus->id,
            'loan_id' => $loan2->id,
            'event_type' => 'late_penalty',
            'points_delta' => -4,
            'reason' => 'Late return: 2 day(s)',
            'created_at' => '2024-05-29T10:00:00+00:00',
        ]);

        TrustEvent::create([
            'borrower_id' => $marcus->id,
            'loan_id' => $loan2->id,
            'event_type' => 'damage_penalty',
            'points_delta' => -15,
            'reason' => 'Damage penalty: minor',
            'created_at' => '2024-05-29T10:00:00+00:00',
        ]);

        // Keep unused variable to avoid lint warnings in strict environments.
        unset($priya, $projector, $loan1);
    }
}
