<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeMetrics extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'employee_metrics';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'total_check_ins',
        'on_time_check_ins',
        'late_check_ins',
        'early_check_ins',
        'early_departures',
        'total_hours_worked',
        'current_streak',
        'best_streak',
        'attendance_percentage',
        'attendance_rating',
        'average_arrival_minutes',
        'average_departure_minutes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Update rating based on percentage
    public function updateRating()
    {
        if ($this->attendance_percentage >= 90) {
            $this->attendance_rating = 'EXCELLENT';
        } elseif ($this->attendance_percentage >= 75) {
            $this->attendance_rating = 'GOOD';
        } elseif ($this->attendance_percentage >= 60) {
            $this->attendance_rating = 'FAIR';
        } else {
            $this->attendance_rating = 'POOR';
        }
        $this->save();
    }

    public function getRatingColor()
    {
        return match($this->attendance_rating) {
            'EXCELLENT' => 'success',
            'GOOD' => 'info',
            'FAIR' => 'warning',
            'POOR' => 'danger',
            default => 'secondary',
        };
    }
}