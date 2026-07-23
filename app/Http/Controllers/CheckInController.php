<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckInController extends Controller
{
    /**
     * Check in employee (authenticated user)
     */
    public function checkIn(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        $today = Carbon::today();
        $existing = CheckIn::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($existing && !$existing->check_out_time) {
            return response()->json([
                'success' => false,
                'message' => 'Already checked in today!',
                'data' => $existing,
            ], 400);
        }

        DB::beginTransaction();

        try {
            $checkIn = new CheckIn();
            $checkIn->user_id = $user->id;
            $checkIn->check_in_time = now();
            $checkIn->date = $today;
            $checkIn->status = 'CHECKED_IN';

            // Check if late
            $department = $user->department;
            $standardCheckIn = Carbon::parse($department->standard_check_in);
            $graceTime = $standardCheckIn->copy()->addMinutes($department->grace_minutes);

            if (now()->gt($graceTime)) {
                $checkIn->is_late = true;
                $checkIn->late_minutes = now()->diffInMinutes($standardCheckIn);
                $checkIn->status = 'LATE';
            }

            // Check if early
            if (now()->lt($standardCheckIn)) {
                $checkIn->is_early = true;
                $checkIn->early_minutes = now()->diffInMinutes($standardCheckIn);
            }

            $checkIn->save();
            $this->updateMetrics($user->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Checked in successfully!',
                'data' => $checkIn,
                'status' => $checkIn->status,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to check in: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check out employee (authenticated user)
     */
    public function checkOut(Request $request)
    {
        $user = $request->user();

        $checkIn = CheckIn::where('user_id', $user->id)
            ->whereNull('check_out_time')
            ->whereDate('date', Carbon::today())
            ->first();

        if (!$checkIn) {
            return response()->json([
                'success' => false,
                'message' => 'No active check-in found for today!',
            ], 404);
        }

        DB::beginTransaction();

        try {
            $checkIn->check_out_time = now();
            $checkIn->status = 'CHECKED_OUT';

            // Calculate hours worked
            $checkIn->hours_worked = $checkIn->check_in_time->diffInHours($checkIn->check_out_time);

            // Check early departure
            $department = $user->department;
            $standardCheckOut = Carbon::parse($department->standard_check_out);
            
            if (now()->lt($standardCheckOut)) {
                $checkIn->is_early_departure = true;
                $checkIn->early_departure_minutes = now()->diffInMinutes($standardCheckOut);
            }

            $checkIn->save();

            // Update metrics
            $this->updateMetrics($checkIn->user_id);

            // Create daily arrival data
            $this->createDailyArrivalData($checkIn);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Checked out successfully!',
                'data' => $checkIn,
                'hours_worked' => round($checkIn->hours_worked, 2),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to check out: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get today's check-in status (authenticated user)
     */
    public function getTodayStatus(Request $request)
    {
        $user = $request->user();

        $checkIn = CheckIn::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();

        if (!$checkIn) {
            return response()->json([
                'success' => true,
                'checked_in' => false,
                'checked_out' => false,
                'message' => 'No check-in record for today',
                'data' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'checked_in' => true,
            'checked_out' => $checkIn->check_out_time !== null,
            'data' => $checkIn,
            'status' => $checkIn->status,
            'hours_worked' => $checkIn->hours_worked,
        ]);
    }

    /**
     * Get check-in history for authenticated user
     */
    public function getHistory(Request $request)
    {
        $request->validate([
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|min:2000',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $user = $request->user();
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;
        $limit = $request->limit ?? 30;

        $checkIns = CheckIn::where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get();

        // Calculate summary
        $summary = [
            'total_days' => $checkIns->count(),
            'present_days' => $checkIns->where('status', 'CHECKED_OUT')->count(),
            'late_days' => $checkIns->where('is_late', true)->count(),
            'total_hours' => round($checkIns->sum('hours_worked'), 2),
            'average_hours' => round($checkIns->avg('hours_worked'), 2),
        ];

        return response()->json([
            'success' => true,
            'month' => $month,
            'year' => $year,
            'summary' => $summary,
            'data' => $checkIns,
        ]);
    }

    // ... (keep the private methods from before)
    private function updateMetrics($userId) { /* ... */ }
    private function createDailyArrivalData($checkIn) { /* ... */ }
}