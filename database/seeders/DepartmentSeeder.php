<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            'Bachelor of Arts',
            'Bachelor of Science in Business Administration',
            'Bachelor of Science in Office Administration',
            'Bachelor of Elementary Education',
            'Bachelor of Secondary Education',
            'Bachelor of Science in Civil Engineering',
            'Bachelor of Science in Accountancy',
            'Bachelor of Science in Information Technology',
            'Bachelor of Science in Midwifery',
            'Bachelor of Science in Nursing',
            'Pre-School',
            'Grade School',
            'Junior High',
            'Senior High',
        ];

        foreach ($departments as $name) {
            Department::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => "Official department details for {$name}.",
                    'content' => "<p>Welcome to the <strong>{$name}</strong> department page. Here you can find our latest news, announcements, events, and academic staff information.</p>",
                ]
            );
        }
    }
}
