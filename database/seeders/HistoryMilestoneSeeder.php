<?php

namespace Database\Seeders;

use App\Models\HistoryMilestone;
use Illuminate\Database\Seeder;

class HistoryMilestoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $milestones = [
            [
                'year' => '1965',
                'title' => 'The Founding',
                'description' => 'San Isidro College was established by a group of visionary educators and civic leaders who believed in making quality education accessible to the local community. The college opened its doors with just 150 students and a handful of dedicated faculty.',
                'icon' => 'landmark',
                'sort_order' => 1,
            ],
            [
                'year' => '1972',
                'title' => 'First Graduates',
                'description' => "The college celebrated its first batch of graduates, producing 45 alumni who went on to become pillars of their communities. This milestone cemented the institution's commitment to academic excellence.",
                'icon' => 'graduation-cap',
                'sort_order' => 2,
            ],
            [
                'year' => '1980',
                'title' => 'Campus Expansion',
                'description' => "A new science building and library were inaugurated, significantly expanding the college's capacity. Enrollment surpassed 1,000 students for the first time, marking a new era of growth.",
                'icon' => 'school',
                'sort_order' => 3,
            ],
            [
                'year' => '1990',
                'title' => 'CHED Recognition',
                'description' => 'San Isidro College received full recognition from the Commission on Higher Education (CHED), validating the quality of its academic programs and institutional governance.',
                'icon' => 'medal',
                'sort_order' => 4,
            ],
            [
                'year' => '1998',
                'title' => 'Graduate Programs Launched',
                'description' => 'The college expanded its offerings with the launch of graduate programs in Education and Business Administration, responding to the growing demand for advanced studies in the region.',
                'icon' => 'book-open',
                'sort_order' => 5,
            ],
            [
                'year' => '2005',
                'title' => 'Center of Excellence Award',
                'description' => 'The College of Education was designated as a Center of Excellence by CHED, recognizing its outstanding teacher education programs and contributions to the field.',
                'icon' => 'trophy',
                'sort_order' => 6,
            ],
            [
                'year' => '2012',
                'title' => 'Technology & Innovation Hub',
                'description' => 'A state-of-the-art IT center and innovation hub was established, integrating modern technology into the curriculum and providing students with hands-on experience in emerging fields.',
                'icon' => 'lightbulb',
                'sort_order' => 7,
            ],
            [
                'year' => '2018',
                'title' => 'International Partnerships',
                'description' => 'San Isidro College forged academic partnerships with universities across Southeast Asia, enabling student exchange programs, collaborative research, and cross-cultural learning opportunities.',
                'icon' => 'handshake',
                'sort_order' => 8,
            ],
            [
                'year' => '2023',
                'title' => 'Diamond Jubilee',
                'description' => 'The college celebrated its 58th anniversary with a grand homecoming event, reuniting thousands of alumni. A new strategic plan was unveiled, charting the path toward becoming a leading institution in the region.',
                'icon' => 'sparkles',
                'sort_order' => 9,
            ],
            [
                'year' => 'Present',
                'title' => 'Looking Forward',
                'description' => 'Today, San Isidro College continues to evolve — embracing digital transformation, expanding research initiatives, and deepening community engagement while staying true to its founding values of faith, service, and academic excellence.',
                'icon' => 'star',
                'sort_order' => 10,
            ],
        ];

        foreach ($milestones as $milestone) {
            HistoryMilestone::create($milestone);
        }
    }
}
