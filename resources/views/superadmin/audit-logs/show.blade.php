@extends('layouts.app')

@section('title', 'Detail Audit Log')
@section('page-title', 'Detail Audit Log')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $auditLog->action_description }}</h2>
                <p class="text-gray-500">{{ $auditLog->created_at->format('d M Y H:i:s') }}</p>
            </div>
            @php
                $actionColors = [
                    'created' => 'bg-green-100 text-green-700',
                    'updated' => 'bg-blue-100 text-blue-700',
                    'deleted' => 'bg-red-100 text-red-700',
                    'locked' => 'bg-gray-100 text-gray-700',
                    'unlocked' => 'bg-amber-100 text-amber-700',
                ];
                $color = $actionColors[$auditLog->action] ?? 'bg-gray-100 text-gray-700';
            @endphp
            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $color }}">
                {{ $auditLog->action }}
            </span>
        </div>
    </div>

    <!-- Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Informasi</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">User</span>
                    <span class="font-medium text-gray-800">{{ $auditLog->user?->name ?? 'System' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Model</span>
                    <span class="font-medium text-gray-800">{{ class_basename($auditLog->auditable_type) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Model ID</span>
                    <span class="font-medium text-gray-800">{{ $auditLog->auditable_id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">IP Address</span>
                    <span class="font-medium text-gray-800">{{ $auditLog->ip_address ?? '-' }}</span>
                </div>
            </div>
        </div>

        @if($auditLog->reason)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Alasan</h3>
                <p class="text-gray-700">{{ $auditLog->reason }}</p>
            </div>
        @endif
    </div>

    <!-- Old Values -->
    @if($auditLog->old_values)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Nilai Lama</h3>
            <pre class="bg-gray-50 p-4 rounded-lg overflow-x-auto text-sm text-gray-700">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT) }}</pre>
        </div>
    @endif

    <!-- New Values -->
    @if($auditLog->new_values)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Nilai Baru</h3>
            <pre class="bg-gray-50 p-4 rounded-lg overflow-x-auto text-sm text-gray-700">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT) }}</pre>
        </div>
    @endif

    <!-- User Agent -->
    @if($auditLog->user_agent)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">User Agent</h3>
            <p class="text-sm text-gray-600 break-all">{{ $auditLog->user_agent }}</p>
        </div>
    @endif

    <!-- Back -->
    <div>
        <a href="{{ route('superadmin.audit-logs.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-800">
            <span class="material-symbols-outlined mr-1">arrow_back</span>
            Kembali
        </a>
    </div>
</div>
@endsection
