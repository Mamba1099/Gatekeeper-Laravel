<!-- resources/views/layouts/dashboard.blade.php -->
@extends('layouts.app')

@section('content')
<style>
    .navbar-glass {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .sidebar-glass {
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-right: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .sidebar-glass .nav-link {
        transition: all 0.2s ease;
    }
    
    .sidebar-glass .nav-link:hover {
        background: rgba(255, 255, 255, 0.5);
    }
    
    .sidebar-glass .nav-link.active {
        background: rgba(255, 255, 255, 0.8);
        color: #1e293b;
    }
</style>

<div class="min-h-screen">
    <nav class="navbar-glass sticky top-0 z-50">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-linear-to-br from-primary-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h1 class="ml-2 text-xl font-bold text-gray-800">GateKeeper</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700" x-text="user?.full_name"></span>
                    <span class="text-xs bg-white/50 backdrop-blur-sm px-2 py-1 rounded-full text-gray-700 border border-white/30" x-text="user?.role"></span>
                    <button 
                        @click="logout()" 
                        class="text-gray-500 hover:text-gray-700 text-sm transition-colors"
                    >
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <aside class="sidebar-glass w-64 min-h-[calc(100vh-64px)] sticky top-16 overflow-y-auto">
            <nav class="mt-5 px-3">
                <a href="{{ route('dashboard') }}" 
                   class="nav-link flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'active bg-white/80 text-gray-900 shadow-sm' : 'text-gray-600 hover:bg-white/50' }}">
                    <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
                
                <a href="{{ route('check-in') }}" 
                   class="nav-link flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('check-in') ? 'active bg-white/80 text-gray-900 shadow-sm' : 'text-gray-600 hover:bg-white/50' }}">
                    <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Check In/Out
                </a>

                @if(auth()->user() && in_array(auth()->user()->role, ['ADMIN', 'HR']))
                    <a href="{{ route('employees.index') }}" 
                       class="nav-link flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('employees.*') ? 'active bg-white/80 text-gray-900 shadow-sm' : 'text-gray-600 hover:bg-white/50' }}">
                        <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Employees
                    </a>
                    
                    <a href="{{ route('departments.index') }}" 
                       class="nav-link flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('departments.*') ? 'active bg-white/80 text-gray-900 shadow-sm' : 'text-gray-600 hover:bg-white/50' }}">
                        <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Departments
                    </a>
                    
                    <a href="{{ route('reports.index') }}" 
                       class="nav-link flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('reports.*') ? 'active bg-white/80 text-gray-900 shadow-sm' : 'text-gray-600 hover:bg-white/50' }}">
                        <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Reports
                    </a>
                @endif
            </nav>
        </aside>
        <main class="flex-1 p-6">
            @yield('dashboard-content')
        </main>
    </div>
</div>
@endsection