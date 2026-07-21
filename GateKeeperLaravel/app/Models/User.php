<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Department;
use App\Models\CheckIn;
use App\Models\EmployeeMetrics;
use App\Models\AttendanceSummary;
use App\Models\DailyArrivalData;
use App\Models\LoginAttempt;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable; // ← HasApiTokens included

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'email',
        'password',
        'full_name',
        'role',
        'department_id',
        'position',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function checkIns()
    {
        return $this->hasMany(CheckIn::class);
    }

    public function employeeMetrics()
    {
        return $this->hasOne(EmployeeMetrics::class);
    }

    public function attendanceSummaries()
    {
        return $this->hasMany(AttendanceSummary::class);
    }

    public function dailyArrivalData()
    {
        return $this->hasMany(DailyArrivalData::class);
    }

    public function loginAttempts()
    {
        return $this->hasMany(LoginAttempt::class);
    }
}