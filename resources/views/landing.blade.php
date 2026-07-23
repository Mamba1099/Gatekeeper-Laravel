@extends('layouts.app')

@section('title', 'GateKeeper - Smart Employee Check-in System')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto text-center relative z-10 w-full">
        <div class="animate-fade-in">
            <div class="flex justify-center mb-6">
                <div class="relative">
                    <div class="absolute -inset-4 bg-primary-500/20 rounded-full blur-2xl animate-pulse"></div>
                    <div class="relative w-20 h-20 bg-linear-to-br from-primary-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-xl">
                        <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight">
                Employee
                <span class="gradient-text">Check-in System</span>
            </h1>
            <p class="mt-4 text-xl text-gray-600 max-w-3xl mx-auto">
                Streamline attendance tracking, boost productivity, and gain valuable insights with our intelligent employee management platform.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('login') }}" class="btn-primary text-lg inline-flex items-center px-8 py-4 bg-linear-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-800 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    Get Started
                    <svg class="inline ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
            <div class="mt-10 flex flex-wrap justify-center items-center gap-6 text-sm text-gray-500">
                <span class="flex items-center">
                    <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Real-time Tracking
                </span>
                <span class="flex items-center">
                    <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Automated Reports
                </span>
                <span class="flex items-center">
                    <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Role-based Access
                </span>
            </div>
        </div>
    </div>
</div>
@endsection