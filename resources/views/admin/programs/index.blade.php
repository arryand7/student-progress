@extends('layouts.app')

@section('title', 'Program')
@section('page-title', 'Manajemen Program')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Daftar Program</h2>
            <p class="text-sm text-gray-500">Kelola program akademik kelas unggulan</p>
        </div>
        <a href="{{ route('admin.programs.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
            <span class="material-symbols-outlined mr-2">add</span>
            Tambah Program
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-sm sm:text-base">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Program</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Siswa</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($programs as $program)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $program->name }}</div>
                                @if($program->description)
                                    <div class="text-sm text-gray-500 truncate max-w-xs">{{ $program->description }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm font-mono">{{ $program->code }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-gray-700">{{ $program->subjects_count }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-gray-700">{{ $program->enrollments_count }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($program->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.programs.show', $program) }}" class="p-2 text-gray-400 hover:text-gray-600" title="Lihat">
                                        <span class="material-symbols-outlined text-xl">visibility</span>
                                    </a>
                                    <a href="{{ route('admin.programs.edit', $program) }}" class="p-2 text-gray-400 hover:text-blue-600" title="Edit">
                                        <span class="material-symbols-outlined text-xl">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.programs.toggle-status', $program) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 text-gray-400 hover:text-amber-600" title="{{ $program->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <span class="material-symbols-outlined text-xl">{{ $program->is_active ? 'toggle_on' : 'toggle_off' }}</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <span class="material-symbols-outlined text-5xl text-gray-300 mb-3">folder_off</span>
                                <p>Belum ada program. <a href="{{ route('admin.programs.create') }}" class="text-primary-600 hover:underline">Tambah program baru</a></p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($programs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $programs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
