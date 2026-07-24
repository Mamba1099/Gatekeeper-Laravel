<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $hrEmail = env('HR_EMAIL');
        $hrPassword = env('HR_PASSWORD');
        $hrFullName = env('HR_FULL_NAME');

        $hrDept = Department::where('code', 'HRAD')->first();

        if (!$hrDept) {
            $this->command->error(' HRAD department not found. Please run DepartmentSeeder first.');
            return;
        }

        $this->command->info('👤 Creating HR user...');

        User::updateOrCreate(
            ['email' => $hrEmail],
            [
                'password' => Hash::make($hrPassword),
                'full_name' => $hrFullName,
                'role' => 'HR',
                'department_id' => $hrDept->id,
                'position' => 'Human Resources Manager',
                'is_active' => true,
            ]
        );

        $this->command->info('✅ HR user created: ' . $hrEmail);
        $departments = Department::orderBy('name')->get();

        $this->command->info("\n✅✅✅ Seeding Complete!\n");
        $this->command->info("  HR: {$hrEmail} / {$hrPassword}\n");
        $this->command->info("DEPARTMENTS:");
        
        foreach ($departments as $index => $dept) {
            $this->command->info(
                "  " . ($index + 1) . ". {$dept->name} ({$dept->code}) - {$dept->standard_check_in} to {$dept->standard_check_out}"
            );
        }
    }
}