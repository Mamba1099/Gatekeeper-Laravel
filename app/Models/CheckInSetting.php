<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckInSetting extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'department_id',
        'standard_time',
        'grace_minutes',
        'late_threshold_minutes',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}