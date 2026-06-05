<script lang="ts">
    import Award from 'lucide-svelte/icons/award';
    import GraduationCap from 'lucide-svelte/icons/graduation-cap';
    import ClipboardList from 'lucide-svelte/icons/clipboard-list';
    import CheckCircle from 'lucide-svelte/icons/check-circle';

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

    let {
        scholarships = [],
    }: {
        scholarships: Array<{
            id: number;
            name: string;
            coverage: string;
            description: string;
            requirements: string | null;
            is_active: boolean;
        }>;
    } = $props();
</script>

<AppHead title="Scholarships & Financial Aid">
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
            <Badge variant="secondary" class="mb-6 text-sm">Opportunities</Badge>
            <h1
                class="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl"
            >
                Scholarships & Financial Aid
            </h1>
            <p
                class="mx-auto mt-6 max-w-2xl text-lg text-primary-foreground/80"
            >
                At San Isidro College, we believe that financial challenges should not stand in the way of academic dreams. Explore our various programs designed to support deserving learners.
            </p>
        </div>
    </section>

    <!-- Overview / Mission of Financial Support -->
    <section class="py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <div>
                    <Badge variant="secondary" class="mb-4">Inclusive Education</Badge>
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                        Empowering Your Academic Journey
                    </h2>
                    <p class="mt-6 leading-relaxed text-muted-foreground">
                        San Isidro College is committed to making high-quality education accessible to all. Through the generosity of our founders, alumni, partners, and institutional sponsors, we offer a diverse range of scholarships, merit awards, and financial assistance programs.
                    </p>
                    <p class="mt-4 leading-relaxed text-muted-foreground">
                        Whether you are an academic achiever, a talented athlete, or in need of financial backing, we have options to help you complete your education successfully. Review the requirements for each option and prepare your application package.
                    </p>
                </div>
                <div class="relative">
                    <div class="overflow-hidden rounded-xl shadow-lg">
                        <img
                            src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=700&h=500&fit=crop"
                            alt="Happy students celebrating scholarship opportunity"
                            class="h-full w-full object-cover"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <Separator class="mx-auto max-w-7xl" />

    <!-- Active Scholarships List -->
    <section class="py-16 sm:py-20 bg-muted/30">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-14 text-center">
                <Badge variant="secondary" class="mb-4">Available Programs</Badge>
                <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                    Our Scholarship Schemes
                </h2>
                <p class="mx-auto mt-4 max-w-2xl text-muted-foreground">
                    Discover our current scholarship offerings, their coverage, and application criteria.
                </p>
            </div>

            {#if scholarships.length === 0}
                <div class="text-center py-12 bg-background rounded-xl border border-dashed p-8">
                    <GraduationCap class="mx-auto h-12 w-12 text-muted-foreground/60" />
                    <h3 class="mt-4 text-lg font-semibold">No Scholarships Posted</h3>
                    <p class="mt-2 text-sm text-muted-foreground">Please check back later or contact the Admissions Office.</p>
                </div>
            {:else}
                <div class="grid gap-8 lg:grid-cols-2">
                    {#each scholarships as scholarship (scholarship.id)}
                        <Card class="flex flex-col h-full bg-background transition duration-300 hover:shadow-lg hover:-translate-y-1">
                            <CardHeader class="border-b bg-muted/10 pb-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-center gap-3 flex-grow min-w-0">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary shrink-0">
                                            <Award class="h-5 w-5" />
                                        </div>
                                        <div class="min-w-0 flex-grow">
                                            <CardTitle class="text-xl font-bold tracking-tight truncate">
                                                {scholarship.name}
                                            </CardTitle>
                                        </div>
                                    </div>
                                    <Badge variant="outline" class="bg-primary/5 text-primary border-primary/20 shrink-0 text-sm font-semibold py-1">
                                        {scholarship.coverage}
                                    </Badge>
                                </div>
                            </CardHeader>
                            <CardContent class="flex-grow pt-6">
                                <h4 class="text-sm font-bold uppercase tracking-wider text-muted-foreground mb-2">
                                    About the Program
                                </h4>
                                <p class="text-sm leading-relaxed text-muted-foreground mb-6">
                                    {scholarship.description}
                                </p>

                                {#if scholarship.requirements}
                                    <Separator class="my-4" />
                                    <h4 class="text-sm font-bold uppercase tracking-wider text-muted-foreground mb-3 flex items-center gap-2">
                                        <ClipboardList class="h-4 w-4 text-primary" />
                                        Requirements & Criteria
                                    </h4>
                                    <ul class="space-y-2 text-sm text-muted-foreground">
                                        {#each scholarship.requirements.split('\n') as req}
                                            {#if req.trim()}
                                                <li class="flex items-start gap-2">
                                                    <CheckCircle class="h-4 w-4 text-green-500 shrink-0 mt-0.5" />
                                                    <span>{req.replace(/^[•\-\*]\s*/, '')}</span>
                                                </li>
                                            {/if}
                                        {/each}
                                    </ul>
                                {/if}
                            </CardContent>
                        </Card>
                    {/each}
                </div>
            {/if}
        </div>
    </section>

    <!-- Call to Action / How to Apply -->
    <section class="relative overflow-hidden bg-primary py-16 text-primary-foreground">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(255,255,255,0.2),transparent_60%)]"></div>
        </div>
        <div class="relative mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                Ready to Apply?
            </h2>
            <p class="mx-auto mt-6 max-w-2xl text-lg text-primary-foreground/80 leading-relaxed">
                Scholarship applications are accepted during the enrollment period. For applications, submissions, and detailed inquiries, please visit or contact the Registrar or Admission Office.
            </p>
            <div class="mt-8 flex justify-center gap-4">
                <a href="#contact" class="inline-flex items-center justify-center rounded-lg bg-background px-6 py-3 text-sm font-semibold text-primary shadow-sm hover:bg-muted transition duration-200">
                    Contact Admissions
                </a>
            </div>
        </div>
    </section>
</PageLayout>
