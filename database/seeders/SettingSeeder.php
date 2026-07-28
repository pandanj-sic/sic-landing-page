<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::setByKey('welcome_hero_content', "# Welcome to San Isidro College\n\n<Center>\nEmpowering minds, cultivating values, and inspiring service since 1949.\n</Center>\n\n<Flex>\n<Card title=\"Academic Programs\">\nExplore our comprehensive collegiate courses and find the perfect path for your future career.\n</Card>\n<Card title=\"Campus Life\">\nJoin student organizations, varsity sports, and artistic groups to build lifelong connections.\n</Card>\n</Flex>\n\n<Accordion title=\"Learn More About SIC\">\nSan Isidro College is a Catholic diocesan educational institution located in Malaybalay City, Bukidnon, Philippines. We are committed to developing students with strong values, academic excellence, and a passion for community service.\n</Accordion>\n\n<Badges>Values, Excellence, Service, Innovation</Badges>");
    }
}
