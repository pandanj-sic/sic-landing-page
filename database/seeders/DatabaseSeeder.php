<?php

namespace Database\Seeders;

use App\Models\CarouselItem;
use App\Models\Department;
use App\Models\Download;
use App\Models\Member;
use App\Models\NavigationLink;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Navigation Links
        NavigationLink::create(['label' => 'Home', 'url' => '/', 'order' => 1]);
        NavigationLink::create(['label' => 'Departments', 'url' => '#departments', 'order' => 2]);

        $downloads = NavigationLink::create(['label' => 'Downloads', 'url' => '/downloads', 'order' => 3]);
        NavigationLink::create(['label' => 'Student Handbook', 'url' => '/downloads', 'order' => 1, 'parent_id' => $downloads->id]);
        NavigationLink::create(['label' => 'Academic Calendars', 'url' => '/downloads', 'order' => 2, 'parent_id' => $downloads->id]);

        NavigationLink::create(['label' => 'Contact Us', 'url' => '/contact', 'order' => 4]);

        // 2. Create Departments
        $depts = [
            [
                'name' => 'College of Information Technology',
                'slug' => 'cit',
                'description' => 'Empowering the next generation of technology leaders through innovative curricula, hands-on programming labs, and industry-partnered software engineering projects.',
                'hero_content' => "# Lead the Digital Frontier\n\nThe College of Information Technology at San Isidro College offers state-of-the-art specialization in **Software Engineering**, **Network Administration**, and **Data Analytics**. Our graduates are globally competitive, highly technical, and values-driven professionals.\n\n### Why Choose CIT?\n- **Modern Laboratories:** Equipped with high-spec developer environments.\n- **Industry Integration:** Internships with top-tier technology firms.\n- **Research Focused:** Active publication in artificial intelligence and systems design.",
                'logo_path' => '/images/cit-logo.svg',
            ],
            [
                'name' => 'College of Business Administration',
                'slug' => 'cba',
                'description' => 'Developing ethical business leaders, entrepreneurs, and financial managers who drive sustainable economic growth and societal progress.',
                'hero_content' => "# Shape the Future of Commerce\n\nAt the College of Business Administration, we foster entrepreneurship, strategic marketing, and ethical leadership. We prepare students for the complexities of global trade, digital finance, and corporate management.\n\n### Key Highlights\n- **Business Incubator:** Pitch, prototype, and launch your own startup.\n- **Case-Study Method:** Learn through real-world corporate challenges.\n- **Global Networking:** Mentorship programs with successful alumni business owners.",
                'logo_path' => '/images/cba-logo.svg',
            ],
            [
                'name' => 'College of Education',
                'slug' => 'coe',
                'description' => 'Nurturing compassionate educators, curriculum developers, and academic leaders dedicated to transformative learning experiences.',
                'hero_content' => "# Inspire Through Transformative Education\n\nThe College of Education is the cornerstone of San Isidro College's academic legacy. We combine pedagogical theory with extensive classroom practice to shape teachers who inspire and lead.\n\n### Specializations Offered\n- **Elementary Education:** Child development and fundamental learning systems.\n- **Secondary Education:** Math, Science, English, and Social Studies majors.\n- **Special Needs Education:** Inclusive teaching and learning strategies.",
                'logo_path' => '/images/coe-logo.svg',
            ],
        ];

        $departments = [];
        foreach ($depts as $dept) {
            $departments[$dept['slug']] = Department::create($dept);
        }

        // 3. Create Users
        $superAdmin = User::create([
            'name' => 'Dr. Maria Clara (Super Admin)',
            'email' => 'admin@email.com',
            'password' => Hash::make('12341234'),
            'role' => 'superadmin',
        ]);

        $citAdmin = User::create([
            'name' => 'Prof. Juan Dela Cruz (CIT Admin)',
            'email' => 'cit@sic.edu',
            'password' => Hash::make('password'),
            'role' => 'department_user',
            'department_id' => $departments['cit']->id,
        ]);

        $cbaAdmin = User::create([
            'name' => 'Dr. Jose Rizal (CBA Admin)',
            'email' => 'cba@sic.edu',
            'password' => Hash::make('password'),
            'role' => 'department_user',
            'department_id' => $departments['cba']->id,
        ]);

        // 4. Create Landing Page Carousel Items
        CarouselItem::create([
            'title' => 'Welcome to San Isidro College',
            'subtitle' => 'A premier institution of learning, values, and community service since 1952.',
            'image_path' => '/images/carousel-main-1.jpg',
            'link_url' => '/contact',
            'order' => 1,
        ]);

        CarouselItem::create([
            'title' => 'Now Accepting Enrolment for Academic Year 2026-2027',
            'subtitle' => 'Online enrollment is fully open for Freshmen, Transferees, and Graduate School.',
            'image_path' => '/images/carousel-main-2.jpg',
            'link_url' => '/downloads',
            'order' => 2,
        ]);

        CarouselItem::create([
            'title' => 'Celebrating 74 Years of Academic Excellence',
            'subtitle' => 'Join us as we host the National SIC Foundation Week from August 15-20, 2026.',
            'image_path' => '/images/carousel-main-3.jpg',
            'link_url' => '/posts',
            'order' => 3,
        ]);

        // Create Department Specific Carousels
        CarouselItem::create([
            'title' => 'CIT Hackathon 2026',
            'subtitle' => '24 hours of coding, building, and solving local problems.',
            'image_path' => '/images/carousel-cit-1.jpg',
            'link_url' => '/posts',
            'order' => 1,
            'department_id' => $departments['cit']->id,
        ]);

        CarouselItem::create([
            'title' => 'CBA Entrepreneurs Fair',
            'subtitle' => 'Support student-led business ventures and startups at the SIC Grand Lobby.',
            'image_path' => '/images/carousel-cba-1.jpg',
            'link_url' => '/posts',
            'order' => 1,
            'department_id' => $departments['cba']->id,
        ]);

        // 5. Create Posts (Articles, Events, Announcements)
        $postsData = [
            // General Posts
            [
                'title' => 'SIC Enrolment Guide for First Semester 2026-2027',
                'content' => "# Complete Enrolment Steps\n\nWelcome to San Isidro College! Follow this simple guide to complete your registration.\n\n### Step 1: Admissions Review\nSubmit your transcript, birth certificate, and high school diploma to the Registrar Office.\n\n### Step 2: Faculty Evaluation\nVisit your department dean's office for academic advising and course scheduling.\n\n### Step 3: Payment & Verification\nPay your tuition fee at the Cashier's Desk or online bank transfer, then check your portal.",
                'type' => 'announcement',
                'start_date' => null,
                'end_date' => null,
                'highlight_image_path' => '/images/post-enrolment.jpg',
                'department_id' => null,
                'user_id' => $superAdmin->id,
                'is_featured' => true,
            ],
            [
                'title' => '74th Foundation Week Celebration',
                'content' => "# Celebrate with Pride!\n\nOur annual Foundation Week features sports events, music festivals, food trucks, and academic debates.\n\n### Schedule of Key Events\n- **August 15:** Grand Opening Parade (8:00 AM)\n- **August 17:** College Quiz Bee & IT Hackathon Exhibition\n- **August 19:** Alumni Dinner Dance & Gala Concert\n- **August 20:** Awarding Ceremony & Fireworks Display",
                'type' => 'event',
                'start_date' => '2026-08-15 08:00:00',
                'end_date' => '2026-08-20 22:00:00',
                'highlight_image_path' => '/images/post-foundation.jpg',
                'department_id' => null,
                'user_id' => $superAdmin->id,
                'is_featured' => true,
            ],
            // CIT Specific Posts
            [
                'title' => 'CIT Unveils New AI Research Laboratory',
                'content' => "# Leading the Future of AI\n\nThe College of Information Technology is excited to announce the official opening of the AI Research Lab. Sponsored by local tech alumni, the lab houses top-tier GPUs to support student research in computer vision, large language models, and IoT automation.\n\nDean Del Cruz expressed his excitement:\n> \"This laboratory provides our software engineering students with the computing power needed to solve real-world problems using machine learning.\"",
                'type' => 'article',
                'start_date' => null,
                'end_date' => null,
                'highlight_image_path' => '/images/post-ai-lab.jpg',
                'department_id' => $departments['cit']->id,
                'user_id' => $citAdmin->id,
                'is_featured' => false,
            ],
            [
                'title' => 'CIT Tech Seminar: Quantum Computing Basics',
                'content' => "# Join the Seminar!\n\nLearn the fundamentals of quantum gates, superposition, and next-generation cryptography in this introductory seminar led by guest speakers from global technology firms.\n\n### Details\n- **Room:** CIT Lab 3\n- **Duration:** 3 Hours\n- **Prerequisites:** Basic understanding of algebra and Boolean logic.",
                'type' => 'event',
                'start_date' => '2026-07-20 13:00:00',
                'end_date' => '2026-07-20 16:00:00',
                'highlight_image_path' => '/images/post-quantum.jpg',
                'department_id' => $departments['cit']->id,
                'user_id' => $citAdmin->id,
                'is_featured' => false,
            ],
            // CBA Specific Posts
            [
                'title' => 'CBA Students Win National Entrepreneurship Award',
                'content' => "# Championing Innovation\n\nA team of three senior Business Administration students won first place at the National Business Innovation Pitch for their sustainable urban agriculture platform. The startup secures seed funding and office space inside the SIC Business Incubator.\n\n### Winning Team Members\n- **Juan Luna** (Finance)\n- **Ana Maria** (Marketing)\n- **Luis Miguel** (Operations)",
                'type' => 'article',
                'start_date' => null,
                'end_date' => null,
                'highlight_image_path' => '/images/post-cba-award.jpg',
                'department_id' => $departments['cba']->id,
                'user_id' => $cbaAdmin->id,
                'is_featured' => false,
            ],
        ];

        foreach ($postsData as $post) {
            $post['slug'] = Str::slug($post['title']);
            Post::create($post);
        }

        // 6. Create Members (Org Chart)
        // CIT Org Chart
        $citDean = Member::create([
            'name' => 'Prof. Juan Dela Cruz',
            'role' => 'Dean, College of IT',
            'department_id' => $departments['cit']->id,
            'parent_id' => null,
            'order' => 1,
            'image_path' => '/images/member-juan.jpg',
        ]);

        $citCSHead = Member::create([
            'name' => 'Dr. Alan Turing',
            'role' => 'Head of Computer Science',
            'department_id' => $departments['cit']->id,
            'parent_id' => $citDean->id,
            'order' => 1,
            'image_path' => '/images/member-alan.jpg',
        ]);

        $citISHead = Member::create([
            'name' => 'Dr. Grace Hopper',
            'role' => 'Head of Information Systems',
            'department_id' => $departments['cit']->id,
            'parent_id' => $citDean->id,
            'order' => 2,
            'image_path' => '/images/member-grace.jpg',
        ]);

        Member::create([
            'name' => 'Ada Lovelace',
            'role' => 'Software Engineering Instructor',
            'department_id' => $departments['cit']->id,
            'parent_id' => $citCSHead->id,
            'order' => 1,
            'image_path' => '/images/member-ada.jpg',
        ]);

        Member::create([
            'name' => 'Tim Berners-Lee',
            'role' => 'Web Technologies Instructor',
            'department_id' => $departments['cit']->id,
            'parent_id' => $citISHead->id,
            'order' => 1,
            'image_path' => '/images/member-tim.jpg',
        ]);

        // CBA Org Chart
        $cbaDean = Member::create([
            'name' => 'Dr. Jose Rizal',
            'role' => 'Dean, College of Business',
            'department_id' => $departments['cba']->id,
            'parent_id' => null,
            'order' => 1,
            'image_path' => '/images/member-jose.jpg',
        ]);

        Member::create([
            'name' => 'Adam Smith',
            'role' => 'Economics Department Chair',
            'department_id' => $departments['cba']->id,
            'parent_id' => $cbaDean->id,
            'order' => 1,
            'image_path' => '/images/member-adam.jpg',
        ]);

        // 7. Create Downloads (Files)
        // Ensure storage directory exists
        Storage::disk('public')->makeDirectory('downloads');

        // Create actual physical files for the seed downloads so they are not empty or missing
        $mockFiles = [
            'Student_Handbook_2026.pdf' => 'This is the official San Isidro College Student Handbook containing all academic rules, code of conduct, and campus guidelines for Academic Year 2026-2027.',
            'Academic_Calendar_AY_2026_2027.pdf' => 'Official Academic Calendar containing semestral schedules, exam periods, national holidays, and institutional events.',
            'CIT_Curriculum_BSCS_2026.pdf' => 'Bachelor of Science in Computer Science curriculum layout showing standard course sequences and descriptive course syllabi.',
            'CBA_Enrolment_Form.pdf' => 'College of Business Administration Enrolment and Advising Form for incoming Freshmen and Transferees.',
        ];

        foreach ($mockFiles as $filename => $content) {
            Storage::disk('public')->put('downloads/'.$filename, $content);
        }

        Download::create([
            'file_name' => 'SIC Student Handbook 2026',
            'file_path' => 'downloads/Student_Handbook_2026.pdf',
            'file_size' => Storage::disk('public')->size('downloads/Student_Handbook_2026.pdf'),
            'tags' => ['Handbook', 'Policy', 'Student'],
            'directory' => 'General Registrar',
            'department_id' => null,
            'user_id' => $superAdmin->id,
        ]);

        Download::create([
            'file_name' => 'Academic Calendar AY 2026-2027',
            'file_path' => 'downloads/Academic_Calendar_AY_2026_2027.pdf',
            'file_size' => Storage::disk('public')->size('downloads/Academic_Calendar_AY_2026_2027.pdf'),
            'tags' => ['Calendar', 'Schedule', 'Registrar'],
            'directory' => 'General Registrar',
            'department_id' => null,
            'user_id' => $superAdmin->id,
        ]);

        Download::create([
            'file_name' => 'BS Computer Science Curriculum',
            'file_path' => 'downloads/CIT_Curriculum_BSCS_2026.pdf',
            'file_size' => Storage::disk('public')->size('downloads/CIT_Curriculum_BSCS_2026.pdf'),
            'tags' => ['Curriculum', 'Syllabus', 'Computer Science'],
            'directory' => 'CIT Resources',
            'department_id' => $departments['cit']->id,
            'user_id' => $citAdmin->id,
        ]);

        Download::create([
            'file_name' => 'CBA Course Enrolment Form',
            'file_path' => 'downloads/CBA_Enrolment_Form.pdf',
            'file_size' => Storage::disk('public')->size('downloads/CBA_Enrolment_Form.pdf'),
            'tags' => ['Form', 'Enrolment', 'Business'],
            'directory' => 'CBA Resources',
            'department_id' => $departments['cba']->id,
            'user_id' => $cbaAdmin->id,
        ]);

        $this->call(SettingSeeder::class);
        $this->call(HeroSectionSeeder::class);
    }
}
