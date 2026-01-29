@extends('layouts.app')

@section('title', 'Pilih Siswa')
@section('page-title', 'Input Evaluasi Mingguan')

@section('content')
<div class="space-y-6">
    <!-- Week Selection -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Pilih Minggu Evaluasi</h3>
        <div class="flex flex-wrap items-center gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Tahun</label>
                <select id="year-select" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                    @for($y = now()->year; $y >= now()->year - 1; $y--)
                        <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Minggu</label>
                <select id="week-select" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                    @for($w = 1; $w <= 53; $w++)
                        <option value="{{ $w }}" {{ $w == $currentWeek ? 'selected' : '' }}>Week {{ $w }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    <!-- Subjects and Students -->
    @foreach($subjects as $subject)
        @if($subject->enrollments->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">{{ $subject->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $subject->program->name ?? '' }}</p>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($subject->enrollments as $enrollment)
                        <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center mr-4">
                                    <span class="text-primary-700 font-semibold text-sm">{{ substr($enrollment->user->name, 0, 2) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $enrollment->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $enrollment->user->email }}</p>
                                </div>
                            </div>
                            <a 
                                href="{{ route('pembina.evaluations.create') }}?enrollment_id={{ $enrollment->id }}&week={{ $currentWeek }}&year={{ $currentYear }}"
                                class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700 transition-colors evaluation-link"
                                data-enrollment="{{ $enrollment->id }}"
                            >
                                <span class="material-symbols-outlined text-sm mr-1">add</span>
                                Input Evaluasi
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach

    @if($subjects->every(fn($s) => $s->enrollments->count() == 0))
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <span class="material-symbols-outlined text-5xl text-gray-300 mb-3">person_off</span>
            <p class="text-gray-500">Tidak ada siswa yang terdaftar dalam mata pelajaran apapun.</p>
            <a href="{{ route('admin.enrollments.create') }}" class="mt-4 inline-flex items-center text-primary-600 hover:text-primary-700">
                Daftarkan siswa <span class="material-symbols-outlined text-sm ml-1">arrow_forward</span>
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const yearSelect = document.getElementById('year-select');
        const weekSelect = document.getElementById('week-select');
        const links = document.querySelectorAll('.evaluation-link');

        function updateLinks() {
            const year = yearSelect.value;
            const week = weekSelect.value;
            
            links.forEach(link => {
                const enrollmentId = link.dataset.enrollment;
                link.href = `{{ route('pembina.evaluations.create') }}?enrollment_id=${enrollmentId}&week=${week}&year=${year}`;
            });
        }

        yearSelect.addEventListener('change', updateLinks);
        weekSelect.addEventListener('change', updateLinks);
    });
</script>
@endpush
@endsection
