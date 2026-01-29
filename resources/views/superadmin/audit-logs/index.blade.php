@extends('layouts.app')

@section('title', 'Audit Log')
@section('page-title', 'Audit Log')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Audit Log</h2>
            <p class="text-sm text-gray-500">Riwayat semua perubahan dalam sistem</p>
        </div>
        <a href="{{ route('superadmin.audit-logs.export') }}?{{ http_build_query(request()->all()) }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50">
            <span class="material-symbols-outlined mr-2">download</span>
            Export CSV
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Action</label>
                <select name="action" class="px-4 py-2 border border-gray-200 rounded-lg">
                    <option value="">Semua Action</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ $action }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-4 py-2 border border-gray-200 rounded-lg">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-4 py-2 border border-gray-200 rounded-lg">
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                Filter
            </button>
            <a href="{{ route('superadmin.audit-logs.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700">
                Reset
            </a>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-sm sm:text-base">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">User</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Action</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Model</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">IP Address</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $log->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-800">{{ $log->user?->name ?? 'System' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $actionColors = [
                                        'created' => 'bg-green-100 text-green-700',
                                        'updated' => 'bg-blue-100 text-blue-700',
                                        'deleted' => 'bg-red-100 text-red-700',
                                        'locked' => 'bg-gray-100 text-gray-700',
                                        'unlocked' => 'bg-amber-100 text-amber-700',
                                    ];
                                    $color = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $color }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $log->ip_address }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('superadmin.audit-logs.show', $log) }}" class="text-primary-600 hover:text-primary-700">
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <span class="material-symbols-outlined text-5xl text-gray-300 mb-3">history</span>
                                <p>Tidak ada log yang ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
