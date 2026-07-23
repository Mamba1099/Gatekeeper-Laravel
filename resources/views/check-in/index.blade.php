<!-- resources/views/check-in/index.blade.php -->
@extends('layouts.main')

@section('title', 'Check In/Out - GateKeeper')

@section('content')
<div x-data="checkIn()" x-init="fetchStatus()" class="space-y-6">
    <h1 class="text-2xl font-semibold text-gray-900">Check In/Out</h1>

    <div class="bg-white p-8 rounded-lg shadow text-center">
        <div class="mb-6">
            <div class="text-6xl mb-4">
                <span x-show="status.checked_in">✅</span>
                <span x-show="!status.checked_in">⭕</span>
            </div>
            <h2 class="text-2xl font-bold">
                <span x-show="status.checked_in">You are checked in</span>
                <span x-show="!status.checked_in">You are not checked in</span>
            </h2>
            <p class="text-gray-600 mt-2" x-text="status.message || 'Ready to check in'"></p>
        </div>

        <div class="space-x-4">
            <button
                @click="handleCheckIn"
                :disabled="status.checked_in || loading"
                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50"
            >
                <span x-show="!loading">Check In</span>
                <span x-show="loading">Processing...</span>
            </button>
            <button
                @click="handleCheckOut"
                :disabled="!status.checked_in || loading"
                class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
            >
                <span x-show="!loading">Check Out</span>
                <span x-show="loading">Processing...</span>
            </button>
        </div>

        <!-- Today's Details -->
        <div x-show="status.data" class="mt-6 text-left">
            <h3 class="font-medium mb-2">Today's Details</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Check In:</span>
                    <span x-text="status.data?.check_in_time ? new Date(status.data.check_in_time).toLocaleString() : '-'"></span>
                </div>
                <div>
                    <span class="text-gray-500">Check Out:</span>
                    <span x-text="status.data?.check_out_time ? new Date(status.data.check_out_time).toLocaleString() : 'Not yet'"></span>
                </div>
                <div>
                    <span class="text-gray-500">Status:</span>
                    <span :class="statusClass(status.data?.status)" x-text="status.data?.status"></span>
                </div>
                <div>
                    <span class="text-gray-500">Hours Worked:</span>
                    <span x-text="status.data?.hours_worked ? status.data.hours_worked + 'h' : '-'"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- History -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium mb-4">Recent History</h3>
        <div class="space-y-2">
            <template x-for="record in history" :key="record.id">
                <div class="flex justify-between items-center border-b pb-2">
                    <div>
                        <span class="font-medium" x-text="new Date(record.date).toLocaleDateString()"></span>
                        <span class="ml-2 text-sm" :class="statusClass(record.status)" x-text="record.status"></span>
                    </div>
                    <span class="text-sm text-gray-500" x-text="record.hours_worked ? record.hours_worked + 'h' : '-'"></span>
                </div>
            </template>
            <div x-show="history.length === 0" class="text-center text-gray-500 py-4">
                No check-in history found
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('checkIn', () => ({
            status: {
                checked_in: false,
                checked_out: false,
                data: null,
                message: '',
            },
            history: [],
            loading: false,

            async fetchStatus() {
                try {
                    const response = await axios.get('/api/employee/today-status');
                    this.status = response.data;
                } catch (error) {
                    console.error('Failed to fetch status:', error);
                }
            },

            async fetchHistory() {
                try {
                    const response = await axios.get('/api/employee/history', {
                        params: { limit: 10 }
                    });
                    this.history = response.data.data || [];
                } catch (error) {
                    console.error('Failed to fetch history:', error);
                }
            },

            async handleCheckIn() {
                this.loading = true;
                try {
                    await axios.post('/api/employee/check-in');
                    await this.fetchStatus();
                    await this.fetchHistory();
                } catch (error) {
                    alert(error.response?.data?.message || 'Check-in failed');
                } finally {
                    this.loading = false;
                }
            },

            async handleCheckOut() {
                this.loading = true;
                try {
                    await axios.post('/api/employee/check-out');
                    await this.fetchStatus();
                    await this.fetchHistory();
                } catch (error) {
                    alert(error.response?.data?.message || 'Check-out failed');
                } finally {
                    this.loading = false;
                }
            },

            statusClass(status) {
                const classes = {
                    'CHECKED_IN': 'text-green-600',
                    'CHECKED_OUT': 'text-blue-600',
                    'LATE': 'text-yellow-600',
                    'ABSENT': 'text-red-600',
                };
                return classes[status] || 'text-gray-600';
            }
        }));
    });
</script>
@endpush
@endsection