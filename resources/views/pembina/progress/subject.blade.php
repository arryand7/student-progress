@extends('layouts.app')

@section('title', 'Perbandingan Siswa')
@section('page-title', 'Perbandingan Siswa - ' . $subject->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $subject->name }}</h2>
                <p class="text-gray-500">{{ $subject->program->name ?? '' }}</p>
            </div>
            <div class="flex items-center gap-4">
                <p class="text-lg font-semibold text-gray-600">Tahun {{ $year }}</p>
                <div class="flex items-center gap-2">
                    <label for="timeframe-select" class="text-sm text-gray-500">Timeframe</label>
                    <select id="timeframe-select" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        <option value="4w" {{ ($timeframe ?? '12w') === '4w' ? 'selected' : '' }}>4 minggu</option>
                        <option value="8w" {{ ($timeframe ?? '12w') === '8w' ? 'selected' : '' }}>8 minggu</option>
                        <option value="12w" {{ ($timeframe ?? '12w') === '12w' ? 'selected' : '' }}>12 minggu</option>
                        <option value="1m" {{ ($timeframe ?? '12w') === '1m' ? 'selected' : '' }}>1 bulan</option>
                        <option value="3m" {{ ($timeframe ?? '12w') === '3m' ? 'selected' : '' }}>3 bulan</option>
                        <option value="6m" {{ ($timeframe ?? '12w') === '6m' ? 'selected' : '' }}>6 bulan</option>
                        <option value="12m" {{ ($timeframe ?? '12w') === '12m' ? 'selected' : '' }}>12 bulan</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparison Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Perbandingan Semua Siswa</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-sm sm:text-base">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Rank</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Siswa</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Rata-rata</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Skor Terakhir</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Evaluasi</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Trend</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($comparison as $index => $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                @if($index == 0)
                                    <span class="w-8 h-8 bg-amber-400 rounded-full flex items-center justify-center text-white font-bold text-sm">1</span>
                                @elseif($index == 1)
                                    <span class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-white font-bold text-sm">2</span>
                                @elseif($index == 2)
                                    <span class="w-8 h-8 bg-amber-600 rounded-full flex items-center justify-center text-white font-bold text-sm">3</span>
                                @else
                                    <span class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 font-medium text-sm">{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-800">{{ $student['student_name'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-lg font-bold {{ ($student['average_score'] ?? 0) >= 80 ? 'text-green-600' : (($student['average_score'] ?? 0) >= 60 ? 'text-amber-600' : 'text-red-600') }}">
                                    {{ $student['average_score'] ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-700">
                                {{ $student['latest_score'] ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center text-gray-500">
                                {{ $student['evaluation_count'] }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($student['trend'] == 'improving')
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-green-100 text-green-700 text-xs">
                                        <span class="material-symbols-outlined text-sm mr-1">trending_up</span>
                                        Naik
                                    </span>
                                @elseif($student['trend'] == 'declining')
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-red-100 text-red-700 text-xs">
                                        <span class="material-symbols-outlined text-sm mr-1">trending_down</span>
                                        Turun
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 text-gray-600 text-xs">
                                        <span class="material-symbols-outlined text-sm mr-1">trending_flat</span>
                                        Stabil
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Comparison Chart -->
    @if(count($comparison) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Grafik Perbandingan</h3>
            <div class="h-[520px] sm:h-[600px]">
                <canvas id="comparisonChart"></canvas>
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
    document.getElementById('timeframe-select').addEventListener('change', function () {
        const params = new URLSearchParams(window.location.search);
        params.set('timeframe', this.value);
        window.location.search = params.toString();
    });

    const comparison = @json($weeklyComparison);
    const ctx = document.getElementById('comparisonChart').getContext('2d');

    const palette = ['#2563eb', '#f97316', '#16a34a', '#dc2626', '#7c3aed', '#0ea5e9'];
    const weeks = (comparison.weeks || []).map(w => 'Week ' + w);
    const datasets = (comparison.series || []).map((item, idx) => {
        const color = palette[idx % palette.length];
        return {
            label: item.student_name,
            data: item.data,
            borderColor: color,
            backgroundColor: color + '33',
            fill: false,
            tension: 0.3,
            pointRadius: 4,
            pointBackgroundColor: color,
        };
    });

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: weeks,
            datasets,
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'nearest',
                intersect: true,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                },
                tooltip: {
                    enabled: true,
                    mode: 'nearest',
                    intersect: true,
                    callbacks: {
                        label: function (context) {
                            const value = context.parsed.y;
                            if (value === null || typeof value === 'undefined') {
                                return null;
                            }
                            const rounded = Math.round(value * 100) / 100;
                            return `${context.dataset.label}: ${rounded}`;
                        }
                    }
                },
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
@endsection
