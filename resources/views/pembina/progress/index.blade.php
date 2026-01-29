@extends('layouts.app')

@section('title', 'Progress Siswa')
@section('page-title', 'Progress Siswa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Progress Siswa</h2>
            <p class="text-sm text-gray-500">Lihat dan analisis perkembangan siswa</p>
        </div>
        <select id="year-filter" class="px-4 py-2 border border-gray-200 rounded-lg">
            @for($y = now()->year; $y >= now()->year - 2; $y--)
                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </div>

    <!-- Subject Cards -->
    @foreach($subjects as $subject)
        @if($subject->enrollments->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-primary-50 to-white border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">{{ $subject->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $subject->program->name ?? '' }} • {{ $subject->enrollments->count() }} siswa</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($subject->enrollments as $enrollment)
                            <a href="{{ route('pembina.progress.student', $enrollment) }}?year={{ $year }}" 
                               class="block p-4 border border-gray-100 rounded-xl hover:border-primary-300 hover:shadow transition-all">
                                <div class="flex items-center mb-3">
                                    <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-primary-700 font-semibold text-sm">{{ substr($enrollment->user->name, 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $enrollment->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $enrollment->user->email }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Lihat progress</span>
                                    <span class="material-symbols-outlined text-primary-600">arrow_forward</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <a href="{{ route('pembina.progress.subject', $subject) }}?year={{ $year }}" class="inline-flex items-center text-primary-600 hover:text-primary-700 text-sm font-medium">
                            <span class="material-symbols-outlined text-sm mr-1">compare_arrows</span>
                            Bandingkan semua siswa
                        </a>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    @if($subjects->every(fn($s) => $s->enrollments->count() == 0))
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <span class="material-symbols-outlined text-5xl text-gray-300 mb-3">person_off</span>
            <p class="text-gray-500">Tidak ada siswa yang terdaftar.</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.getElementById('year-filter').addEventListener('change', function() {
        window.location.href = '{{ route('pembina.progress.index') }}?year=' + this.value;
    });
</script>
@endpush
@endsection
