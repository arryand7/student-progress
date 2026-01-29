@extends('layouts.app')

@section('title', 'Detail Pendaftaran')
@section('page-title', 'Detail Pendaftaran')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-bold text-gray-800">{{ $enrollment->user->name }}</h2>
        <p class="text-gray-500">{{ $enrollment->user->email }}</p>
        <div class="mt-2 text-sm text-gray-600">
            Program: {{ $enrollment->program->name ?? '-' }}<br>
            Mata Pelajaran: {{ $enrollment->subject->name ?? '-' }}<br>
            Status: {{ $enrollment->isActive() ? 'Aktif' : 'Nonaktif' }}<br>
            Tanggal: {{ $enrollment->enrolled_at->format('d M Y') }}
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Evaluasi</h3>
        @if($enrollment->evaluations->count() > 0)
            <div class="space-y-2">
                @foreach($enrollment->evaluations as $evaluation)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800">Week {{ $evaluation->week_number }}, {{ $evaluation->year }}</p>
                            <p class="text-sm text-gray-500">Skor: {{ $evaluation->total_score ?? '-' }}</p>
                        </div>
                        @if($evaluation->is_locked)
                            <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600">Locked</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-xs bg-green-100 text-green-700">Open</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">Belum ada evaluasi.</p>
        @endif
    </div>

    <div>
        <a href="{{ route('admin.enrollments.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-800">
            <span class="material-symbols-outlined mr-1">arrow_back</span>
            Kembali
        </a>
    </div>
</div>
@endsection
