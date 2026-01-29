<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = [
            [
                'name' => 'Olimpiade Sains Nasional',
                'code' => 'OSN',
                'description' => 'Program persiapan Olimpiade Sains Nasional untuk berbagai bidang sains.',
                'is_active' => true,
            ],
            [
                'name' => 'International General Certificate of Secondary Education',
                'code' => 'IGCSE',
                'description' => 'Program kurikulum internasional Cambridge IGCSE.',
                'is_active' => true,
            ],
            [
                'name' => 'Kelas Unggulan Sains',
                'code' => 'KUS',
                'description' => 'Kelas unggulan dengan fokus pada pengembangan kemampuan sains.',
                'is_active' => true,
            ],
        ];

        foreach ($programs as $program) {
            Program::updateOrCreate(
                ['code' => $program['code']],
                $program
            );
        }
    }
}
