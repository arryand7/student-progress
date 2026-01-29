@extends('layouts.app')

@section('title', 'Input Evaluasi')
@section('page-title', 'Input Evaluasi Mingguan')

@section('content')
<form method="POST" action="{{ route('pembina.evaluations.store') }}">
    @csrf
    <input type="hidden" name="enrollment_id" value="{{ $enrollment->id }}">
    <input type="hidden" name="week_number" value="{{ $week }}">
    <input type="hidden" name="year" value="{{ $year }}">

    <div class="space-y-6">
        <!-- Student Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-14 h-14 bg-primary-100 rounded-xl flex items-center justify-center mr-4">
                        <span class="text-primary-700 font-bold text-lg">{{ substr($enrollment->user->name, 0, 2) }}</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ $enrollment->user->name }}</h2>
                        <p class="text-gray-500">{{ $enrollment->subject->name }} - {{ $enrollment->program->name }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-primary-600">Week {{ $week }}</p>
                    <p class="text-gray-500">{{ $year }}</p>
                </div>
            </div>
        </div>

        <!-- Components -->
        @foreach($components as $index => $component)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-primary-50 to-white border-b border-gray-100">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold text-gray-800">{{ $component->name }}</h3>
                        <span class="px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-sm">
                            Bobot: {{ $component->weight }}%
                        </span>
                    </div>
                    @if($component->description)
                        <p class="text-sm text-gray-500 mt-1">{{ $component->description }}</p>
                    @endif
                </div>
                
                <div class="p-6">
                    <input type="hidden" name="components[{{ $index }}][component_id]" value="{{ $component->id }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Score -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Skor <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="number" 
                                    name="components[{{ $index }}][score]"
                                    min="0" 
                                    max="100" 
                                    step="0.01"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500"
                                    placeholder="0-100"
                                >
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">/100</span>
                            </div>
                        </div>

                        <!-- Time Spent -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Waktu (menit)</label>
                            <input 
                                type="number" 
                                name="components[{{ $index }}][time_spent_minutes]"
                                min="0"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500"
                                placeholder="Waktu pengerjaan"
                            >
                        </div>

                        <!-- Total Questions -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Soal</label>
                            <input 
                                type="number" 
                                name="components[{{ $index }}][total_questions]"
                                min="0"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500"
                                placeholder="Jumlah soal"
                            >
                        </div>

                        <!-- Attempted Questions -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dikerjakan</label>
                            <input 
                                type="number" 
                                name="components[{{ $index }}][attempted_questions]"
                                min="0"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500"
                                placeholder="Soal dikerjakan"
                            >
                        </div>

                        <!-- Correct Questions -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Benar</label>
                            <input 
                                type="number" 
                                name="components[{{ $index }}][correct_questions]"
                                min="0"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500"
                                placeholder="Jawaban benar"
                            >
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                            <input 
                                type="text" 
                                name="components[{{ $index }}][notes]"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500"
                                placeholder="Catatan komponen"
                            >
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- General Notes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Umum</label>
            <textarea 
                name="notes"
                rows="3"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500"
                placeholder="Catatan evaluasi keseluruhan untuk minggu ini"
            ></textarea>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('pembina.evaluations.select-student') }}" class="px-6 py-3 text-gray-700 hover:text-gray-900">
                Batal
            </a>
            <button type="submit" class="px-8 py-3 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition-colors font-medium">
                <span class="material-symbols-outlined align-middle mr-1">save</span>
                Simpan Evaluasi
            </button>
        </div>
    </div>
</form>
@endsection
