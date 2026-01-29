@extends('layouts.app')

@section('title', 'Dashboard Pembina')
@section('page-title', 'Dashboard Pembina')

@section('content')
<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Siswa</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_students'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600">people</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Evaluasi Minggu Ini</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['evaluations_this_week'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-600">check_circle</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Belum Dievaluasi</p>
                    <p class="text-3xl font-bold text-amber-600 mt-1">{{ $stats['pending_evaluations'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-amber-600">pending</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Rata-rata Skor</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['average_score'] ?? '-' }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-purple-600">trending_up</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Cepat</h3>
            <div class="space-y-3">
                <a href="{{ route('pembina.evaluations.select-student') }}" class="flex items-center p-4 bg-primary-50 rounded-xl hover:bg-primary-100 transition-colors">
                    <span class="material-symbols-outlined text-primary-600 text-2xl mr-4">add_circle</span>
                    <div>
                        <p class="font-medium text-gray-800">Input Evaluasi Baru</p>
                        <p class="text-sm text-gray-500">Masukkan evaluasi mingguan siswa</p>
                    </div>
                </a>
                <a href="{{ route('pembina.progress.index') }}" class="flex items-center p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors">
                    <span class="material-symbols-outlined text-blue-600 text-2xl mr-4">insights</span>
                    <div>
                        <p class="font-medium text-gray-800">Lihat Progress Siswa</p>
                        <p class="text-sm text-gray-500">Analisis tren perkembangan siswa</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Evaluasi Pending</h3>
            @if(($stats['pending_evaluations'] ?? 0) > 0)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <div class="flex items-center text-amber-700">
                        <span class="material-symbols-outlined mr-2">warning</span>
                        <p class="font-medium">{{ $stats['pending_evaluations'] }} siswa belum dievaluasi minggu ini</p>
                    </div>
                    <a href="{{ route('pembina.evaluations.select-student') }}" class="mt-3 inline-flex items-center text-sm text-amber-700 hover:text-amber-800 font-medium">
                        Mulai evaluasi <span class="material-symbols-outlined text-sm ml-1">arrow_forward</span>
                    </a>
                </div>
            @else
                <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                    <div class="flex items-center text-green-700">
                        <span class="material-symbols-outlined mr-2">check_circle</span>
                        <p class="font-medium">Semua siswa sudah dievaluasi minggu ini!</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
