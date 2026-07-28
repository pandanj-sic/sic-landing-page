<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import PublicLayout from '@/components/PublicLayout.svelte';
    import { Link } from '@inertiajs/svelte';
    import { ArrowRight, Calendar, FileText, Megaphone, ChevronLeft, ChevronRight } from 'lucide-svelte';
    import { onDestroy, onMount } from 'svelte';
    import { parseMarkdown } from '@/lib/markdown';

    let {
        carouselItems = [],
        departments = [],
        featuredPosts = [],
        latestPosts = [],
        navigationLinks = [],
        welcomeHeroContent = '',
        heroSections = [],
    }: {
        carouselItems?: any[];
        departments?: any[];
        featuredPosts?: any[];
        latestPosts?: any[];
        navigationLinks?: any[];
        welcomeHeroContent?: string;
        heroSections?: any[];
    } = $props();

    const parsedHeroHtml = $derived(parseMarkdown(welcomeHeroContent));

    // Carousel State
    let currentSlide = $state(0);
    let carouselInterval: any;

    function nextSlide() {
        currentSlide = (currentSlide + 1) % carouselItems.length;
    }

    function prevSlide() {
        currentSlide = (currentSlide - 1 + carouselItems.length) % carouselItems.length;
    }

    onMount(() => {
        if (carouselItems.length > 0) {
            carouselInterval = setInterval(nextSlide, 6000);
        }
    });

    onDestroy(() => {
        if (carouselInterval) clearInterval(carouselInterval);
    });
</script>

<AppHead title="Welcome to San Isidro College" />

<PublicLayout {navigationLinks} {departments}>
    <!-- Hero Carousel Section -->
    {#if carouselItems.length > 0}
        <div class="relative h-[500px] w-full overflow-hidden bg-white md:h-[600px]">
            <!-- Slides -->
            {#each carouselItems as item, index}
                <div 
                    class="absolute inset-0 transition-opacity duration-1000 ease-in-out {index === currentSlide ? 'opacity-100 z-10' : 'opacity-0 z-0'}"
                    style="background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.7)), url('{item.image_path}'); background-size: cover; background-position: center;"
                >
                    <div class="mx-auto flex h-full max-w-7xl flex-col justify-center px-6 text-white">
                        <div class="max-w-2xl space-y-4">
                            {#if item.title}
                                <h1 class="text-4xl font-extrabold tracking-tight md:text-6xl animate-fade-in">
                                    {item.title}
                                </h1>
                            {/if}
                            {#if item.subtitle}
                                <p class="text-lg md:text-xl text-neutral-200">
                                    {item.subtitle}
                                </p>
                            {/if}
                            {#if item.link_url}
                                <div class="pt-4">
                                    <Link 
                                        href={item.link_url}
                                        class="inline-flex items-center gap-2 rounded-lg bg-amber-600 px-6 py-3 font-semibold text-white shadow-lg shadow-amber-600/20 transition-all hover:bg-amber-700 hover:shadow-amber-600/30"
                                    >
                                        Explore More <ArrowRight class="h-4 w-4" />
                                    </Link>
                                </div>
                            {/if}
                        </div>
                    </div>
                </div>
            {/each}

            <!-- Controls -->
            <button 
                onclick={prevSlide}
                class="absolute left-4 top-1/2 z-20 -translate-y-1/2 rounded-full bg-white/10 p-3 text-white backdrop-blur-sm transition-colors hover:bg-white/20"
                aria-label="Previous slide"
            >
                <ChevronLeft class="h-6 w-6" />
            </button>
            <button 
                onclick={nextSlide}
                class="absolute right-4 top-1/2 z-20 -translate-y-1/2 rounded-full bg-white/10 p-3 text-white backdrop-blur-sm transition-colors hover:bg-white/20"
                aria-label="Next slide"
            >
                <ChevronRight class="h-6 w-6" />
            </button>

            <!-- Indicators -->
            <div class="absolute bottom-6 left-1/2 z-20 flex -translate-x-1/2 gap-2">
                {#each carouselItems as _, index}
                    <button 
                        onclick={() => currentSlide = index}
                        class="h-2 w-8 rounded-full transition-all {index === currentSlide ? 'bg-amber-600' : 'bg-white/40'}"
                        aria-label="Go to slide {index + 1}"
                    ></button>
                {/each}
            </div>
        </div>
    {/if}

    <!-- Hero Content (Parsed MDX) -->
    {#if welcomeHeroContent}
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

    <!-- Features Section -->
    <section class="py-20">
        <div class="mx-auto max-w-7xl px-6">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold tracking-tight text-neutral-900 md:text-4xl dark:text-white">
                    Our Academic Programs
                </h2>
                <p class="mx-auto mt-4 max-w-2xl text-neutral-500 dark:text-neutral-400">
                    San Isidro College offers specialized college programs aimed at training professionals to be values-driven and globally competent.
                </p>
            </div>

            <div id="departments" class="mt-16 grid grid-cols-1 gap-8 md:grid-cols-3">
                {#each departments as dept}
                    <div class="flex flex-col rounded-2xl border border-black/5 bg-white p-8 shadow-sm transition-all hover:-translate-y-1 hover:shadow-md dark:border-white/5 dark:bg-[#161615]">
                        <h3 class="text-xl font-bold text-neutral-900 dark:text-white">{dept.name}</h3>
                        <p class="mt-4 flex-1 text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            {dept.description}
                        </p>
                        <div class="mt-8 pt-4 border-t border-neutral-100 dark:border-neutral-800">
                            <Link 
                                href={`/departments/${dept.slug}`}
                                class="inline-flex items-center gap-1.5 text-sm font-semibold text-amber-600 hover:text-amber-700 dark:text-amber-500"
                            >
                                View Department <ArrowRight class="h-4 w-4" />
                            </Link>
                        </div>
                    </div>
                {/each}
            </div>
        </div>
    </section>

    <!-- Latest News, Events, Announcements -->
    <section class="bg-neutral-50 py-20 dark:bg-[#161615]">
        <div class="mx-auto max-w-7xl px-6">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight text-neutral-900 dark:text-white">Latest from San Isidro</h2>
                    <p class="mt-2 text-neutral-500 dark:text-neutral-400">Stay updated with our news, campus events, and announcements.</p>
                </div>
                <Link 
                    href="/posts" 
                    class="inline-flex items-center gap-1 text-sm font-semibold text-amber-600 hover:underline dark:text-amber-500"
                >
                    View All Posts <ArrowRight class="h-4 w-4" />
                </Link>
            </div>

            <div class="mt-12 grid grid-cols-1 gap-8 md:grid-cols-3">
                {#each latestPosts as post}
                    <article class="flex flex-col overflow-hidden rounded-2xl border border-black/5 bg-white shadow-sm dark:border-white/5 dark:bg-[#0a0a0a]">
                        {#if post.highlight_image_path}
                            <img 
                                src={post.highlight_image_path} 
                                alt={post.title} 
                                class="h-48 w-full object-cover"
                            />
                        {:else}
                            <div class="flex h-48 w-full items-center justify-center bg-amber-600/10 text-amber-600">
                                {#if post.type === 'event'}
                                    <Calendar class="h-12 w-12" />
                                {:else if post.type === 'announcement'}
                                    <Megaphone class="h-12 w-12" />
                                {:else}
                                    <FileText class="h-12 w-12" />
                                {/if}
                            </div>
                        {/if}
                        <div class="flex flex-1 flex-col p-6">
                            <div class="flex items-center gap-2 text-xs">
                                <span class="rounded-full px-2.5 py-0.5 font-semibold capitalize 
                                    {post.type === 'event' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/35 dark:text-blue-400' : ''}
                                    {post.type === 'announcement' ? 'bg-red-50 text-red-700 dark:bg-red-900/35 dark:text-red-400' : ''}
                                    {post.type === 'article' ? 'bg-green-50 text-green-700 dark:bg-green-900/35 dark:text-green-400' : ''}
                                ">
                                    {post.type}
                                </span>
                                {#if post.department}
                                    <span class="text-neutral-400">|</span>
                                    <span class="text-neutral-500 dark:text-neutral-400">{post.department.name}</span>
                                {/if}
                            </div>
                            <h3 class="mt-3 text-lg font-bold text-neutral-900 line-clamp-2 hover:text-amber-600 dark:text-white">
                                <Link href={`/posts/${post.slug}`}>{post.title}</Link>
                            </h3>
                            <p class="mt-3 flex-1 text-sm text-neutral-500 dark:text-neutral-400 line-clamp-3 leading-relaxed">
                                {post.content.replace(/[#*`>]/g, '').slice(0, 150)}...
                            </p>
                            <div class="mt-6 flex items-center justify-between text-xs text-neutral-500 dark:text-neutral-400">
                                <span>{new Date(post.created_at).toLocaleDateString(undefined, {month: 'long', day: 'numeric', year: 'numeric'})}</span>
                                <Link href={`/posts/${post.slug}`} class="font-bold text-amber-600 dark:text-amber-500">Read More</Link>
                            </div>
                        </div>
                    </article>
                {/each}
            </div>
        </div>
    </section>
</PublicLayout>
