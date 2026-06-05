<script lang="ts">
    import BookOpen from 'lucide-svelte/icons/book-open';
    import Eye from 'lucide-svelte/icons/eye';
    import Globe from 'lucide-svelte/icons/globe';
    import GraduationCap from 'lucide-svelte/icons/graduation-cap';
    import HandHeart from 'lucide-svelte/icons/hand-heart';
    import Heart from 'lucide-svelte/icons/heart';
    import Lightbulb from 'lucide-svelte/icons/lightbulb';
    import Scale from 'lucide-svelte/icons/scale';
    import Shield from 'lucide-svelte/icons/shield';
    import Sparkles from 'lucide-svelte/icons/sparkles';
    import Target from 'lucide-svelte/icons/target';
    import Users from 'lucide-svelte/icons/users';
    import Newspaper from 'lucide-svelte/icons/newspaper';
    import History from 'lucide-svelte/icons/history';
    import Award from 'lucide-svelte/icons/award';
    import Building2 from 'lucide-svelte/icons/building-2';
    import ArrowRight from 'lucide-svelte/icons/arrow-right';

    import { Link } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import { Badge } from '@/components/ui/badge';
    import {
        Card,
        CardContent,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import { Separator } from '@/components/ui/separator';
    import PageLayout from '@/layouts/PageLayout.svelte';

    const coreValues = [
        {
            icon: Target,
            title: 'Excellence',
            description:
                'We pursue the highest standards in teaching, research, and community service. We challenge our students and ourselves to go beyond mediocrity, cultivating a culture of continuous improvement and intellectual rigor in everything we do.',
        },
        {
            icon: Heart,
            title: 'Faith',
            description:
                'Grounded in Christian values, we nurture the spiritual growth of every student. We believe that true education forms not just the mind but also the heart, fostering a deep sense of purpose and moral responsibility.',
        },
        {
            icon: HandHeart,
            title: 'Service',
            description:
                'We are dedicated to serving others and building a just and compassionate society. Through community outreach, volunteerism, and social engagement, we instill in our students a lifelong commitment to the common good.',
        },
        {
            icon: Users,
            title: 'Community',
            description:
                'We foster a welcoming and inclusive environment where everyone belongs. Our campus is a place of mutual respect, collaboration, and shared responsibility — where diverse perspectives are celebrated and every voice matters.',
        },
        {
            icon: Shield,
            title: 'Integrity',
            description:
                'We uphold honesty, transparency, and ethical conduct in all our endeavors. We hold ourselves accountable to the highest standards of academic and professional integrity, building trust with our students, partners, and communities.',
        },
        {
            icon: Lightbulb,
            title: 'Innovation',
            description:
                'We embrace creativity and forward-thinking approaches to education. By integrating modern technology, research-driven pedagogy, and interdisciplinary learning, we prepare our students to thrive in a rapidly changing world.',
        },
    ];

    const goals = [
        {
            icon: GraduationCap,
            title: 'Academic Excellence',
            description:
                'Deliver rigorous, relevant, and responsive curricula that meet national and international standards, equipping graduates with the knowledge and skills needed for professional success.',
        },
        {
            icon: BookOpen,
            title: 'Research & Innovation',
            description:
                'Foster a vibrant culture of research and innovation that contributes to the advancement of knowledge and addresses the challenges facing our communities and society.',
        },
        {
            icon: Globe,
            title: 'Community Engagement',
            description:
                'Strengthen partnerships with local communities, government agencies, and the private sector to create meaningful, sustainable impact through outreach programs and collaborative projects.',
        },
        {
            icon: Sparkles,
            title: 'Holistic Formation',
            description:
                'Develop well-rounded individuals through programs that integrate academic learning with spiritual formation, character development, physical wellness, and social responsibility.',
        },
        {
            icon: Scale,
            title: 'Good Governance',
            description:
                'Maintain transparent, accountable, and participatory institutional governance that ensures efficient resource management and continuous organizational improvement.',
        },
        {
            icon: Users,
            title: 'Inclusive Access',
            description:
                'Expand access to quality education for underserved and marginalized communities through scholarships, flexible learning modalities, and support services that reduce barriers to learning.',
        },
    ];

    const quickLinks = [
        {
            icon: Newspaper,
            title: 'Latest News & Events',
            description: 'Stay updated with the latest happenings, announcements, and events at SIC.',
            href: '/news',
        },
        {
            icon: History,
            title: 'Our History',
            description: 'Explore the milestones and rich heritage that shaped San Isidro College.',
            href: '/history',
        },
        {
            icon: Award,
            title: 'Scholarships',
            description: 'Learn about financial aid options and scholarship opportunities available.',
            href: '/scholarships',
        },
        {
            icon: Building2,
            title: 'Organization',
            description: 'Meet our administration, departments, and esteemed faculty members.',
            href: '/organization',
        },
    ];
</script>

<AppHead title="Mission and Vision">
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
</AppHead>

<PageLayout>
    <!-- Hero Banner -->
    <section
        class="relative overflow-hidden bg-primary py-20 text-primary-foreground sm:py-28"
    >
        <div class="absolute inset-0 opacity-10">
            <div
                class="absolute inset-0 bg-[radial-gradient(circle_at_30%_50%,rgba(255,255,255,0.2),transparent_70%)]"
            ></div>
            <div
                class="absolute inset-0 bg-[radial-gradient(circle_at_70%_80%,rgba(255,255,255,0.15),transparent_60%)]"
            ></div>
        </div>
        <div
            class="relative mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8"
        >
            <Badge variant="secondary" class="mb-6 text-sm">Ora et Labora</Badge>
            <h1
                class="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl"
            >
                Mission & Vision
            </h1>
            <p
                class="mx-auto mt-6 max-w-2xl text-lg text-primary-foreground/80"
            >
                Guided by faith and driven by purpose, we are committed to
                shaping competent, values-driven leaders who serve God, country,
                and community.
            </p>
        </div>
    </section>

    <!-- Vision Section -->
    <section class="py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <div>
                    <div class="mb-4 flex items-center gap-3">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary"
                        >
                            <Eye class="h-6 w-6" />
                        </div>
                        <Badge variant="secondary">Our Vision</Badge>
                    </div>
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                        Our Vision
                    </h2>
                    <div class="mt-6 border-l-4 border-primary pl-6 py-2">
                        <p
                            class="text-xl sm:text-2xl font-medium italic leading-relaxed text-foreground"
                        >
                            "To be a premier institution of higher learning in the
                            region, recognized for academic excellence, innovative
                            research, and transformative community engagement —
                            producing competent, values-driven graduates who are
                            agents of positive change in society."
                        </p>
                    </div>
                    <p class="mt-6 leading-relaxed text-muted-foreground">
                        We envision a future where every San Isidro College
                        graduate is equipped not only with professional
                        competence but also with the moral courage and
                        compassion to make a meaningful difference in the world.
                        Our commitment extends beyond the classroom — we aspire
                        to be a catalyst for social transformation, economic
                        development, and cultural enrichment in every community
                        we serve.
                    </p>
                    <p class="mt-4 leading-relaxed text-muted-foreground">
                        As we look ahead, we see San Isidro College as a hub of
                        innovation and collaboration, bridging the gap between
                        academic knowledge and real-world application, and
                        empowering the next generation to lead with integrity,
                        creativity, and heart.
                    </p>
                </div>
                <div class="relative">
                    <div class="overflow-hidden rounded-xl shadow-lg">
                        <img
                            src="https://images.unsplash.com/photo-1523050854058-8df90110c476?w=700&h=500&fit=crop"
                            alt="Students looking toward the future"
                            class="h-full w-full object-cover"
                        />
                    </div>
                    <div
                        class="absolute -bottom-4 -right-4 rounded-lg bg-primary px-5 py-3 text-primary-foreground shadow-lg"
                    >
                        <Eye class="h-6 w-6" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <Separator class="mx-auto max-w-7xl" />

    <!-- Mission Section -->
    <section class="py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <div class="order-2 lg:order-1">
                    <div class="overflow-hidden rounded-xl shadow-lg">
                        <img
                            src="https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?w=700&h=500&fit=crop"
                            alt="Students learning together"
                            class="h-full w-full object-cover"
                        />
                    </div>
                </div>
                <div class="order-1 lg:order-2">
                    <div class="mb-4 flex items-center gap-3">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary"
                        >
                            <Target class="h-6 w-6" />
                        </div>
                        <Badge variant="secondary">Our Mission</Badge>
                    </div>
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                        Our Mission
                    </h2>
                    <div class="mt-6 border-l-4 border-primary pl-6 py-2">
                        <p
                            class="text-xl sm:text-2xl font-medium italic leading-relaxed text-foreground"
                        >
                            "San Isidro College is committed to providing
                            accessible, quality, and relevant education that
                            develops the intellectual, moral, and social
                            capabilities of its students. Through faith-centered
                            formation, rigorous academics, and active community
                            partnership, we prepare our graduates to lead with
                            integrity and serve with compassion."
                        </p>
                    </div>
                    <p class="mt-6 leading-relaxed text-muted-foreground">
                        We believe that every student deserves the
                        opportunity to discover their potential, develop their
                        talents, and contribute meaningfully to society.
                    </p>
                    <p class="mt-4 leading-relaxed text-muted-foreground">
                        Our mission is realized through three interconnected
                        pillars: <strong class="text-foreground"
                            >instruction</strong
                        >
                        that is learner-centered, competency-based, and globally relevant;
                        <strong class="text-foreground">research</strong>
                        that is innovative, ethical, and responsive to the needs of
                        society; and
                        <strong class="text-foreground"
                            >community extension</strong
                        > that is collaborative, sustainable, and rooted in genuine
                        concern for the welfare of others.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <Separator class="mx-auto max-w-7xl" />

    <!-- Core Values -->
    <section class="bg-muted/50 py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-14 text-center">
                <Badge variant="secondary" class="mb-4">What We Stand For</Badge>
                <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                    Our Core Values
                </h2>
                <p class="mx-auto mt-4 max-w-2xl text-muted-foreground">
                    These values serve as the foundation of everything we do —
                    guiding our decisions, shaping our culture, and defining who
                    we are as an institution.
                </p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                {#each coreValues as value (value.title)}
                    <Card class="transition hover:shadow-md">
                        <CardHeader>
                            <div
                                class="mb-2 flex h-14 w-14 items-center justify-center rounded-full bg-primary/10 text-primary"
                            >
                                <value.icon class="h-7 w-7" />
                            </div>
                            <CardTitle class="text-xl">{value.title}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="leading-relaxed text-muted-foreground">
                                {value.description}
                            </p>
                        </CardContent>
                    </Card>
                {/each}
            </div>
        </div>
    </section>

    <!-- Motto Section -->
    <section
        class="relative overflow-hidden bg-primary py-16 text-primary-foreground sm:py-20"
    >
        <div class="absolute inset-0 opacity-10">
            <div
                class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(255,255,255,0.2),transparent_60%)]"
            ></div>
        </div>
        <div
            class="relative mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8"
        >
            <p
                class="text-5xl font-bold italic tracking-tight sm:text-6xl lg:text-7xl"
            >
                "Ora et Labora"
            </p>
            <p class="mt-6 text-xl text-primary-foreground/80">Pray and Work</p>
            <Separator class="mx-auto my-8 max-w-xs bg-primary-foreground/20" />
            <p
                class="mx-auto max-w-2xl text-lg leading-relaxed text-primary-foreground/70"
            >
                Our motto reflects the twin pillars of our identity — a deep
                faith in God's providence and an unwavering commitment to
                diligent, purposeful action. At San Isidro College, we believe
                that prayerful reflection and dedicated effort together forge
                the path to true excellence.
            </p>
        </div>
    </section>

    <!-- Institutional Goals -->
    <section class="py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-14 text-center">
                <Badge variant="secondary" class="mb-4">Our Commitment</Badge>
                <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                    Institutional Goals
                </h2>
                <p class="mx-auto mt-4 max-w-2xl text-muted-foreground">
                    To fulfill our mission and realize our vision, we pursue
                    these strategic goals that guide our programs, investments,
                    and partnerships.
                </p>
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                {#each goals as goal, i (goal.title)}
                    <div class="flex gap-4">
                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary"
                        >
                            <goal.icon class="h-6 w-6" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">{goal.title}</h3>
                            <p
                                class="mt-2 text-sm leading-relaxed text-muted-foreground"
                            >
                                {goal.description}
                            </p>
                        </div>
                    </div>
                {/each}
            </div>
        </div>
    </section>

    <Separator class="mx-auto max-w-7xl" />

    <!-- Quality Policy -->
    <section class="bg-muted/50 py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-4xl text-center">
                <Badge variant="secondary" class="mb-4">Quality Assurance</Badge>
                <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                    Our Quality Policy
                </h2>
                <p class="mt-6 text-lg leading-relaxed text-muted-foreground">
                    San Isidro College is committed to providing quality
                    education that meets the evolving needs of its stakeholders.
                    We ensure continuous improvement of our programs, services,
                    and processes through a culture of excellence,
                    accountability, and innovation.
                </p>
                <p class="mt-4 leading-relaxed text-muted-foreground">
                    We adhere to the standards set by the Commission on Higher
                    Education (CHED) and accrediting bodies, while proactively
                    seeking best practices and benchmarking against leading
                    institutions. Our quality management system is driven by
                    data, feedback, and a genuine commitment to the success of
                    every learner.
                </p>
            </div>

            <div
                class="mx-auto mt-12 grid max-w-4xl grid-cols-2 gap-6 md:grid-cols-4"
            >
                <div class="rounded-xl bg-background p-6 text-center shadow-sm">
                    <p class="text-4xl font-bold text-primary">100%</p>
                    <p class="mt-1 text-sm font-medium text-muted-foreground">
                        CHED Compliance
                    </p>
                </div>
                <div class="rounded-xl bg-background p-6 text-center shadow-sm">
                    <p class="text-4xl font-bold text-primary">Level III</p>
                    <p class="mt-1 text-sm font-medium text-muted-foreground">
                        Accreditation Status
                    </p>
                </div>
                <div class="rounded-xl bg-background p-6 text-center shadow-sm">
                    <p class="text-4xl font-bold text-primary">95%</p>
                    <p class="mt-1 text-sm font-medium text-muted-foreground">
                        Licensure Pass Rate
                    </p>
                </div>
                <div class="rounded-xl bg-background p-6 text-center shadow-sm">
                    <p class="text-4xl font-bold text-primary">90%</p>
                    <p class="mt-1 text-sm font-medium text-muted-foreground">
                        Employment Rate
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Explore More Section (Quick Links) -->
    <section class="py-16 sm:py-20 border-t">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-12 text-center">
                <Badge variant="secondary" class="mb-4">Explore More</Badge>
                <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                    Discover San Isidro College
                </h2>
                <p class="mx-auto mt-4 max-w-2xl text-muted-foreground">
                    Learn more about our school community, history, resources, and achievements.
                </p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                {#each quickLinks as link (link.title)}
                    <Link href={link.href} class="group block h-full">
                        <Card class="h-full border transition hover:border-primary hover:shadow-md">
                            <CardHeader class="pb-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/5 text-primary group-hover:bg-primary group-hover:text-primary-foreground transition-all duration-300">
                                        <link.icon class="h-5 w-5" />
                                    </div>
                                    <ArrowRight class="h-4 w-4 text-muted-foreground/40 group-hover:text-primary group-hover:translate-x-1 transition-all duration-300" />
                                </div>
                                <CardTitle class="mt-4 text-lg font-semibold group-hover:text-primary transition-colors">
                                    {link.title}
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p class="text-sm leading-relaxed text-muted-foreground">
                                    {link.description}
                                </p>
                            </CardContent>
                        </Card>
                    </Link>
                {/each}
            </div>
        </div>
    </section>
</PageLayout>
