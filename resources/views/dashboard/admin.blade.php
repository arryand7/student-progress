@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Program</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_programs'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600">category</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Mata Pelajaran</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_subjects'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-600">menu_book</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Siswa</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_students'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-purple-600">people</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Rata-rata Skor</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['average_score'] ?? '-' }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-amber-600">trending_up</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @if($user->hasPermission('program.create'))
                <a href="{{ route('admin.programs.create') }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                    <span class="material-symbols-outlined text-primary-600 text-3xl mb-2">add_circle</span>
                    <span class="text-sm font-medium text-gray-700">Tambah Program</span>
                </a>
            @endif
            @if($user->hasPermission('subject.create'))
                <a href="{{ route('admin.subjects.create') }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                    <span class="material-symbols-outlined text-primary-600 text-3xl mb-2">library_add</span>
                    <span class="text-sm font-medium text-gray-700">Tambah Mapel</span>
                </a>
            @endif
            @if($user->hasPermission('enrollment.assign_subject'))
                <a href="{{ route('admin.enrollments.create') }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                    <span class="material-symbols-outlined text-primary-600 text-3xl mb-2">person_add</span>
                    <span class="text-sm font-medium text-gray-700">Daftarkan Siswa</span>
                </a>
            @endif
            @if($user->hasPermission('evaluation.create') && ($user->isSuperadmin() || $user->isPembina()))
                <a href="{{ route('pembina.evaluations.select-student') }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                    <span class="material-symbols-outlined text-primary-600 text-3xl mb-2">assignment</span>
                    <span class="text-sm font-medium text-gray-700">Input Evaluasi</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan</h3>
        <div class="text-center py-8 text-gray-500">
            <span class="material-symbols-outlined text-5xl text-gray-300 mb-3">info</span>
            <p>Total {{ $stats['total_evaluations'] ?? 0 }} evaluasi tahun ini</p>
        </div>
    </div>
</div>
@endsection
