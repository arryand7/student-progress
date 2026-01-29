@extends('layouts.app')

@section('title', 'Daftar Evaluasi')
@section('page-title', 'Evaluasi Mingguan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Daftar Evaluasi</h2>
            <p class="text-sm text-gray-500">Lihat dan kelola evaluasi mingguan siswa</p>
        </div>
        <a href="{{ route('pembina.evaluations.select-student') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
            <span class="material-symbols-outlined mr-2">add</span>
            Input Evaluasi
        </a>
    </div>

    <!-- Pending Alert -->
    @if(count($pending) > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
            <div class="flex items-center">
                <span class="material-symbols-outlined text-amber-600 mr-3">warning</span>
                <div>
                    <p class="font-medium text-amber-800">{{ count($pending) }} siswa belum dievaluasi</p>
                    <a href="{{ route('pembina.evaluations.select-student') }}" class="text-sm text-amber-700 hover:text-amber-800 underline">
                        Lihat daftar →
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap items-center gap-4">
            <select name="subject_id" class="px-4 py-2 border border-gray-200 rounded-lg">
                <option value="">Semua Mapel</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                        {{ $subject->name }}
                    </option>
                @endforeach
            </select>
            <select name="year" class="px-4 py-2 border border-gray-200 rounded-lg">
                @for($y = now()->year; $y >= now()->year - 2; $y--)
                    <option value="{{ $y }}" {{ request('year', $year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <select name="week" class="px-4 py-2 border border-gray-200 rounded-lg">
                <option value="">Semua Minggu</option>
                @for($w = 1; $w <= 53; $w++)
                    <option value="{{ $w }}" {{ request('week') == $w ? 'selected' : '' }}>Week {{ $w }}</option>
                @endfor
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                Filter
            </button>
        </form>
    </div>

    <!-- Evaluations Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-sm sm:text-base">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Siswa</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Mata Pelajaran</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Minggu</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Skor</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($evaluations as $evaluation)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-primary-700 font-semibold text-xs">{{ substr($evaluation->enrollment->user->name, 0, 2) }}</span>
                                    </div>
                                    <span class="font-medium text-gray-800">{{ $evaluation->enrollment->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $evaluation->enrollment->subject->name }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-gray-700">Week {{ $evaluation->week_number }}, {{ $evaluation->year }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold {{ ($evaluation->total_score ?? 0) >= 80 ? 'text-green-600' : (($evaluation->total_score ?? 0) >= 60 ? 'text-amber-600' : 'text-red-600') }}">
                                    {{ $evaluation->total_score ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($evaluation->is_locked)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600">
                                        <span class="material-symbols-outlined text-xs mr-1">lock</span> Locked
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-green-100 text-green-700">
                                        <span class="material-symbols-outlined text-xs mr-1">edit</span> Editable
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('pembina.evaluations.show', $evaluation) }}" class="p-2 text-gray-400 hover:text-gray-600">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </a>
                                    @if(!$evaluation->is_locked)
                                        <a href="{{ route('pembina.evaluations.edit', $evaluation) }}" class="p-2 text-gray-400 hover:text-blue-600">
                                            <span class="material-symbols-outlined">edit</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <span class="material-symbols-outlined text-5xl text-gray-300 mb-3">assignment</span>
                                <p>Belum ada evaluasi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($evaluations->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $evaluations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
