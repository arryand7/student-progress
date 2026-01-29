@extends('layouts.app')

@section('title', 'Edit Komponen')
@section('page-title', 'Edit Komponen')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.subjects.components.update', [$subject, $component]) }}">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name', $component->name) }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg">{{ old('description', $component->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bobot (%)</label>
                <input type="number" name="weight" step="0.01" min="0" max="100" value="{{ old('weight', $component->weight) }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Urutan (opsional)</label>
                <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $component->sort_order) }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" value="1" class="w-4 h-4 text-primary-600 border-gray-300 rounded" {{ $component->is_active ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">Aktif</span>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-4">
            <a href="{{ route('admin.subjects.components.index', $subject) }}" class="text-gray-600 hover:text-gray-800">Batal</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
