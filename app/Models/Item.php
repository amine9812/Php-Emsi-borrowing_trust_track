<?php
// Item model: manages inventory items and availability relations.

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'items';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'category',
        'serial',
        'notes',
        'is_active',
        'created_at',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function openLoans()
    {
        return $this->hasMany(Loan::class)->where('status', 'open');
    }
}
