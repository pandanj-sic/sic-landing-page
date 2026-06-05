<?php

namespace Database\Seeders;

use App\Models\Scholarship;
use Illuminate\Database\Seeder;

class ScholarshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $scholarships = [
            [
                'name' => 'Academic Excellence Scholarship',
                'coverage' => '100% Tuition Fee Discount',
                'description' => 'Awarded to graduating high school valedictorians and salutatorians to support outstanding academic achievers in pursuing higher education.',
                'requirements' => "• High School Valedictorian or Salutatorian certification\n• Certificate of Good Moral Character\n• Maintain a GPA of 1.75 (or equivalent) each semester with no grade below 2.0",
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => "President's Scholarship",
                'coverage' => '100% Tuition & Miscellaneous Fees',
                'description' => 'A prestigious program for students who demonstrate exceptional leadership qualities, outstanding academic standing, and active participation in school and community activities.',
                'requirements' => "• Minimum average grade of 92%\n• Active leadership roles in previous school/organization\n• Recommendation letter from the previous School Principal",
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Athletic & Sports Scholarship',
                'coverage' => '50% to 100% Tuition Fee Discount',
                'description' => 'Designed to support talented athletes who represent San Isidro College in national, regional, or local sports competitions.',
                'requirements' => "• Pass the athletic tryouts and physical fitness assessments\n• Endorsement from the Sports Coordinator/Coach\n• Maintain passing grades in all enrolled subjects",
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Financial Aid & Grants',
                'coverage' => '50% Tuition Fee Discount',
                'description' => 'A need-based program dedicated to helping underprivileged yet deserving students achieve their academic and professional goals.',
                'requirements' => "• Combined parent/guardian annual income below the threshold\n• Certificate of Indigency from the local Barangay\n• Maintain a GPA of 2.50 or better with no failing grades",
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($scholarships as $scholarship) {
            Scholarship::create($scholarship);
        }
    }
}
