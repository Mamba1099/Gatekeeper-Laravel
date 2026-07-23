<!-- resources/views/auth/login.blade.php -->
@extends('layouts.app')

@section('title', 'Login - GateKeeper')

@section('content')
<div x-data="login()" class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                GateKeeper
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Employee Check-in System
            </p>
        </div>

        <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input
                        id="email"
                        x-model="form.email"
                        type="email"
                        required
                        class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                        placeholder="Email address"
                    />
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input
                        id="password"
                        x-model="form.password"
                        type="password"
                        required
                        class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                        placeholder="Password"
                    />
                </div>
            </div>

            <div>
                <button
                    type="submit"
                    :disabled="loading"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50"
                >
                    <span x-show="!loading">Sign in</span>
                    <span x-show="loading">Signing in...</span>
                </button>
            </div>

            <div x-show="error" class="text-red-500 text-sm text-center" x-text="error"></div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('login', () => ({
            form: {
                email: '',
                password: '',
            },
            loading: false,
            error: '',

            async handleLogin() {
                this.loading = true;
                this.error = '';

                try {
                    const response = await axios.post('/api/login', this.form);
                    
                    if (response.data.success) {
                        localStorage.setItem('auth_token', response.data.token);
                        window.location.href = '/';
                    }
                } catch (error) {
                    this.error = error.response?.data?.message || 'Login failed';
                } finally {
                    this.loading = false;
                }
            }
        }));
    });
</script>
@endpush
@endsection