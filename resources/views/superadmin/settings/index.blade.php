@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan Sistem')

@section('content')
@php
    $general = $settings['general'] ?? [];
    $smtp = $settings['smtp'] ?? [];
    $sso = $settings['sso'] ?? [];
    $logoPath = $general['app_logo'] ?? null;
@endphp
<div class="space-y-6">
    <!-- General Settings -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-primary-50 to-white border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Pengaturan Umum</h3>
            <p class="text-sm text-gray-500">Nama aplikasi, deskripsi, dan logo.</p>
        </div>
        <form method="POST" action="{{ route('superadmin.settings.update') }}" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="general">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Aplikasi</label>
                    <input type="text" name="app_name" value="{{ old('app_name', $general['app_name'] ?? '') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tagline</label>
                    <input type="text" name="app_tagline" value="{{ old('app_tagline', $general['app_tagline'] ?? '') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea name="app_description" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg">{{ old('app_description', $general['app_description'] ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                    <input type="file" name="app_logo" class="w-full text-sm text-gray-600">
                    <p class="text-xs text-gray-500 mt-2">Format PNG/JPG, maksimal 2MB.</p>
                    @if($logoPath)
                        <label class="flex items-center mt-3 text-sm text-gray-600">
                            <input type="checkbox" name="remove_logo" value="1" class="mr-2">
                            Hapus logo saat ini
                        </label>
                    @endif
                </div>
                <div class="flex items-center justify-center border border-gray-100 rounded-lg p-4 bg-gray-50">
                    @if($logoPath)
                        <img src="{{ asset('storage/' . $logoPath) }}" alt="Logo" class="h-16 object-contain">
                    @else
                        <div class="text-sm text-gray-400">Belum ada logo</div>
                    @endif
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100">
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    Simpan Pengaturan Umum
                </button>
            </div>
        </form>
    </div>

    <!-- SMTP Settings -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-primary-50 to-white border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Pengaturan SMTP</h3>
            <p class="text-sm text-gray-500">Atur pengiriman email melalui server SMTP.</p>
        </div>
        <form method="POST" action="{{ route('superadmin.settings.update') }}" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="smtp">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Host</label>
                    <input type="text" name="smtp_host" value="{{ old('smtp_host', $smtp['host'] ?? '') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Port</label>
                    <input type="number" name="smtp_port" value="{{ old('smtp_port', $smtp['port'] ?? '') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <input type="text" name="smtp_username" value="{{ old('smtp_username', $smtp['username'] ?? '') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="smtp_password" class="w-full px-3 py-2 border border-gray-200 rounded-lg" placeholder="{{ isset($smtp['password']) ? '******** (tersimpan)' : '' }}">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengganti password.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Enkripsi</label>
                    <select name="smtp_encryption" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                        @php
                            $smtpEnc = old('smtp_encryption', $smtp['encryption'] ?? '');
                        @endphp
                        <option value="none" {{ $smtpEnc === null || $smtpEnc === '' ? 'selected' : '' }}>Tidak ada</option>
                        <option value="tls" {{ $smtpEnc === 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ $smtpEnc === 'ssl' ? 'selected' : '' }}>SSL</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Name</label>
                    <input type="text" name="smtp_from_name" value="{{ old('smtp_from_name', $smtp['from_name'] ?? '') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">From Address</label>
                <input type="email" name="smtp_from_address" value="{{ old('smtp_from_address', $smtp['from_address'] ?? '') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
            </div>

            <div class="pt-4 border-t border-gray-100">
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    Simpan Pengaturan SMTP
                </button>
            </div>
        </form>
    </div>

    <!-- SSO Settings -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-primary-50 to-white border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Pengaturan SSO</h3>
            <p class="text-sm text-gray-500">Atur koneksi ke Gate SSO.</p>
        </div>
        @php
            $appUrl = rtrim(config('app.url') ?: request()->getSchemeAndHttpHost(), '/');
            $loginUrl = $appUrl . '/sso/login';
            $callbackUrl = $appUrl . '/sso/callback';
        @endphp
        <div class="px-6 py-4 border-b border-gray-100 bg-amber-50/60">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-amber-600 mt-0.5">info</span>
                <div class="text-sm text-amber-900 space-y-2">
                    <p class="font-medium">Panduan untuk konfigurasi di aplikasi SSO:</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="p-3 bg-white border border-amber-100 rounded-lg">
                            <p class="text-xs text-amber-700 uppercase tracking-wider">Base URL</p>
                            <p class="font-mono text-amber-900 break-all">{{ $appUrl }}</p>
                        </div>
                        <div class="p-3 bg-white border border-amber-100 rounded-lg">
                            <p class="text-xs text-amber-700 uppercase tracking-wider">Redirect URI</p>
                            <p class="font-mono text-amber-900 break-all">{{ $callbackUrl }}</p>
                        </div>
                        <div class="p-3 bg-white border border-amber-100 rounded-lg">
                            <p class="text-xs text-amber-700 uppercase tracking-wider">SSO Login URL</p>
                            <p class="font-mono text-amber-900 break-all">{{ $loginUrl }}</p>
                        </div>
                    </div>
                    <p class="text-xs text-amber-800">Gunakan nilai di atas saat mendaftarkan aplikasi ini pada Gate SSO.</p>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('superadmin.settings.update') }}" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="sso">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Base URL</label>
                    <input type="url" name="sso_base_url" value="{{ old('sso_base_url', $sso['base_url'] ?? '') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Client ID</label>
                    <input type="text" name="sso_client_id" value="{{ old('sso_client_id', $sso['client_id'] ?? '') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Client Secret</label>
                    <input type="password" name="sso_client_secret" class="w-full px-3 py-2 border border-gray-200 rounded-lg" placeholder="{{ isset($sso['client_secret']) ? '******** (tersimpan)' : '' }}">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengganti secret.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Redirect URI</label>
                    <input type="url" name="sso_redirect_uri" value="{{ old('sso_redirect_uri', $sso['redirect_uri'] ?? '') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Authorize Endpoint</label>
                    <input type="text" name="sso_authorize_endpoint" value="{{ old('sso_authorize_endpoint', $sso['authorize_endpoint'] ?? '') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Token Endpoint</label>
                    <input type="text" name="sso_token_endpoint" value="{{ old('sso_token_endpoint', $sso['token_endpoint'] ?? '') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Userinfo Endpoint</label>
                    <input type="text" name="sso_userinfo_endpoint" value="{{ old('sso_userinfo_endpoint', $sso['userinfo_endpoint'] ?? '') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Scopes</label>
                <input type="text" name="sso_scopes" value="{{ old('sso_scopes', $sso['scopes'] ?? '') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg" placeholder="openid profile email">
            </div>

            <div class="pt-4 border-t border-gray-100">
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    Simpan Pengaturan SSO
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
