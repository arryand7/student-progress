@extends('layouts.app')

@section('title', 'Mata Pelajaran')
@section('page-title', 'Manajemen Mata Pelajaran')

@section('content')
@php
    $user = auth()->user();
@endphp
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Daftar Mata Pelajaran</h2>
            <p class="text-sm text-gray-500">Kelola mata pelajaran per program</p>
        </div>
        @if($user->hasPermission('subject.create'))
            <a href="{{ route('admin.subjects.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                <span class="material-symbols-outlined mr-2">add</span>
                Tambah Mapel
            </a>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('admin.subjects.index') }}" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[220px]">
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
            <button type="submit" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-sm sm:text-base">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Program</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Komponen</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Siswa</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($subjects as $subject)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $subject->name }}</div>
                                @if($subject->description)
                                    <div class="text-sm text-gray-500 truncate max-w-xs">{{ $subject->description }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-700">{{ $subject->program->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm font-mono">{{ $subject->code }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">{{ $subject->components_count }}</td>
                            <td class="px-6 py-4 text-center">{{ $subject->enrollments_count }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($subject->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    @if($user->hasPermission('subject.edit'))
                                        <a href="{{ route('admin.subjects.show', $subject) }}" class="p-2 text-gray-400 hover:text-gray-600" title="Lihat">
                                            <span class="material-symbols-outlined text-xl">visibility</span>
                                        </a>
                                        <a href="{{ route('admin.subjects.edit', $subject) }}" class="p-2 text-gray-400 hover:text-blue-600" title="Edit">
                                            <span class="material-symbols-outlined text-xl">edit</span>
                                        </a>
                                    @endif
                                    @if(
                                        $user->hasPermission('component.create')
                                        || $user->hasPermission('component.edit')
                                        || $user->hasPermission('component.toggle_active')
                                        || $user->hasPermission('component.adjust_weight')
                                    )
                                        <a href="{{ route('admin.subjects.components.index', $subject) }}" class="p-2 text-gray-400 hover:text-amber-600" title="Komponen">
                                            <span class="material-symbols-outlined text-xl">settings</span>
                                        </a>
                                    @endif
                                    @if($user->hasPermission('subject.deactivate'))
                                        <form method="POST" action="{{ route('admin.subjects.toggle-status', $subject) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-gray-400 hover:text-amber-600" title="{{ $subject->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <span class="material-symbols-outlined text-xl">{{ $subject->is_active ? 'toggle_on' : 'toggle_off' }}</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <span class="material-symbols-outlined text-5xl text-gray-300 mb-3">menu_book</span>
                                <p>Belum ada mata pelajaran.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subjects->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $subjects->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
