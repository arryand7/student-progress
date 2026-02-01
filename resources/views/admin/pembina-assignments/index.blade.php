@extends('layouts.app')

@section('title', 'Penugasan Pembina')
@section('page-title', 'Penugasan Pembina')

@section('content')
@php
    $user = auth()->user();
    $canManageAssignments = $user->hasPermission('subject.edit');
@endphp
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-bold text-gray-800">Penugasan Pembina ke Mata Pelajaran</h2>
        <p class="text-sm text-gray-500">Atur pembina yang bertanggung jawab untuk setiap mata pelajaran.</p>
        @if(!$canManageAssignments)
            <p class="text-xs text-gray-500 mt-2">Akses Anda hanya untuk melihat penugasan.</p>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-sm sm:text-base">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Mata Pelajaran</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Program</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Pembina</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">{{ $canManageAssignments ? 'Aksi' : 'Jumlah' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($subjects as $subject)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $subject->name }}</div>
                                <div class="text-xs text-gray-500">{{ $subject->code }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $subject->program->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($canManageAssignments)
                                    <form method="POST" action="{{ route('admin.pembina-assignments.update', $subject) }}" class="flex items-center gap-4">
                                        @csrf
                                        @method('PUT')
                                        <select name="pembina_ids[]" multiple class="w-full min-w-[260px] px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                                            @foreach($pembinas as $pembina)
                                                <option value="{{ $pembina->id }}" {{ $subject->pembinas->contains($pembina->id) ? 'selected' : '' }}>
                                                    {{ $pembina->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm">
                                            Simpan
                                        </button>
                                    </form>
                                @else
                                    @if($subject->pembinas->count() > 0)
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($subject->pembinas as $pembina)
                                                <span class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-medium">
                                                    {{ $pembina->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">Belum ada pembina</span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-gray-500 text-sm">
                                {{ $subject->pembinas->count() }} pembina
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
