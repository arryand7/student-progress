<?php

namespace Database\Seeders;

use App\Models\Component;
use App\Models\Enrollment;
use App\Models\Evaluation;
use App\Models\EvaluationDetail;
use App\Models\Program;
use App\Models\Role;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedSubjectsAndComponents();
        $this->seedPembinaAssignments();
        $this->seedStudentsWithEvaluations();
    }

    private function seedSubjectsAndComponents(): void
    {
        $programs = Program::all()->keyBy('code');

        $subjectMap = [
            'OSN' => [
                'Matematika',
                'Fisika',
                'Kimia',
            ],
            'IGCSE' => [
                'Mathematics',
                'Science',
                'English',
            ],
            'KUS' => [
                'Biologi',
                'Matematika Lanjut',
                'Fisika Lanjut',
            ],
        ];

        foreach ($subjectMap as $programCode => $subjects) {
            $program = $programs->get($programCode);
            if (!$program) {
                continue;
            }

            foreach ($subjects as $name) {
                $code = Str::upper(Str::slug($name, ''));
                $subject = Subject::firstOrCreate(
                    ['program_id' => $program->id, 'code' => $code],
                    [
                        'name' => $name,
                        'description' => "Mata pelajaran {$name} untuk program {$program->name}",
                        'is_active' => true,
                    ]
                );

                $this->seedComponentsForSubject($subject);
            }
        }
    }

    private function seedComponentsForSubject(Subject $subject): void
    {
        $components = [
            ['name' => 'Skor Ujian', 'weight' => 40],
            ['name' => 'Kecepatan', 'weight' => 30],
            ['name' => 'Akurasi', 'weight' => 30],
        ];

        foreach ($components as $index => $component) {
            Component::firstOrCreate(
                [
                    'subject_id' => $subject->id,
                    'name' => $component['name'],
                ],
                [
                    'description' => "Komponen {$component['name']}",
                    'weight' => $component['weight'],
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedPembinaAssignments(): void
    {
        $pembinaRole = Role::where('name', 'pembina')->first();
        if (!$pembinaRole) {
            return;
        }

        $pembina = User::whereHas('roles', fn($q) => $q->where('name', 'pembina'))->first();
        if (!$pembina) {
            $pembina = User::create([
                'name' => 'Pembina Demo',
                'email' => 'pembina.demo@sabira.sch.id',
                'password' => 'password',
                'is_active' => true,
            ]);
            $pembina->roles()->sync([$pembinaRole->id]);
        }

        $subjectIds = Subject::pluck('id')->toArray();
        if (!empty($subjectIds)) {
            $pembina->pembinaSubjects()->syncWithoutDetaching($subjectIds);
        }
    }

    private function seedStudentsWithEvaluations(): void
    {
        $studentRole = Role::where('name', 'student')->first();
        $pembina = User::whereHas('roles', fn($q) => $q->where('name', 'pembina'))->first();

        if (!$studentRole || !$pembina) {
            return;
        }

        $subjects = Subject::with('components')->get();
        if ($subjects->isEmpty()) {
            return;
        }

        $targetStudents = 20;
        $existingStudents = User::whereHas('roles', fn($q) => $q->where('name', 'student'))->count();

        for ($i = $existingStudents + 1; $i <= $targetStudents; $i++) {
            $user = User::create([
                'name' => "Siswa Demo {$i}",
                'email' => "siswa{$i}@sabira.sch.id",
                'password' => 'password',
                'is_active' => true,
            ]);
            $user->roles()->sync([$studentRole->id]);
        }

        $students = User::whereHas('roles', fn($q) => $q->where('name', 'student'))->get();
        $weeksToSeed = 16;

        foreach ($students as $student) {
            $assignedSubjects = $subjects->count() >= 2
                ? $subjects->random(2)
                : $subjects;

            foreach ($assignedSubjects as $subject) {
                $enrollment = Enrollment::firstOrCreate(
                    [
                        'user_id' => $student->id,
                        'subject_id' => $subject->id,
                    ],
                    [
                        'program_id' => $subject->program_id,
                        'status' => 'active',
                        'enrolled_at' => now()->subDays(rand(10, 120)),
                    ]
                );

                for ($i = 0; $i < $weeksToSeed; $i++) {
                    $date = now()->startOfWeek()->subWeeks($i);
                    $weekNumber = $date->weekOfYear;
                    $year = $date->year;

                    $exists = Evaluation::where('enrollment_id', $enrollment->id)
                        ->where('week_number', $weekNumber)
                        ->where('year', $year)
                        ->exists();
                    if ($exists) {
                        continue;
                    }

                    $evaluation = Evaluation::create([
                        'enrollment_id' => $enrollment->id,
                        'evaluator_id' => $pembina->id,
                        'week_number' => $weekNumber,
                        'year' => $year,
                        'evaluation_date' => $date->toDateString(),
                        'notes' => 'Evaluasi demo otomatis.',
                        'is_locked' => false,
                    ]);

                    foreach ($subject->components as $component) {
                        $totalQuestions = rand(30, 60);
                        $attempted = rand(20, $totalQuestions);
                        $correct = rand(10, $attempted);
                        $score = rand(55, 95);

                        EvaluationDetail::create([
                            'evaluation_id' => $evaluation->id,
                            'component_id' => $component->id,
                            'score' => $score,
                            'time_spent_minutes' => rand(30, 120),
                            'total_questions' => $totalQuestions,
                            'attempted_questions' => $attempted,
                            'correct_questions' => $correct,
                            'notes' => 'Catatan demo.',
                        ]);
                    }

                    $evaluation->calculateTotalScore();

                    if (!$evaluation->evaluation_date->isSameDay(now())) {
                        $evaluation->lock($pembina);
                    }
                }
            }
        }
    }
}
