<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\HeroSection;
use Illuminate\Database\Seeder;

class HeroSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Welcome page hero sections
        HeroSection::create([
            'title' => 'Welcome Introduction',
            'content' => "# Welcome to San Isidro College\n\n<Center>\nEmpowering minds, cultivating values, and inspiring service since 1949.\n</Center>",
            'order' => 1,
            'is_active' => true,
            'department_id' => null,
        ]);

        HeroSection::create([
            'title' => 'Academic & Campus Life Blocks',
            'content' => "<Flex>\n<Card title=\"Academic Programs\">\nExplore our comprehensive collegiate courses and find the perfect path for your future career.\n</Card>\n<Card title=\"Campus Life\">\nJoin student organizations, varsity sports, and artistic groups to build lifelong connections.\n</Card>\n</Flex>\n\n<Accordion title=\"Learn More About SIC\">\nSan Isidro College is a Catholic diocesan educational institution located in Malaybalay City, Bukidnon, Philippines. We are committed to developing students with strong values, academic excellence, and a passion for community service.\n</Accordion>\n\n<Badges>Values, Excellence, Service, Innovation</Badges>",
            'order' => 2,
            'is_active' => true,
            'department_id' => null,
        ]);

        // 2. Department specific hero sections
        $cit = Department::where('slug', 'cit')->first();
        if ($cit) {
            HeroSection::create([
                'title' => 'CIT Tech Spotlight',
                'content' => "# Lead the Digital Frontier\n\n<Center>\nThe College of Information Technology at San Isidro College offers state-of-the-art specialization in **Software Engineering**, **Network Administration**, and **Data Analytics**.\n</Center>\n\n<Flex>\n<Card title=\"Modern Laboratories\">\nEquipped with high-spec developer environments and active publication in AI.\n</Card>\n<Card title=\"Industry Integration\">\nInternships with top-tier technology firms and coding hackathons.\n</Card>\n</Flex>",
                'order' => 1,
                'is_active' => true,
                'department_id' => $cit->id,
            ]);
        }

        $cba = Department::where('slug', 'cba')->first();
        if ($cba) {
            HeroSection::create([
                'title' => 'CBA Commerce Highlight',
                'content' => "# Shape the Future of Commerce\n\n<Center>\nDeveloping ethical business leaders, entrepreneurs, and financial managers.\n</Center>\n\n<Flex>\n<Card title=\"Business Incubator\">\nPitch, prototype, and launch your own startup with mentor support.\n</Card>\n<Card title=\"Case-Study Method\">\nLearn through real-world corporate challenges and strategic marketing.\n</Card>\n</Flex>",
                'order' => 1,
                'is_active' => true,
                'department_id' => $cba->id,
            ]);
        }
    }
}
