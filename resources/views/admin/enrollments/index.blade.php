@extends('layouts.app')

@section('title', 'Pendaftaran')
@section('page-title', 'Manajemen Pendaftaran')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Pendaftaran Siswa</h2>
            <p class="text-sm text-gray-500">Kelola pendaftaran siswa ke mata pelajaran</p>
        </div>
        <a href="{{ route('admin.enrollments.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
            <span class="material-symbols-outlined mr-2">person_add</span>
            Daftarkan Siswa
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('admin.enrollments.index') }}" class="flex flex-wrap items-center gap-4">
            <div class="min-w-[200px]">
                <label class="block text-xs text-gray-500 mb-1">Program</label>
                <select name="program_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                    <option value="">Semua Program</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                            {{ $program->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[200px]">
                <label class="block text-xs text-gray-500 mb-1">Mata Pelajaran</label>
                <select name="subject_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                    <option value="">Semua Mapel</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs text-gray-500 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                    <option value="">Semua</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-sm sm:text-base">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Siswa</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Program</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Mata Pelajaran</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($enrollments as $enrollment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $enrollment->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $enrollment->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-700">{{ $enrollment->program->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $enrollment->subject->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($enrollment->isActive())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600">{{ $enrollment->enrolled_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.enrollments.show', $enrollment) }}" class="p-2 text-gray-400 hover:text-gray-600">
                                        <span class="material-symbols-outlined text-xl">visibility</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.enrollments.toggle-status', $enrollment) }}">
                                        @csrf
                                        <button type="submit" class="p-2 text-gray-400 hover:text-amber-600">
                                            <span class="material-symbols-outlined text-xl">{{ $enrollment->isActive() ? 'toggle_on' : 'toggle_off' }}</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada pendaftaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($enrollments->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $enrollments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
