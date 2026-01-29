@extends('layouts.app')

@section('title', 'Progress Saya')
@section('page-title', 'Progress Saya')

@section('content')
<div class="space-y-6">
    <!-- Overall Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Mata Pelajaran</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_subjects'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600">menu_book</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Evaluasi</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['evaluations_this_year'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-600">assignment</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Rata-rata Skor</p>
                    <p class="text-3xl font-bold text-primary-600 mt-1">{{ $stats['average_score'] ?? '-' }}</p>
                </div>
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary-600">trending_up</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Subject Progress Cards -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Progress Per Mata Pelajaran</h3>
        
        @if($enrollments->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($enrollments as $enrollment)
                    <a href="{{ route('student.progress', $enrollment) }}" class="block p-4 border border-gray-200 rounded-xl hover:border-primary-300 hover:shadow-md transition-all">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $enrollment->subject->name }}</h4>
                                <p class="text-sm text-gray-500">{{ $enrollment->program->name }}</p>
                            </div>
                            @if($enrollment->evaluations->first())
                                <span class="px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-sm font-medium">
                                    {{ $enrollment->evaluations->first()->total_score ?? '-' }}
                                </span>
                            @endif
                        </div>
                        
                        @if($enrollment->evaluations->count() > 0)
                            <div class="flex items-center text-sm text-gray-500">
                                <span class="material-symbols-outlined text-sm mr-1">calendar_today</span>
                                Evaluasi terakhir: Week {{ $enrollment->evaluations->first()->week_number }}
                            </div>
                        @else
                            <div class="text-sm text-gray-400">Belum ada evaluasi</div>
                        @endif
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <span class="material-symbols-outlined text-5xl text-gray-300 mb-3">info</span>
                <p>Anda belum terdaftar pada mata pelajaran apapun.</p>
            </div>
        @endif
    </div>

    <!-- Latest Scores -->
    @if(!empty($stats['latest_scores']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Skor Terbaru</h3>
            <div class="space-y-3">
                @foreach($stats['latest_scores'] as $score)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800">{{ $score['subject'] }}</p>
                            <p class="text-sm text-gray-500">Week {{ $score['week'] }}</p>
                        </div>
                        <span class="text-lg font-bold {{ $score['score'] >= 80 ? 'text-green-600' : ($score['score'] >= 60 ? 'text-amber-600' : 'text-red-600') }}">
                            {{ $score['score'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
