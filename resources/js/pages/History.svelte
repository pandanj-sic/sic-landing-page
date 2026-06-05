<script lang="ts">
    import BookOpen from 'lucide-svelte/icons/book-open';
    import Eye from 'lucide-svelte/icons/eye';
    import GraduationCap from 'lucide-svelte/icons/graduation-cap';
    import HandHeart from 'lucide-svelte/icons/hand-heart';
    import Handshake from 'lucide-svelte/icons/handshake';
    import Heart from 'lucide-svelte/icons/heart';
    import Landmark from 'lucide-svelte/icons/landmark';
    import Lightbulb from 'lucide-svelte/icons/lightbulb';
    import Medal from 'lucide-svelte/icons/medal';
    import School from 'lucide-svelte/icons/school';
    import Sparkles from 'lucide-svelte/icons/sparkles';
    import Star from 'lucide-svelte/icons/star';
    import Target from 'lucide-svelte/icons/target';
    import Trophy from 'lucide-svelte/icons/trophy';
    import Users from 'lucide-svelte/icons/users';

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

    const iconMap: Record<string, any> = {
        'book-open': BookOpen,
        eye: Eye,
        'graduation-cap': GraduationCap,
        'hand-heart': HandHeart,
        handshake: Handshake,
        heart: Heart,
        landmark: Landmark,
        lightbulb: Lightbulb,
        medal: Medal,
        school: School,
        sparkles: Sparkles,
        star: Star,
        target: Target,
        trophy: Trophy,
        users: Users,
    };

    let {
        timeline = [],
    }: {
        timeline: Array<{
            year: string;
            title: string;
            description: string;
            icon: string;
        }>;
    } = $props();
</script>

<AppHead title="History">
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
            <Badge variant="secondary" class="mb-6 text-sm">Est. 1965</Badge>
            <h1
                class="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl"
            >
                Our History
            </h1>
            <p
                class="mx-auto mt-6 max-w-2xl text-lg text-primary-foreground/80"
            >
                For nearly six decades, San Isidro College has been shaping
                minds, building character, and transforming lives through the
                power of education.
            </p>
            <p
                class="mx-auto mt-2 text-base font-medium italic text-primary-foreground/70"
            >
                Ora et Labora — Pray and Work
            </p>
        </div>
    </section>

    <!-- Founding Story -->
    <section class="py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <div>
                    <Badge variant="secondary" class="mb-4">Our Beginning</Badge
                    >
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                        Founded on Faith and Service
                    </h2>
                    <p class="mt-6 leading-relaxed text-muted-foreground">
                        In 1965, at a time when access to higher education was a
                        privilege reserved for the few, a group of dedicated
                        educators and community leaders came together with a
                        shared dream: to establish an institution that would
                        open the doors of learning to every deserving student,
                        regardless of background or circumstance.
                    </p>
                    <p class="mt-4 leading-relaxed text-muted-foreground">
                        Guided by the motto <strong class="text-foreground"
                            >"Ora et Labora"</strong
                        > — Pray and Work — San Isidro College was born from the belief
                        that education is both a sacred calling and a labor of love.
                        From its humble beginnings with just a handful of classrooms
                        and 150 students, the college has grown into a thriving institution
                        that has produced thousands of graduates who serve communities
                        across the nation and beyond.
                    </p>
                    <p class="mt-4 leading-relaxed text-muted-foreground">
                        Today, the founding spirit lives on in every classroom,
                        every research project, and every act of service that
                        defines the San Isidro College experience.
                    </p>
                </div>
                <div class="relative">
                    <div class="overflow-hidden rounded-xl shadow-lg">
                        <img
                            src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=700&h=500&fit=crop"
                            alt="Historic college campus"
                            class="h-full w-full object-cover"
                        />
                    </div>
                    <div
                        class="absolute -bottom-4 -left-4 rounded-lg bg-primary px-5 py-3 text-primary-foreground shadow-lg"
                    >
                        <p class="text-2xl font-bold">1965</p>
                        <p
                            class="text-xs uppercase tracking-wider text-primary-foreground/70"
                        >
                            Year Founded
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <Separator class="mx-auto max-w-7xl" />

    <!-- Timeline -->
    <section class="py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-14 text-center">
                <Badge variant="secondary" class="mb-4">Milestones</Badge>
                <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                    A Journey of Growth
                </h2>
                <p class="mx-auto mt-4 max-w-2xl text-muted-foreground">
                    From a small community college to a recognized institution
                    of higher learning — explore the key moments that shaped who
                    we are today.
                </p>
            </div>

            <!-- Timeline container -->
            <div class="relative">
                <!-- Central line (desktop) -->
                <div
                    class="absolute left-1/2 top-0 hidden h-full w-px -translate-x-1/2 bg-border lg:block"
                ></div>
                <!-- Left line (mobile/tablet) -->
                <div
                    class="absolute left-6 top-0 h-full w-px bg-border sm:left-8 lg:hidden"
                ></div>

                <div class="space-y-12 lg:space-y-16">
                    {#each timeline as milestone, i (milestone.year)}
                        {@const Icon = iconMap[milestone.icon] || Star}
                        <div
                            class="relative flex items-start gap-6 sm:gap-8 lg:gap-0 {i %
                                2 ===
                            0
                                ? 'lg:flex-row'
                                : 'lg:flex-row-reverse'}"
                        >
                            <!-- Dot on the line -->
                            <div
                                class="absolute left-6 z-10 flex h-12 w-12 -translate-x-1/2 items-center justify-center rounded-full border-4 border-background bg-primary text-primary-foreground shadow-md sm:left-8 lg:left-1/2"
                            >
                                <Icon class="h-5 w-5" />
                            </div>

                            <!-- Spacer for mobile left column -->
                            <div class="w-12 shrink-0 sm:w-14 lg:hidden"></div>

                            <!-- Content card -->
                            <div
                                class="flex-1 lg:w-[calc(50%-3rem)] {i % 2 === 0
                                    ? 'lg:pr-12 lg:text-right'
                                    : 'lg:pl-12'}"
                            >
                                <Card class="transition hover:shadow-md">
                                    <CardHeader>
                                        <Badge
                                            variant="outline"
                                            class="w-fit {i % 2 === 0
                                                ? 'lg:ml-auto'
                                                : ''}"
                                        >
                                            {milestone.year}
                                        </Badge>
                                        <CardTitle class="text-xl"
                                            >{milestone.title}</CardTitle
                                        >
                                    </CardHeader>
                                    <CardContent>
                                        <p
                                            class="leading-relaxed text-muted-foreground"
                                        >
                                            {milestone.description}
                                        </p>
                                    </CardContent>
                                </Card>
                            </div>

                            <!-- Empty spacer for the other side (desktop) -->
                            <div
                                class="hidden flex-1 lg:block lg:w-[calc(50%-3rem)]"
                            ></div>
                        </div>
                    {/each}
                </div>
            </div>
        </div>
    </section>

    <Separator class="mx-auto max-w-7xl" />

    <!-- By the Numbers -->
    <section class="py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-14 text-center">
                <Badge variant="secondary" class="mb-4">By the Numbers</Badge>
                <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                    Our Impact Over the Years
                </h2>
            </div>

            <div class="grid grid-cols-2 gap-6 md:grid-cols-4">
                <div class="rounded-xl bg-primary/5 p-6 text-center">
                    <p class="text-4xl font-bold text-primary">58+</p>
                    <p class="mt-1 text-sm font-medium text-muted-foreground">
                        Years of Excellence
                    </p>
                </div>
                <div class="rounded-xl bg-primary/5 p-6 text-center">
                    <p class="text-4xl font-bold text-primary">15,000+</p>
                    <p class="mt-1 text-sm font-medium text-muted-foreground">
                        Alumni Worldwide
                    </p>
                </div>
                <div class="rounded-xl bg-primary/5 p-6 text-center">
                    <p class="text-4xl font-bold text-primary">120+</p>
                    <p class="mt-1 text-sm font-medium text-muted-foreground">
                        Academic Programs
                    </p>
                </div>
                <div class="rounded-xl bg-primary/5 p-6 text-center">
                    <p class="text-4xl font-bold text-primary">500+</p>
                    <p class="mt-1 text-sm font-medium text-muted-foreground">
                        Faculty & Staff
                    </p>
                </div>
            </div>
        </div>
    </section>
</PageLayout>
