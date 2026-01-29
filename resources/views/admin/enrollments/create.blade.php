@extends('layouts.app')

@section('title', 'Daftarkan Siswa')
@section('page-title', 'Daftarkan Siswa')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.enrollments.store') }}">
        @csrf

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Siswa</label>
                <select name="user_id" class="w-full px-4 py-2 border border-gray-200 rounded-lg" required>
                    <option value="">Pilih Siswa</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Program</label>
                <select name="program_id" id="program-select" class="w-full px-4 py-2 border border-gray-200 rounded-lg" required>
                    <option value="">Pilih Program</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}">{{ $program->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                <select name="subject_id" id="subject-select" class="w-full px-4 py-2 border border-gray-200 rounded-lg" required>
                    <option value="">Pilih Mata Pelajaran</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" data-program-id="{{ $subject->program_id }}">
                            {{ $subject->program->name ?? 'Program' }} - {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
                @if($subjects->isEmpty())
                    <p class="text-xs text-gray-500 mt-1">Tidak ada mata pelajaran dalam penugasan Anda.</p>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Daftar</label>
                <input type="date" name="enrolled_at" value="{{ old('enrolled_at', now()->toDateString()) }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg" required>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-4">
            <a href="{{ route('admin.enrollments.index') }}" class="text-gray-600 hover:text-gray-800">Batal</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Simpan</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const programSelect = document.getElementById('program-select');
    const subjectSelect = document.getElementById('subject-select');
    const subjectOptions = Array.from(subjectSelect.options);

    function filterSubjects() {
        const programId = programSelect.value;

        subjectOptions.forEach(option => {
            if (!option.value) return;
            const optionProgramId = option.dataset.programId;
            const show = !programId || optionProgramId === programId;
            option.hidden = !show;
        });

        if (subjectSelect.selectedOptions[0]?.hidden) {
            subjectSelect.value = '';
        }
    }

    programSelect.addEventListener('change', filterSubjects);
    filterSubjects();
</script>
@endpush
@endsection
