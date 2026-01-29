@extends('layouts.app')

@section('title', 'Komponen')
@section('page-title', 'Komponen - ' . $subject->name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Komponen {{ $subject->name }}</h2>
            <p class="text-sm text-gray-500">Total bobot aktif: {{ $totalWeight }}%</p>
        </div>
        <a href="{{ route('admin.subjects.components.create', $subject) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
            <span class="material-symbols-outlined mr-2">add</span>
            Tambah Komponen
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-sm sm:text-base">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Komponen</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Bobot</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($components as $component)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $component->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $component->description ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">{{ $component->weight }}%</td>
                            <td class="px-6 py-4 text-center">
                                @if($component->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.subjects.components.edit', [$subject, $component]) }}" class="p-2 text-gray-400 hover:text-blue-600">
                                        <span class="material-symbols-outlined text-xl">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.subjects.components.toggle-status', [$subject, $component]) }}">
                                        @csrf
                                        <button type="submit" class="p-2 text-gray-400 hover:text-amber-600">
                                            <span class="material-symbols-outlined text-xl">{{ $component->is_active ? 'toggle_on' : 'toggle_off' }}</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada komponen.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <a href="{{ route('admin.subjects.show', $subject) }}" class="inline-flex items-center text-gray-600 hover:text-gray-800">
            <span class="material-symbols-outlined mr-1">arrow_back</span>
            Kembali ke Detail Mata Pelajaran
        </a>
    </div>
</div>
@endsection
