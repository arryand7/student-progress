@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-8 py-10 text-center">
            <div class="w-20 h-20 bg-white/20 rounded-2xl mx-auto flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-white text-4xl">school</span>
            </div>
            <h1 class="text-2xl font-bold text-white">Elite Class Progress</h1>
            <p class="text-primary-100 mt-2">MA Unggul SABIRA</p>
        </div>

        <!-- Form -->
        <div class="px-8 py-10">
            <div class="mb-6">
                <a 
                    href="{{ route('sso.login') }}"
                    class="w-full inline-flex items-center justify-center bg-white border border-gray-200 text-gray-700 py-3 px-4 rounded-xl font-semibold hover:bg-gray-50 transition-all"
                >
                    <span class="material-symbols-outlined mr-2">verified_user</span>
                    Masuk dengan SSO SABIRA
                </a>
            </div>

            <div class="flex items-center my-6">
                <div class="flex-1 border-t border-gray-200"></div>
                <span class="px-3 text-xs text-gray-400 uppercase">atau</span>
                <div class="flex-1 border-t border-gray-200"></div>
            </div>

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <span class="material-symbols-outlined text-xl">mail</span>
                        </span>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('email') border-red-500 @enderror"
                            placeholder="email@sabira.sch.id"
                            required
                            autofocus
                        >
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative" x-data="{ showPassword: false }">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <span class="material-symbols-outlined text-xl">lock</span>
                        </span>
                        <input 
                            :type="showPassword ? 'text' : 'password'" 
                            id="password" 
                            name="password" 
                            class="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                            placeholder="••••••••"
                            required
                        >
                        <button 
                            type="button" 
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                        >
                            <span class="material-symbols-outlined text-xl" x-text="showPassword ? 'visibility_off' : 'visibility'"></span>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                        <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white py-3 px-4 rounded-xl font-semibold hover:from-primary-700 hover:to-primary-800 focus:ring-4 focus:ring-primary-200 transition-all duration-200 flex items-center justify-center"
                >
                    <span class="material-symbols-outlined mr-2">login</span>
                    Masuk
                </button>
            </form>

            <!-- Demo Accounts Info -->
            <div class="mt-8 p-4 bg-gray-50 rounded-xl">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Demo Accounts</p>
                <div class="space-y-2 text-sm text-gray-600">
                    <p><strong>Superadmin:</strong> superadmin@sabira.sch.id</p>
                    <p><strong>Admin:</strong> admin@sabira.sch.id</p>
                    <p><strong>Pembina:</strong> pembina@sabira.sch.id</p>
                    <p><strong>Siswa:</strong> siswa@sabira.sch.id</p>
                    <p class="text-gray-400">Password: password</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
