@extends('layouts.app')

@section('title', 'Login - GateKeeper')

@section('content')
<div class="flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8 min-h-[calc(100vh-80px)]">
    <div class="w-full max-w-md bg-white/40 backdrop-blur-xl p-6 rounded-2xl shadow-xl animate-fade-in border border-white/30">
        <div class="text-center">
            <div class="flex justify-center">
                <div class="w-16 h-16 bg-white/50 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/30">
                    <svg class="h-10 w-10 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <h2 class="mt-4 text-3xl font-extrabold text-gray-900">Welcome Back</h2>
            <p class="mt-2 text-sm text-gray-600">Sign in to your account to continue</p>
        </div>

        <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        required
                        class="input-field mt-1 bg-white/50 backdrop-blur-sm border-white/30 focus:bg-white/70"
                        placeholder="you@example.com"
                    />
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        class="input-field mt-1 bg-white/50 backdrop-blur-sm border-white/30 focus:bg-white/70"
                        placeholder="••••••••"
                    />
                </div>
            </div>

            <div>
                <button type="submit" class="btn-primary w-full bg-linear-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 shadow-lg shadow-primary-500/30">
                    Sign In
                </button>
            </div>
        </form>
    </div>
</div>
@endsection