@extends('layouts.app')

@section('title', 'Progress Siswa')
@section('page-title', 'Progress Siswa - ' . $enrollment->user->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-14 h-14 bg-primary-100 rounded-xl flex items-center justify-center mr-4">
                    <span class="text-primary-700 font-bold text-lg">{{ substr($enrollment->user->name, 0, 2) }}</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $enrollment->user->name }}</h2>
                    <p class="text-gray-500">{{ $enrollment->subject->name }} - {{ $enrollment->program->name }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Tahun {{ $year }}</p>
                <p class="text-2xl font-bold text-primary-600">{{ count($weeklyTrend) }} evaluasi</p>
            </div>
        </div>
    </div>

    <!-- Weekly Trend Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Mingguan</h3>
        <div class="h-80">
            <canvas id="weeklyTrendChart"></canvas>
        </div>
    </div>

    <!-- Component Averages -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Rata-rata Per Komponen</h3>
        
        @if(count($componentAverages) > 0)
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
                        <div class="mt-3 space-y-1 text-sm text-gray-500">
                            <div class="flex justify-between">
                                <span>Rata-rata Waktu:</span>
                                <span>{{ $comp['avg_time'] ? round($comp['avg_time']) . ' menit' : '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Akurasi:</span>
                                <span>{{ $comp['avg_accuracy_rate'] ? $comp['avg_accuracy_rate'] . '%' : '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Data dari:</span>
                                <span>{{ $comp['evaluation_count'] }} evaluasi</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500 py-8">Belum ada data komponen.</p>
        @endif
    </div>

    <!-- Latest Evaluation Breakdown -->
    @if($latestEvaluation && count($componentBreakdown) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Breakdown Evaluasi Terakhir (Week {{ $latestEvaluation->week_number }})</h3>
            <div class="h-64">
                <canvas id="componentBreakdownChart"></canvas>
            </div>
        </div>
    @endif

    <!-- Back -->
    <div>
        <a href="{{ route('pembina.progress.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-800">
            <span class="material-symbols-outlined mr-1">arrow_back</span>
            Kembali
        </a>
    </div>
</div>

@push('scripts')
<script>
    // Weekly Trend Chart
    const weeklyData = @json($weeklyTrend);
    const weeklyCtx = document.getElementById('weeklyTrendChart').getContext('2d');
    
    new Chart(weeklyCtx, {
        type: 'line',
        data: {
            labels: weeklyData.map(d => 'Week ' + d.week),
            datasets: [{
                label: 'Total Skor',
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

    // Component Breakdown Chart
    @if($latestEvaluation && count($componentBreakdown) > 0)
        const compData = @json($componentBreakdown);
        const compCtx = document.getElementById('componentBreakdownChart').getContext('2d');
        
        new Chart(compCtx, {
            type: 'radar',
            data: {
                labels: compData.map(d => d.component_name),
                datasets: [{
                    label: 'Skor',
                    data: compData.map(d => d.score),
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22, 163, 74, 0.2)',
                    pointBackgroundColor: '#16a34a',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    @endif
</script>
@endpush
@endsection
