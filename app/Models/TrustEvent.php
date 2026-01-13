<?php
// TrustEvent model: records scoring changes with reasons.

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrustEvent extends Model
{
    protected $table = 'trust_events';
    public $timestamps = false;

    protected $fillable = [
        'borrower_id',
        'loan_id',
        'event_type',
        'points_delta',
        'reason',
        'created_at',
    ];

    public function borrower()
    {
        return $this->belongsTo(Borrower::class);
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
