@extends('layouts.app')

@section('title', 'Tambah Siswa')
@section('page-title', 'Tambah Siswa')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.students.store') }}">
        @csrf

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password (opsional)</label>
                <input type="password" name="password" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                <p class="text-xs text-gray-500 mt-1">Jika kosong, password akan dibuat otomatis (SSO disarankan).</p>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" value="1" class="w-4 h-4 text-primary-600 border-gray-300 rounded" checked>
                <span class="ml-2 text-sm text-gray-600">Aktif</span>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-4">
            <a href="{{ route('admin.students.index') }}" class="text-gray-600 hover:text-gray-800">Batal</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
