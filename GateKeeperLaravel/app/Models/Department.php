<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use app\Models\CheckInSetting;

class Department extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'code',
        'description',
        'standard_check_in',
        'standard_check_out',
        'grace_minutes',
        'late_threshold',
        'good_attendance_threshold',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function checkInSetting()
    {
        return $this->hasOne(CheckInSetting::class);
    }
}