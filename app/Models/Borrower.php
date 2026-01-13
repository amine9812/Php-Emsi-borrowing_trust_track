<?php
// Borrower model: holds borrower profile, trust score, and relations.

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borrower extends Model
{
    protected $table = 'borrowers';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'trust_score',
        'created_at',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function trustEvents()
    {
        return $this->hasMany(TrustEvent::class);
    }

    public function recomputeTrustScore(): int
    {
        $totalDelta = (int) $this->trustEvents()->sum('points_delta');
        $score = clamp_int(100 + $totalDelta, 0, 100);
        $this->update(['trust_score' => $score]);
        return $score;
    }
}
