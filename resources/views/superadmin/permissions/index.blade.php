@extends('layouts.app')

@section('title', 'Manajemen Hak Akses')
@section('page-title', 'Manajemen Hak Akses')

@section('content')
<div class="space-y-6">
    <!-- Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex items-start">
            <span class="material-symbols-outlined text-blue-600 mr-3">info</span>
            <div>
                <p class="font-medium text-blue-800">Konfigurasi Hak Akses</p>
                <p class="text-sm text-blue-600">Atur permission yang dimiliki setiap role. Superadmin memiliki semua akses secara default.</p>
            </div>
        </div>
    </div>

    <!-- Permission Matrix -->
    @foreach($roles as $role)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-primary-50 to-white border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">{{ $role->display_name }}</h3>
                <p class="text-sm text-gray-500">{{ $role->description }}</p>
            </div>
            
            <form method="POST" action="{{ route('superadmin.permissions.update', $role) }}" class="p-6">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    @foreach($permissions as $category => $categoryPermissions)
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">{{ ucfirst($category) }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($categoryPermissions as $permission)
                                    <label class="flex items-center p-3 border border-gray-100 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input 
                                            type="checkbox" 
                                            name="permissions[]" 
                                            value="{{ $permission->id }}"
                                            {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}
                                            class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                                        >
                                        <span class="ml-3 text-sm text-gray-700">{{ $permission->description ?? $permission->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 pt-6 border-t border-gray-100">
                    <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    @endforeach
</div>
@endsection
