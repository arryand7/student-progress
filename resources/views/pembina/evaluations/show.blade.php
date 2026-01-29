@extends('layouts.app')

@section('title', 'Detail Evaluasi')
@section('page-title', 'Detail Evaluasi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center">
                <div class="w-14 h-14 bg-primary-100 rounded-xl flex items-center justify-center mr-4">
                    <span class="text-primary-700 font-bold text-lg">{{ substr($evaluation->student->name, 0, 2) }}</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $evaluation->student->name }}</h2>
                    <p class="text-gray-500">{{ $evaluation->subject->name }} - {{ $evaluation->enrollment->program->name }}</p>
                </div>
            </div>
            <div class="flex flex-col items-end gap-2">
                <p class="text-2xl font-bold text-primary-600">{{ $evaluation->week_label }}</p>
                @if($evaluation->is_locked)
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-600">
                        <span class="material-symbols-outlined text-xs mr-1">lock</span> Locked
                    </span>
                    @if(auth()->user()->isSuperadmin())
                        <span class="text-xs text-gray-500">Evaluasi terkunci. Superadmin dapat unlock untuk edit.</span>
                    @else
                        <span class="text-xs text-gray-500">Evaluasi terkunci dan tidak dapat diedit.</span>
                    @endif
                    @if(auth()->user()->isSuperadmin())
                        <form method="POST" action="{{ route('superadmin.evaluations.unlock', $evaluation) }}">
                            @csrf
                            <input type="hidden" name="reason" value="Manual unlock by superadmin">
                            <button type="submit" class="inline-flex items-center px-3 py-1 rounded text-xs bg-amber-100 text-amber-700 hover:bg-amber-200">
                                <span class="material-symbols-outlined text-xs mr-1">lock_open</span> Unlock
                            </button>
                        </form>
                    @endif
                @else
                    <div class="flex items-center gap-2">
                        @if($evaluation->canBeEditedBy(auth()->user()))
                            <a href="{{ route('pembina.evaluations.edit', $evaluation) }}" class="inline-flex items-center px-3 py-1 rounded text-xs bg-blue-100 text-blue-700 hover:bg-blue-200">
                                <span class="material-symbols-outlined text-xs mr-1">edit</span> Edit
                            </a>
                        @else
                            <span class="text-xs text-gray-500">Edit hanya hari ini</span>
                        @endif
                        @if(auth()->user()->hasPermission('evaluation.edit_before_lock'))
                            <form method="POST" action="{{ route('pembina.evaluations.lock', $evaluation) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-1 rounded text-xs bg-gray-100 text-gray-700 hover:bg-gray-200">
                                    <span class="material-symbols-outlined text-xs mr-1">lock</span> Lock
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Total Score -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-primary-100 text-sm">Total Skor (Weighted)</p>
                <p class="text-4xl font-bold mt-1">{{ $evaluation->total_score ?? '-' }}</p>
            </div>
            <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl">trending_up</span>
            </div>
        </div>
    </div>

    <!-- Component Details -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Detail Komponen</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($evaluation->details as $detail)
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="font-medium text-gray-800">{{ $detail->component->name }}</h4>
                            <span class="text-sm text-gray-500">Bobot: {{ $detail->component->weight }}%</span>
                        </div>
                        <span class="text-2xl font-bold {{ ($detail->score ?? 0) >= 80 ? 'text-green-600' : (($detail->score ?? 0) >= 60 ? 'text-amber-600' : 'text-red-600') }}">
                            {{ $detail->score ?? '-' }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500">Waktu</p>
                            <p class="font-medium text-gray-700">{{ $detail->time_spent_minutes ? $detail->time_spent_minutes . ' menit' : '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500">Soal Dikerjakan</p>
                            <p class="font-medium text-gray-700">{{ $detail->attempted_questions ?? '-' }} / {{ $detail->total_questions ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500">Jawaban Benar</p>
                            <p class="font-medium text-gray-700">{{ $detail->correct_questions ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500">Akurasi</p>
                            <p class="font-medium text-gray-700">{{ $detail->accuracy_rate ? $detail->accuracy_rate . '%' : '-' }}</p>
                        </div>
                    </div>

                    @if($detail->notes)
                        <div class="mt-4 text-sm text-gray-600">
                            <strong>Catatan:</strong> {{ $detail->notes }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- General Notes -->
    @if($evaluation->notes)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-2">Catatan Umum</h3>
            <p class="text-gray-600">{{ $evaluation->notes }}</p>
        </div>
    @endif

    <!-- Meta Info -->
    <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-500">
        <div class="flex flex-wrap gap-4">
            <span>
                <strong>Evaluator:</strong> {{ $evaluation->evaluator->name }}
            </span>
            <span>
                <strong>Tanggal Input:</strong> {{ $evaluation->created_at->format('d M Y H:i') }}
            </span>
            @if($evaluation->is_locked)
                <span>
                    <strong>Dikunci oleh:</strong> {{ $evaluation->lockedByUser?->name ?? '-' }}
                </span>
            @endif
        </div>
    </div>

    <!-- Back Button -->
    <div>
        <a href="{{ route('pembina.evaluations.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-800">
            <span class="material-symbols-outlined mr-1">arrow_back</span>
            Kembali ke Daftar
        </a>
    </div>
</div>
@endsection
