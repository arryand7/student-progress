@extends('layouts.app')

@section('title', 'Edit Pengguna')
@section('page-title', 'Edit Pengguna')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-3xl">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Edit Pengguna</h2>

        <form method="POST" action="{{ route('superadmin.users.update', $user) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru (opsional)</label>
                <input type="password" name="password" class="w-full px-3 py-2 border border-gray-200 rounded-lg" placeholder="Kosongkan jika tidak ingin mengganti">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($roles as $role)
                        <label class="flex items-center p-3 border border-gray-100 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input
                                type="checkbox"
                                name="roles[]"
                                value="{{ $role->id }}"
                                class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                                {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}
                            >
                            <span class="ml-3 text-sm text-gray-700">{{ $role->display_name ?? $role->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <label class="flex items-center text-sm text-gray-700">
                <input type="checkbox" name="is_active" value="1" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                <span class="ml-2">Aktif</span>
            </label>

            <div class="pt-4 border-t border-gray-100 flex gap-3">
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    Simpan Perubahan
                </button>
                <a href="{{ route('superadmin.users.index') }}" class="px-6 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
