@extends('layouts.app')

@section('title', 'Manajemen Siswa')
@section('page-title', 'Manajemen Siswa')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Siswa</h2>
            <p class="text-sm text-gray-500">Kelola data siswa dan status aktif</p>
        </div>
        <a href="{{ route('admin.students.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
            <span class="material-symbols-outlined mr-2">person_add</span>
            Tambah Siswa
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('admin.students.index') }}" class="flex items-center gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email"
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
            <button type="submit" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">Cari</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-sm sm:text-base">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($students as $student)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $student->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $student->email }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($student->is_active)
                                    <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">Aktif</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-600">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.students.edit', $student) }}" class="text-blue-600 hover:text-blue-700 text-sm">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada siswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($students->hasPages())
            <div class="p-4">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
