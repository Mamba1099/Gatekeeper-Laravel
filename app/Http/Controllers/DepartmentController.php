<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\CheckInSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;


class DepartmentController extends Controller
{
    /**
     * List all departments
     */
    public function index(Request $request): JsonResponse
    {
        $query = Department::withCount('users');

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        $departments = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $departments,
        ]);
    }

    /**
     * Create new department
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'code' => 'required|string|max:50|unique:departments,code',
            'description' => 'nullable|string',
            'standard_check_in' => 'sometimes|date_format:H:i',
            'standard_check_out' => 'sometimes|date_format:H:i',
            'grace_minutes' => 'sometimes|integer|min:0',
            'late_threshold' => 'sometimes|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $department = Department::create($request->all());

            // Create check-in settings
            CheckInSetting::create([
                'department_id' => $department->id,
                'standard_time' => $request->standard_check_in ?? '09:00',
                'grace_minutes' => $request->grace_minutes ?? 15,
                'late_threshold_minutes' => $request->late_threshold ?? 30,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Department created successfully!',
                'data' => $department,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create department: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single department
     */
    public function show(string $id): JsonResponse
    {
        $department = Department::with(['users', 'checkInSetting'])->find($id);

        if (!$department) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $department,
        ]);
    }

    /**
     * Update department
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $department = Department::find($id);

        if (!$department) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found',
            ], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255|unique:departments,name,' . $id,
            'code' => 'sometimes|string|max:50|unique:departments,code,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $department->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Department updated successfully!',
            'data' => $department->fresh(),
        ]);
    }

    /**
     * Delete department
     */
    public function destroy(string $id): JsonResponse
    {
        $department = Department::find($id);

        if (!$department) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found',
            ], 404);
        }

        // Check if department has employees
        if ($department->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete department with active employees. Move or deactivate employees first.',
            ], 400);
        }

        $department->delete();

        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully!',
        ]);
    }

    /**
     * Get department statistics
     */
    public function statistics(string $id): JsonResponse
    {
        $department = Department::with(['users' => function($query) {
            $query->with('employeeMetrics');
        }])->find($id);

        if (!$department) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found',
            ], 404);
        }

        $stats = [
            'total_employees' => $department->users->count(),
            'active_employees' => $department->users->where('is_active', true)->count(),
            'average_attendance' => $department->users->avg(function($user) {
                return $user->employeeMetrics?->attendance_percentage ?? 0;
            }),
            'department' => $department,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}