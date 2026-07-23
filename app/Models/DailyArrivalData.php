<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyArrivalData extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'date',
        'arrival_minutes',
        'departure_minutes',
        'is_late',
        'is_early',
        'is_early_departure',
    ];

    protected $casts = [
        'date' => 'date',
        'is_late' => 'boolean',
        'is_early' => 'boolean',
        'is_early_departure' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}