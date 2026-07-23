<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSummary;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Generate monthly report
     */
    public function monthly(Request $request)
    {
        $request->validate([
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|min:2000',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;

        $query = AttendanceSummary::with('user.department')
            ->where('month', $month)
            ->where('year', $year);

        if ($request->has('department_id')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $summaries = $query->get();

        // Calculate department totals
        $departmentTotals = $summaries->groupBy('user.department_id')->map(function($group) {
            $dept = $group->first()->user->department;
            return [
                'department' => $dept?->name ?? 'Unknown',
                'total_employees' => $group->count(),
                'avg_attendance' => round($group->avg('attendance_percentage'), 2),
                'avg_punctuality' => round($group->avg('punctuality_percentage'), 2),
                'total_hours' => round($group->sum('total_hours_worked'), 2),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'month' => $month,
                'year' => $year,
                'month_name' => Carbon::create($year, $month)->format('F Y'),
                'summary' => $summaries,
                'department_totals' => $departmentTotals,
                'overall' => [
                    'total_employees' => $summaries->count(),
                    'avg_attendance' => round($summaries->avg('attendance_percentage'), 2),
                    'avg_punctuality' => round($summaries->avg('punctuality_percentage'), 2),
                    'total_hours' => round($summaries->sum('total_hours_worked'), 2),
                ],
            ],
        ]);
    }

    /**
     * Export report as CSV
     */
    public function export(Request $request)
    {
        $request->validate([
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|min:2000',
            'format' => 'in:csv,excel',
        ]);

        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;

        $summaries = AttendanceSummary::with('user.department')
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=attendance_report_{$month}_{$year}.csv",
        ];

        $callback = function() use ($summaries) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'Employee',
                'Department',
                'Total Days',
                'Present',
                'Absent',
                'Late',
                'On Time',
                'Hours Worked',
                'Attendance %',
                'Rating'
            ]);

            // Data
            foreach ($summaries as $summary) {
                fputcsv($file, [
                    $summary->user->full_name,
                    $summary->user->department->name ?? 'N/A',
                    $summary->total_days,
                    $summary->present_days,
                    $summary->absent_days,
                    $summary->late_days,
                    $summary->on_time_days,
                    round($summary->total_hours_worked, 2),
                    round($summary->attendance_percentage, 2) . '%',
                    $summary->attendance_rating,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get attendance trends
     */
    public function trends(Request $request)
    {
        $request->validate([
            'months' => 'nullable|integer|min:1|max:12',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $months = $request->months ?? 6;
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subMonths($months);

        $query = AttendanceSummary::with('user.department')
            ->whereBetween('date', [$startDate, $endDate]);

        if ($request->has('department_id')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $trends = $query->get()
            ->groupBy(['year', 'month'])
            ->map(function($yearGroup) {
                return $yearGroup->map(function($monthGroup) {
                    return [
                        'avg_attendance' => round($monthGroup->avg('attendance_percentage'), 2),
                        'avg_punctuality' => round($monthGroup->avg('punctuality_percentage'), 2),
                        'total_hours' => round($monthGroup->sum('total_hours_worked'), 2),
                        'total_employees' => $monthGroup->count(),
                    ];
                });
            });

        return response()->json([
            'success' => true,
            'data' => $trends,
        ]);
    }
}