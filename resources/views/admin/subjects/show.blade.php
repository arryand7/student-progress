@extends('layouts.app')

@section('title', 'Detail Mata Pelajaran')
@section('page-title', $subject->name)

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center space-x-3">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $subject->name }}</h2>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm font-mono">{{ $subject->code }}</span>
                    @if($subject->is_active)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Nonaktif</span>
                    @endif
                </div>
                <p class="mt-2 text-gray-600">Program: {{ $subject->program->name ?? '-' }}</p>
                @if($subject->description)
                    <p class="mt-2 text-gray-600">{{ $subject->description }}</p>
                @endif
            </div>
            <a href="{{ route('admin.subjects.edit', $subject) }}" class="inline-flex items-center px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50">
                <span class="material-symbols-outlined mr-2">edit</span>
                Edit
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Komponen</h3>
            <a href="{{ route('admin.subjects.components.index', $subject) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                Kelola Komponen
            </a>
        </div>

        @if($subject->components->count() > 0)
            <div class="space-y-3">
                @foreach($subject->components as $component)
                    <div class="flex items-center justify-between p-4 border border-gray-100 rounded-lg hover:bg-gray-50">
                        <div>
                            <p class="font-medium text-gray-800">{{ $component->name }}</p>
                            <p class="text-sm text-gray-500">Bobot {{ $component->weight }}%</p>
                        </div>
                        <div>
                            @if($component->is_active)
                                <span class="px-2 py-0.5 rounded text-xs bg-green-100 text-green-700">Aktif</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600">Nonaktif</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">Belum ada komponen.</p>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Siswa Terdaftar ({{ $subject->enrollments->count() }})</h3>
        @if($subject->enrollments->count() > 0)
            <div class="space-y-2">
                @foreach($subject->enrollments->take(10) as $enrollment)
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-primary-700 font-semibold text-sm">{{ substr($enrollment->user->name, 0, 2) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $enrollment->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $enrollment->user->email }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">Belum ada siswa terdaftar.</p>
        @endif
    </div>

    <div>
        <a href="{{ route('admin.subjects.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-800">
            <span class="material-symbols-outlined mr-1">arrow_back</span>
            Kembali ke Daftar
        </a>
    </div>
</div>
@endsection
