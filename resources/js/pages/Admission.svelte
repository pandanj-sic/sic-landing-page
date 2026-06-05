<script lang="ts">
    import ClipboardList from 'lucide-svelte/icons/clipboard-list';
    import GraduationCap from 'lucide-svelte/icons/graduation-cap';
    import HelpCircle from 'lucide-svelte/icons/help-circle';
    import ArrowRight from 'lucide-svelte/icons/arrow-right';

    import AppHead from '@/components/AppHead.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Card, CardContent } from '@/components/ui/card';
    import PageLayout from '@/layouts/PageLayout.svelte';

    let {
        sections = [],
    }: {
        sections: Array<{
            id: number;
            title: string;
            content: string;
            is_active: boolean;
            sort_order: number;
        }>;
    } = $props();

    function slugify(text: string): string {
        return text
            .toString()
            .toLowerCase()
            .replace(/\s+/g, '-')
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '-')
            .replace(/^-+/, '')
            .replace(/-+$/, '');
    }
</script>

<AppHead title="Admissions & Enrollment">
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
</AppHead>

<PageLayout>
    <!-- Hero Banner -->
    <section class="relative overflow-hidden bg-primary py-20 text-primary-foreground sm:py-28">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_50%,rgba(255,255,255,0.2),transparent_70%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_80%,rgba(255,255,255,0.15),transparent_60%)]"></div>
        </div>
        <div class="relative mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <Badge variant="secondary" class="mb-6 text-sm">Enrollment</Badge>
            <h1 class="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
                Admissions & Enrollment
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-lg text-primary-foreground/80">
                Join the San Isidro College community. Find all the details, procedures, and requirements to start your enrollment process here.
            </p>
        </div>
    </section>

    <!-- Main Content Grid -->
    <section class="py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {#if sections.length > 0}
                <div class="grid gap-10 lg:grid-cols-4">
                    <!-- Sidebar Navigation -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-24 hidden space-y-2 lg:block">
                            <h3 class="px-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                Quick Navigation
                            </h3>
                            <div class="flex flex-col space-y-1">
                                {#each sections as section}
                                    <a
                                        href={`#${slugify(section.title)}`}
                                        class="rounded-md px-3 py-2 text-sm font-medium text-muted-foreground transition hover:bg-muted hover:text-foreground"
                                    >
                                        {section.title}
                                    </a>
                                {/each}
                            </div>
                        </div>
                    </div>

                    <!-- Admission Content Sections -->
                    <div class="space-y-12 lg:col-span-3">
                        {#each sections as section}
                            <div id={slugify(section.title)} class="scroll-mt-24 space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                        <ClipboardList class="h-5 w-5" />
                                    </div>
                                    <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">
                                        {section.title}
                                    </h2>
                                </div>
                                <Card class="border shadow-sm">
                                    <CardContent class="p-6 sm:p-8">
                                        <div class="prose prose-slate max-w-none dark:prose-invert">
                                            {@html section.content}
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        {/each}
                    </div>
                </div>
            {:else}
                <div class="mx-auto max-w-2xl text-center">
                    <HelpCircle class="mx-auto h-12 w-12 text-muted-foreground/50" />
                    <h2 class="mt-4 text-2xl font-bold">No Admission Information Yet</h2>
                    <p class="mt-2 text-muted-foreground">
                        Please update this page content in the administration panel.
                    </p>
                </div>
            {/if}
        </div>
    </section>

    <!-- Call to Action / Support -->
    <section class="relative overflow-hidden bg-primary py-16 text-primary-foreground">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(255,255,255,0.2),transparent_60%)]"></div>
        </div>
        <div class="relative mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                Need Help with Your Application?
            </h2>
            <p class="mx-auto mt-6 max-w-2xl text-lg text-primary-foreground/80 leading-relaxed">
                Our Admission Office is here to guide you every step of the way. Reach out to us for any questions regarding enrollment requirements, schedules, or procedures.
            </p>
            <div class="mt-8 flex justify-center gap-4">
                <a href="https://siccollegeregistrar.com/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-lg bg-background px-6 py-3 text-sm font-semibold text-primary shadow-sm hover:bg-muted transition duration-200">
                    Visit Registrar Portal
                    <ArrowRight class="ml-2 h-4 w-4" />
                </a>
            </div>
        </div>
    </section>
</PageLayout>
