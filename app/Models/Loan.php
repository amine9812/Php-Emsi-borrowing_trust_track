<?php
// Loan model: ties borrowers to items and tracks loan state.

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $table = 'loans';
    public $timestamps = false;

    protected $fillable = [
        'borrower_id',
        'item_id',
        'loan_date',
        'due_date',
        'returned_at',
        'status',
        'return_condition',
        'notes',
    ];

    public function borrower()
    {
        return $this->belongsTo(Borrower::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function trustEvents()
    {
        return $this->hasMany(TrustEvent::class);
    }
}
