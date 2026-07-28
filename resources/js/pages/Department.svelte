<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import PublicLayout from '@/components/PublicLayout.svelte';
    import { parseMarkdown } from '@/lib/markdown';
    import { Link } from '@inertiajs/svelte';
    import { ArrowRight, ChevronLeft, ChevronRight, User, Users, FileText, Calendar, Megaphone } from 'lucide-svelte';
    import { onMount, onDestroy } from 'svelte';

    let {
        department,
        carouselItems = [],
        posts = [],
        rootMembers = [],
        navigationLinks = [],
        departments = [],
        heroSections = [],
    }: {
        department: any;
        carouselItems?: any[];
        posts?: any[];
        rootMembers?: any[];
        navigationLinks?: any[];
        departments?: any[];
        heroSections?: any[];
    } = $props();

    // Carousel State
    let currentSlide = $state(0);
    let carouselInterval: any;

    function nextSlide() {
        if (carouselItems.length > 0) {
            currentSlide = (currentSlide + 1) % carouselItems.length;
        }
    }

    function prevSlide() {
        if (carouselItems.length > 0) {
            currentSlide = (currentSlide - 1 + carouselItems.length) % carouselItems.length;
        }
    }

    onMount(() => {
        if (carouselItems.length > 0) {
            carouselInterval = setInterval(nextSlide, 6000);
        }
    });

    onDestroy(() => {
        if (carouselInterval) clearInterval(carouselInterval);
    });

    const parsedHeroHtml = $derived(parseMarkdown(department.hero_content));
</script>

<AppHead title={`${department.name} - San Isidro College`} />

<PublicLayout {navigationLinks} {departments}>
    <!-- Department Carousel/Header -->
    {#if carouselItems.length > 0}
        <div class="relative h-[400px] w-full overflow-hidden bg-white md:h-[450px]">
            {#each carouselItems as item, index}
                <div 
                    class="absolute inset-0 transition-opacity duration-1000 ease-in-out {index === currentSlide ? 'opacity-100 z-10' : 'opacity-0 z-0'}"
                    style="background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.7)), url('{item.image_path}'); background-size: cover; background-position: center;"
                >
                    <div class="mx-auto flex h-full max-w-7xl flex-col justify-center px-6 text-white">
                        <div class="max-w-xl space-y-4">
                            <span class="inline-block rounded bg-amber-600 px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wider">
                                {department.name}
                            </span>
                            {#if item.title}
                                <h1 class="text-3xl font-extrabold tracking-tight md:text-5xl">
                                    {item.title}
                                </h1>
                            {/if}
                            {#if item.subtitle}
                                <p class="text-base md:text-lg text-neutral-200">
                                    {item.subtitle}
                                </p>
                            {/if}
                        </div>
                    </div>
                </div>
            {/each}

            <!-- Carousel navigation buttons (only show if > 1 slide) -->
            {#if carouselItems.length > 1}
                <button 
                    onclick={prevSlide}
                    class="absolute left-4 top-1/2 z-20 -translate-y-1/2 rounded-full bg-white/10 p-2.5 text-white backdrop-blur-sm transition-colors hover:bg-white/20"
                    aria-label="Previous slide"
                >
                    <ChevronLeft class="h-5 w-5" />
                </button>
                <button 
                    onclick={nextSlide}
                    class="absolute right-4 top-1/2 z-20 -translate-y-1/2 rounded-full bg-white/10 p-2.5 text-white backdrop-blur-sm transition-colors hover:bg-white/20"
                    aria-label="Next slide"
                >
                    <ChevronRight class="h-5 w-5" />
                </button>
            {/if}
        </div>
    {:else}
        <!-- Fallback header if no carousel -->
        <div class="bg-neutral-900 py-20 text-white">
            <div class="mx-auto max-w-7xl px-6">
                <span class="inline-block rounded bg-amber-600 px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wider">
                    Academic Department
                </span>
                <h1 class="mt-4 text-4xl font-extrabold tracking-tight md:text-5xl">{department.name}</h1>
                <p class="mt-4 max-w-2xl text-lg text-neutral-300">{department.description}</p>
            </div>
        </div>
    {/if}

    <!-- Hero Content (Parsed MDX) -->
    {#if department.hero_content}
        <section class="py-16 md:py-24 border-b border-neutral-100 dark:border-neutral-900 bg-white dark:bg-[#0a0a0a]">
            <div class="mx-auto max-w-4xl px-6">
                <div class="prose prose-neutral dark:prose-invert max-w-none">
                    {@html parsedHeroHtml}
                </div>
            </div>
        </section>
    {/if}

    <!-- Multiple Hero Sections (Parsed MDX Blocks) -->
    {#each heroSections as section}
        <section class="py-16 md:py-24 border-b border-neutral-100 dark:border-neutral-900 bg-white dark:bg-[#0a0a0a]">
            <div class="mx-auto max-w-4xl px-6">
                <div class="prose prose-neutral dark:prose-invert max-w-none font-sans">
                    {@html parseMarkdown(section.content)}
                </div>
            </div>
        </section>
    {/each}

    <!-- Organizational Chart -->
    {#if rootMembers.length > 0}
        <section class="bg-neutral-50 py-16 dark:bg-[#161615]">
            <div class="mx-auto max-w-7xl px-6">
                <div class="text-center">
                    <h2 class="text-3xl font-extrabold tracking-tight text-neutral-900 dark:text-white">College Leadership & Faculty</h2>
                    <p class="mt-2 text-neutral-500 dark:text-neutral-400">Our esteemed administrators, program chairs, and teaching staff.</p>
                </div>

                <div class="mt-16 flex flex-col items-center gap-12">
                    <!-- Recursive rendering of Org Chart -->
                    {#each rootMembers as root}
                        <div class="flex flex-col items-center">
                            <!-- Dean/Root Node -->
                            <div class="flex flex-col items-center rounded-2xl border border-black/5 bg-white p-6 text-center shadow-sm w-64 dark:border-white/5 dark:bg-[#0a0a0a]">
                                {#if root.image_path}
                                    <img src={root.image_path} alt={root.name} class="mx-auto h-20 w-20 rounded-full object-cover shadow-inner" />
                                {:else}
                                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-amber-600/10 text-amber-600">
                                        <User class="h-10 w-10" />
                                    </div>
                                {/if}
                                <h3 class="mt-4 text-base font-bold text-neutral-900 dark:text-white">{root.name}</h3>
                                <p class="text-xs font-semibold text-amber-600 dark:text-amber-500 mt-1 uppercase tracking-wider">{root.role}</p>
                            </div>

                            <!-- Level 2 (Program Chairs) -->
                            {#if root.children && root.children.length > 0}
                                <div class="w-px h-8 bg-neutral-200 dark:bg-neutral-800"></div>
                                <div class="relative flex flex-col md:flex-row gap-8 items-start justify-center">
                                    {#each root.children as child}
                                        <div class="flex flex-col items-center">
                                            <div class="flex flex-col items-center rounded-xl border border-black/5 bg-white p-5 text-center shadow-sm w-56 dark:border-white/5 dark:bg-[#0a0a0a]">
                                                {#if child.image_path}
                                                    <img src={child.image_path} alt={child.name} class="mx-auto h-16 w-16 rounded-full object-cover" />
                                                {:else}
                                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-amber-600/10 text-amber-600">
                                                        <User class="h-8 w-8" />
                                                    </div>
                                                {/if}
                                                <h4 class="mt-3 text-sm font-bold text-neutral-900 dark:text-white">{child.name}</h4>
                                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">{child.role}</p>
                                            </div>

                                            <!-- Level 3 (Instructors/Faculty) -->
                                            {#if child.children && child.children.length > 0}
                                                <div class="w-px h-6 bg-neutral-200 dark:bg-neutral-800"></div>
                                                <div class="flex flex-col gap-4">
                                                    {#each child.children as grandchild}
                                                        <div class="flex items-center gap-3 rounded-lg border border-black/5 bg-white px-4 py-2.5 shadow-sm w-56 dark:border-white/5 dark:bg-[#0a0a0a]">
                                                            {#if grandchild.image_path}
                                                                <img src={grandchild.image_path} alt={grandchild.name} class="h-10 w-10 rounded-full object-cover" />
                                                            {:else}
                                                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-600/10 text-amber-600">
                                                                    <User class="h-5 w-5" />
                                                                </div>
                                                            {/if}
                                                            <div>
                                                                <h5 class="text-xs font-bold text-neutral-900 dark:text-white leading-tight">{grandchild.name}</h5>
                                                                <p class="text-[10px] text-neutral-500 leading-tight mt-0.5">{grandchild.role}</p>
                                                            </div>
                                                        </div>
                                                    {/each}
                                                </div>
                                            {/if}
                                        </div>
                                    {/each}
                                </div>
                            {/if}
                        </div>
                    {/each}
                </div>
            </div>
        </section>
    {/if}

    <!-- Department Posts Section -->
    {#if posts.length > 0}
        <section class="py-16">
            <div class="mx-auto max-w-7xl px-6">
                <h2 class="text-3xl font-extrabold tracking-tight text-neutral-900 dark:text-white">Department Announcements & Events</h2>
                <div class="mt-10 grid grid-cols-1 gap-8 md:grid-cols-3">
                    {#each posts as post}
                        <article class="flex flex-col overflow-hidden rounded-2xl border border-black/5 bg-white shadow-sm dark:border-white/5 dark:bg-[#161615]">
                            {#if post.highlight_image_path}
                                <img src={post.highlight_image_path} alt={post.title} class="h-44 w-full object-cover" />
                            {:else}
                                <div class="flex h-44 w-full items-center justify-center bg-amber-600/10 text-amber-600">
                                    {#if post.type === 'event'}
                                        <Calendar class="h-10 w-10" />
                                    {:else if post.type === 'announcement'}
                                        <Megaphone class="h-10 w-10" />
                                    {:else}
                                        <FileText class="h-10 w-10" />
                                    {/if}
                                </div>
                            {/if}
                            <div class="flex flex-1 flex-col p-6">
                                <div class="text-xs font-semibold uppercase tracking-wider text-amber-600">
                                    {post.type}
                                </div>
                                <h3 class="mt-2 text-base font-bold text-neutral-900 line-clamp-2 hover:text-amber-600 dark:text-white">
                                    <Link href={`/posts/${post.slug}`}>{post.title}</Link>
                                </h3>
                                <p class="mt-2 flex-1 text-xs text-neutral-500 dark:text-neutral-400 line-clamp-3">
                                    {post.content.replace(/[#*`>]/g, '')}
                                </p>
                                <div class="mt-6 flex items-center justify-between text-xs text-neutral-500">
                                    <span>{new Date(post.created_at).toLocaleDateString()}</span>
                                    <Link href={`/posts/${post.slug}`} class="font-bold text-amber-600 dark:text-amber-500">Read More</Link>
                                </div>
                            </div>
                        </article>
                    {/each}
                </div>
            </div>
        </section>
    {/if}
</PublicLayout>
