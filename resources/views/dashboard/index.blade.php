<!-- resources/views/dashboard/index.blade.php -->
@extends('layouts.main')

@section('title', 'Dashboard - GateKeeper')

@section('content')
<div x-data="dashboard()" x-init="fetchData()" class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
        <span class="text-sm text-gray-500">
            Last updated: <span x-text="new Date().toLocaleString()"></span>
        </span>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-500">Total Employees</p>
                    <p class="text-xl font-semibold" x-text="stats.total_employees || 0"></p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-500">Today's Check-ins</p>
                    <p class="text-xl font-semibold" x-text="stats.today_checkins || 0"></p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-500">Average Attendance</p>
                    <p class="text-xl font-semibold" x-text="stats.avg_attendance + '%' || '0%'"></p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-lg">
                    <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-500">Active Employees</p>
                    <p class="text-xl font-semibold" x-text="stats.active_employees || 0"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium mb-4">Recent Activity</h3>
        <div class="space-y-4">
            <template x-for="activity in recentActivity" :key="activity.id">
                <div class="flex items-center justify-between border-b pb-2">
                    <div>
                        <span class="font-medium" x-text="activity.user"></span>
                        <span class="text-sm text-gray-500 ml-2" x-text="activity.action"></span>
                    </div>
                    <span class="text-sm text-gray-500" x-text="new Date(activity.time).toLocaleTimeString()"></span>
                </div>
            </template>
            <div x-show="recentActivity.length === 0" class="text-center text-gray-500 py-4">
                No recent activity
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboard', () => ({
            stats: {},
            recentActivity: [],
            loading: true,

            async fetchData() {
                try {
                    const response = await axios.get('/api/admin/dashboard');
                    const data = response.data.data;
                    
                    this.stats = {
                        total_employees: data.overview.total_employees,
                        today_checkins: data.overview.today_checkins,
                        avg_attendance: data.overview.avg_attendance,
                        active_employees: data.overview.active_employees,
                    };
                    
                    this.recentActivity = data.recent_activity || [];
                } catch (error) {
                    console.error('Failed to fetch dashboard data:', error);
                } finally {
                    this.loading = false;
                }
            }
        }));
    });
</script>
@endpush
@endsection