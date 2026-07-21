<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSummary extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'month',
        'year',
        'total_days',
        'present_days',
        'absent_days',
        'late_days',
        'on_time_days',
        'early_days',
        'early_departure_days',
        'total_hours_worked',
        'average_arrival_time',
        'average_departure_time',
        'attendance_percentage',
        'punctuality_percentage',
        'attendance_rating',
    ];

    protected $casts = [
        'average_arrival_time' => 'datetime:H:i',
        'average_departure_time' => 'datetime:H:i',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getMonthName()
    {
        return date('F', mktime(0, 0, 0, $this->month, 1));
    }
}