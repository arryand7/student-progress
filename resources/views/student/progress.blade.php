@extends('layouts.app')

@section('title', 'Progress ' . $enrollment->subject->name)
@section('page-title', 'Progress Saya')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $enrollment->subject->name }}</h2>
                <p class="text-gray-500">{{ $enrollment->program->name }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Tahun {{ $year }}</p>
                <p class="text-2xl font-bold text-primary-600">{{ count($weeklyTrend) }} evaluasi</p>
            </div>
        </div>
    </div>

    <!-- Weekly Trend Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Mingguan Saya</h3>
        @if(count($weeklyTrend) > 0)
            <div class="h-80">
                <canvas id="weeklyTrendChart"></canvas>
            </div>
        @else
            <p class="text-center text-gray-500 py-8">Belum ada data evaluasi.</p>
        @endif
    </div>

    <!-- Component Averages -->
    @if(count($componentAverages) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Rata-rata Per Komponen</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($componentAverages as $comp)
                    <div class="border border-gray-100 rounded-xl p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium text-gray-800">{{ $comp['component_name'] }}</h4>
                            <span class="px-2 py-0.5 bg-gray-100 rounded text-xs text-gray-600">{{ $comp['weight'] }}%</span>
                        </div>
                        <p class="text-3xl font-bold {{ ($comp['avg_score'] ?? 0) >= 80 ? 'text-green-600' : (($comp['avg_score'] ?? 0) >= 60 ? 'text-amber-600' : 'text-red-600') }}">
                            {{ $comp['avg_score'] ?? '-' }}
                        </p>
                        <div class="mt-3 text-sm text-gray-500">
                            <p>Berdasarkan {{ $comp['evaluation_count'] }} evaluasi</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Latest Evaluation -->
    @if($latestEvaluation)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Evaluasi Terakhir (Week {{ $latestEvaluation->week_number }})</h3>
                <span class="text-2xl font-bold text-primary-600">{{ $latestEvaluation->total_score }}</span>
            </div>
            
            <div class="space-y-3">
                @foreach($componentBreakdown as $comp)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-700">{{ $comp['component_name'] }}</span>
                        <span class="font-bold {{ ($comp['score'] ?? 0) >= 80 ? 'text-green-600' : (($comp['score'] ?? 0) >= 60 ? 'text-amber-600' : 'text-red-600') }}">
                            {{ $comp['score'] ?? '-' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Back -->
    <div>
        <a href="{{ route('student.dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-gray-800">
            <span class="material-symbols-outlined mr-1">arrow_back</span>
            Kembali ke Dashboard
        </a>
    </div>
</div>

@if(count($weeklyTrend) > 0)
@push('scripts')
<script>
    const weeklyData = @json($weeklyTrend);
    const weeklyCtx = document.getElementById('weeklyTrendChart').getContext('2d');
    
    new Chart(weeklyCtx, {
        type: 'line',
        data: {
            labels: weeklyData.map(d => 'Week ' + d.week),
            datasets: [{
                label: 'Skor Saya',
                data: weeklyData.map(d => d.total_score),
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22, 163, 74, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#16a34a',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        color: '#f3f4f6'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endpush
@endif
@endsection
