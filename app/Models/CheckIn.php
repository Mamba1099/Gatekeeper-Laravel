<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckIn extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'check_in_time',
        'check_out_time',
        'status',
        'is_late',
        'late_minutes',
        'is_early',
        'early_minutes',
        'is_early_departure',
        'early_departure_minutes',
        'hours_worked',
        'date',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
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