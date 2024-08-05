<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'priority',
        'quantity',
        'amount',
    ];

    /**
     * Get all the prize records associated with the prize.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prizeRecords() 
    {
        return $this->hasMany(PrizeRecord::class, 'prize_id', 'id');
    }
}
