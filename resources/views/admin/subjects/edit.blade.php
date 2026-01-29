@extends('layouts.app')

@section('title', 'Edit Mata Pelajaran')
@section('page-title', 'Edit Mata Pelajaran')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.subjects.update', $subject) }}">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Program</label>
                <select name="program_id" class="w-full px-4 py-2 border border-gray-200 rounded-lg" required>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}" {{ (old('program_id', $subject->program_id) == $program->id) ? 'selected' : '' }}>
                            {{ $program->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name', $subject->name) }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode</label>
                <input type="text" name="code" value="{{ old('code', $subject->code) }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg">{{ old('description', $subject->description) }}</textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" value="1" class="w-4 h-4 text-primary-600 border-gray-300 rounded" {{ $subject->is_active ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">Aktif</span>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-4">
            <a href="{{ route('admin.subjects.index') }}" class="text-gray-600 hover:text-gray-800">Batal</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
