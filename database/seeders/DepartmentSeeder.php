<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\CheckInSetting;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Operations Division',
                'code' => 'OPS',
                'standard_check_in' => '08:00',
                'standard_check_out' => '15:00',
                'description' => 'Day-to-day operations, supply chain, logistics',
            ],
            [
                'name' => 'Engineering',
                'code' => 'ENG',
                'standard_check_in' => '08:00',
                'standard_check_out' => '15:00',
                'description' => 'Design, development, technical operations',
            ],
            [
                'name' => 'Finance',
                'code' => 'FIN',
                'standard_check_in' => '08:00',
                'standard_check_out' => '15:00',
                'description' => 'Financial management, accounting, budgeting',
            ],
            [
                'name' => 'Human Resource & Administration Division',
                'code' => 'HRAD',
                'standard_check_in' => '08:00',
                'standard_check_out' => '15:00',
                'description' => 'HR, recruitment, admin, facility management',
            ],
            [
                'name' => 'Corporate Services',
                'code' => 'CORP',
                'standard_check_in' => '08:00',
                'standard_check_out' => '15:00',
                'description' => 'Strategy, business planning, corporate affairs',
            ],
            [
                'name' => 'Legal Services',
                'code' => 'LEGAL',
                'standard_check_in' => '08:00',
                'standard_check_out' => '15:00',
                'description' => 'Legal advice, compliance, contracts',
            ],
            [
                'name' => 'Infrastructure Services',
                'code' => 'INFRA',
                'standard_check_in' => '08:00',
                'standard_check_out' => '15:00',
                'description' => 'IT, facilities, security, maintenance',
            ],
            [
                'name' => 'Marine Operations',
                'code' => 'MARINE',
                'standard_check_in' => '07:30',
                'standard_check_out' => '16:00',
                'description' => 'Marine vessel operations and coordination',
            ],
            [
                'name' => 'Ferry Services',
                'code' => 'FERRY',
                'standard_check_in' => '06:00',
                'standard_check_out' => '18:00',
                'description' => 'Ferry operations and passenger services',
            ],
            [
                'name' => 'Container Terminal Department',
                'code' => 'CONTAINER',
                'standard_check_in' => '07:00',
                'standard_check_out' => '16:00',
                'description' => 'Container handling and terminal operations',
            ],
            [
                'name' => 'Conventional Cargo Department',
                'code' => 'CARGO',
                'standard_check_in' => '07:00',
                'standard_check_out' => '16:00',
                'description' => 'General cargo handling and documentation',
            ],
            [
                'name' => 'Inland Container Depots',
                'code' => 'ICD',
                'standard_check_in' => '08:00',
                'standard_check_out' => '15:00',
                'description' => 'Inland container depot operations',
            ],
        ];

        $this->command->info('📁 Creating departments...');

        foreach ($departments as $dept) {
            Department::updateOrCreate(
                ['code' => $dept['code']],
                $dept
            );
        }

        $this->command->info('✅ ' . count($departments) . ' departments created');

        $this->command->info('⚙️ Creating check-in settings...');

        $allDepartments = Department::all();

        foreach ($allDepartments as $dept) {
            CheckInSetting::updateOrCreate(
                ['department_id' => $dept->id],
                [
                    'standard_time' => $dept->standard_check_in,
                    'grace_minutes' => 15,
                    'late_threshold_minutes' => 30,
                ]
            );
        }

        $this->command->info('✅ ' . $allDepartments->count() . ' check-in settings created');
    }
}