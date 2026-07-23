<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EmployeeMetrics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * List all employees
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::where('role', 'EMPLOYEE')->with('department');

        // Filter by department
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $employees = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $employees,
        ]);
    }

    /**
     * Create new employee
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'full_name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'position' => 'nullable|string|max:255',
            'role' => 'required|in:EMPLOYEE,HR,ADMIN',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'full_name' => $request->full_name,
                'department_id' => $request->department_id,
                'position' => $request->position,
                'role' => $request->role,
                'is_active' => true,
            ]);

            // Create metrics record
            EmployeeMetrics::create(['user_id' => $user->id]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Employee created successfully!',
                'data' => $user->load('department'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create employee: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single employee
     */
    public function show(string $id): JsonResponse
    {
        $employee = User::with(['department', 'employeeMetrics'])
            ->where('role', 'EMPLOYEE')
            ->find($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $employee,
        ]);
    }

    /**
     * Update employee
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $employee = User::where('role', 'EMPLOYEE')->find($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        $request->validate([
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'full_name' => 'sometimes|string|max:255',
            'department_id' => 'sometimes|exists:departments,id',
            'position' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($request->has('password')) {
            $request->validate(['password' => 'string|min:8']);
            $employee->password = Hash::make($request->password);
        }

        $employee->update($request->except(['password']));

        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully!',
            'data' => $employee->fresh('department'),
        ]);
    }

    /**
     * Delete employee
     */
    public function destroy(string $id): JsonResponse
    {
        $employee = User::where('role', 'EMPLOYEE')->find($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully!',
        ]);
    }

    /**
     * Get employee metrics
     */
    public function metrics(string $id): JsonResponse
    {
        $employee = User::with('employeeMetrics')->find($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'employee' => $employee,
                'metrics' => $employee->employeeMetrics,
            ],
        ]);
    }

    /**
     * Bulk import employees
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'employees' => 'required|array',
            'employees.*.email' => 'required|email|unique:users,email',
            'employees.*.full_name' => 'required|string',
            'employees.*.department_id' => 'required|exists:departments,id',
        ]);

        DB::beginTransaction();

        try {
            $created = [];
            foreach ($request->employees as $employeeData) {
                $user = User::create([
                    'email' => $employeeData['email'],
                    'password' => Hash::make('password123'), // Default password
                    'full_name' => $employeeData['full_name'],
                    'department_id' => $employeeData['department_id'],
                    'position' => $employeeData['position'] ?? null,
                    'role' => 'EMPLOYEE',
                    'is_active' => true,
                ]);

                EmployeeMetrics::create(['user_id' => $user->id]);
                $created[] = $user;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($created) . ' employees imported successfully!',
                'data' => $created,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to import employees: ' . $e->getMessage(),
            ], 500);
        }
    }
}