<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\CheckIn;
use App\Models\EmployeeMetrics;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function index(Request $request)
    {
        $dateRange = $request->date_range ?? 'today';
        $startDate = $this->getStartDate($dateRange);
        $endDate = Carbon::now();

        $stats = [
            'overview' => $this->getOverviewStats(),
            'attendance' => $this->getAttendanceStats($startDate, $endDate),
            'departments' => $this->getDepartmentStats(),
            'recent_activity' => $this->getRecentActivity(),
            'trends' => $this->getTrends($dateRange),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get overview statistics
     */
    private function getOverviewStats(): array
    {
        $today = Carbon::today();
        
        return [
            'total_employees' => User::where('role', 'EMPLOYEE')->count(),
            'active_employees' => User::where('role', 'EMPLOYEE')->where('is_active', true)->count(),
            'total_departments' => Department::count(),
            'today_checkins' => CheckIn::whereDate('date', $today)->count(),
            'today_checkouts' => CheckIn::whereDate('date', $today)->whereNotNull('check_out_time')->count(),
            'today_late' => CheckIn::whereDate('date', $today)->where('is_late', true)->count(),
            'avg_attendance' => round(EmployeeMetrics::avg('attendance_percentage'), 2),
            'excellent_performers' => EmployeeMetrics::where('attendance_rating', 'EXCELLENT')->count(),
            'poor_performers' => EmployeeMetrics::where('attendance_rating', 'POOR')->count(),
        ];
    }

    /**
     * Get attendance statistics for date range
     */
    private function getAttendanceStats(Carbon $startDate, Carbon $endDate): array
    {
        $checkIns = CheckIn::whereBetween('date', [$startDate, $endDate])->get();

        return [
            'total_checkins' => $checkIns->count(),
            'on_time' => $checkIns->where('is_late', false)->where('is_early', false)->count(),
            'late' => $checkIns->where('is_late', true)->count(),
            'early' => $checkIns->where('is_early', true)->count(),
            'early_departures' => $checkIns->where('is_early_departure', true)->count(),
            'total_hours_worked' => round($checkIns->sum('hours_worked'), 2),
            'average_hours' => round($checkIns->avg('hours_worked'), 2),
            'attendance_rate' => $checkIns->count() > 0 ? 
                round(($checkIns->where('status', 'CHECKED_OUT')->count() / $checkIns->count()) * 100, 2) : 0,
        ];
    }

    /**
     * Get department statistics
     */
    private function getDepartmentStats(): Collection
    {
        return Department::withCount(['users' => function($query) {
            $query->where('role', 'EMPLOYEE');
        }])->get()->map(function($dept) {
            return [
                'id' => $dept->id,
                'name' => $dept->name,
                'code' => $dept->code,
                'employee_count' => $dept->users_count,
                'is_active' => $dept->is_active,
            ];
        });
    }

    /**
     * Get recent activity
     */
    private function getRecentActivity(): Collection
    {
        $recentCheckins = CheckIn::with(['user' => function($query) {
            $query->select('id', 'full_name', 'email');
        }])
        ->latest()
        ->limit(10)
        ->get()
        ->map(function($checkin) {
            return [
                'id' => $checkin->id,
                'user' => $checkin->user?->full_name ?? 'Unknown',
                'action' => $checkin->check_out_time ? 'Checked Out' : 'Checked In',
                'time' => $checkin->check_out_time ?? $checkin->check_in_time,
                'status' => $checkin->status,
                'is_late' => $checkin->is_late,
            ];
        });

        return $recentCheckins;
    }

    /**
     * Get trends data
     */
    private function getTrends(string $dateRange): Collection
    {
        $days = $dateRange === 'today' ? 1 : 
                ($dateRange === 'week' ? 7 : 
                ($dateRange === 'month' ? 30 : 7));

        $trends = collect();
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $trends->push([
                'date' => $date->format('Y-m-d'),
                'checkins' => CheckIn::whereDate('date', $date)->count(),
                'late' => CheckIn::whereDate('date', $date)->where('is_late', true)->count(),
                'on_time' => CheckIn::whereDate('date', $date)->where('is_late', false)->count(),
            ]);
        }

        return $trends;
    }

    /**
     * Get start date based on range
     */
    private function getStartDate(string$range): Carbon
    {
        return match($range) {
            'today' => Carbon::today(),
            'week' => Carbon::today()->subDays(7),
            'month' => Carbon::today()->subDays(30),
            default => Carbon::today()->subDays(7),
        };
    }

    /**
     * Get attendance chart data
     */
    public function getAttendanceChart(Request $request)
    {
        $days = $request->days ?? 7;
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $data[] = [
                'date' => $date->format('M d'),
                'check_ins' => CheckIn::whereDate('date', $date)->count(),
                'late' => CheckIn::whereDate('date', $date)->where('is_late', true)->count(),
                'on_time' => CheckIn::whereDate('date', $date)->where('is_late', false)->count(),
                'early' => CheckIn::whereDate('date', $date)->where('is_early', true)->count(),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get top performers
     */
    public function getTopPerformers(Request $request)
    {
        $limit = $request->limit ?? 10;

        $performers = EmployeeMetrics::with('user')
            ->whereHas('user', function($query) {
                $query->where('role', 'EMPLOYEE');
            })
            ->orderBy('attendance_percentage', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($metric) {
                return [
                    'user_id' => $metric->user_id,
                    'name' => $metric->user?->full_name ?? 'Unknown',
                    'attendance_percentage' => $metric->attendance_percentage,
                    'rating' => $metric->attendance_rating,
                    'total_hours' => $metric->total_hours_worked,
                    'streak' => $metric->current_streak,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $performers,
        ]);
    }
}