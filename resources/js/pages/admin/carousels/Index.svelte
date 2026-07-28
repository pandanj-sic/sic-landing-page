
<script lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Carousels', href: '/admin/carousels' },
        ],
    };
    import AppHead from '@/components/AppHead.svelte';
    import { Link, router } from '@inertiajs/svelte';
    import { untrack } from 'svelte';
    import {
        Trash,
        SquarePen,
        Link as LinkIcon,
        Search,
        RefreshCw,
        Plus,
        ChevronLeft,
        ChevronRight,
        Play,
        Pause,
        LayoutGrid,
        Layers,
        ExternalLink,
        Sparkles,
        Monitor,
        CheckCircle2,
        Image as ImageIcon
    } from 'lucide-svelte';

    let {
        carouselItems = [],
        departments = [],
    }: {
        carouselItems?: any[];
        departments?: any[];
    } = $props();

    // Filters & View state
    let searchQuery = $state('');
    let filterDept = $state('');
    let viewMode = $state<'canva' | 'table'>('canva');

    // Slide Player & Selection state
    let selectedIndex = $state(0);
    let isPlaying = $state(false);
    let intervalId: any = null;

    function deleteSlide(id: number) {
        if (confirm('Are you sure you want to delete this slide?')) {
            router.delete(`/admin/carousels/${id}`);
        }
    }

    function resetFilters() {
        searchQuery = '';
        filterDept = '';
        selectedIndex = 0;
    }

    // Filter computation
    const filteredSlides = $derived.by(() => {
        let result = [...carouselItems];

        // Search text
        if (searchQuery.trim()) {
            const q = searchQuery.toLowerCase();
            result = result.filter(item =>
                (item.title && item.title.toLowerCase().includes(q)) ||
                (item.subtitle && item.subtitle.toLowerCase().includes(q)) ||
                (item.link_url && item.link_url.toLowerCase().includes(q))
            );
        }

        // Department filter
        if (filterDept) {
            if (filterDept === 'main') {
                result = result.filter(item => item.department_id === null || item.department_id === undefined);
            } else {
                result = result.filter(item => String(item.department_id) === filterDept);
            }
        }

        // Always sort by order
        result.sort((a, b) => (a.order || 0) - (b.order || 0));

        return result;
    });

    // Active Slide derived state
    const activeSlide = $derived.by(() => {
        if (filteredSlides.length === 0) return null;
        const validIndex = Math.min(selectedIndex, filteredSlides.length - 1);
        return filteredSlides[validIndex] || filteredSlides[0] || null;
    });

    function nextSlide() {
        if (filteredSlides.length === 0) return;
        selectedIndex = (selectedIndex + 1) % filteredSlides.length;
    }

    function prevSlide() {
        if (filteredSlides.length === 0) return;
        selectedIndex = (selectedIndex - 1 + filteredSlides.length) % filteredSlides.length;
    }

    function togglePlay() {
        isPlaying = !isPlaying;
    }

    $effect(() => {
        if (isPlaying && filteredSlides.length > 1) {
            intervalId = setInterval(() => {
                untrack(() => nextSlide());
            }, 3500);
        } else {
            if (intervalId) clearInterval(intervalId);
        }

        return () => {
            if (intervalId) clearInterval(intervalId);
        };
    });

    // Ensure selectedIndex remains in valid range when filteredSlides change
    $effect(() => {
        if (selectedIndex >= filteredSlides.length && filteredSlides.length > 0) {
            untrack(() => {
                selectedIndex = 0;
            });
        }
    });
</script>

<AppHead title="Carousel Studio" />

<div class="flex h-full w-full flex-col gap-6 p-4 md:p-6 max-w-7xl mx-auto">
    <!-- Canva Studio Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-neutral-200/80 pb-4 dark:border-neutral-800">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400 border border-amber-500/20 shadow-sm">
                <Sparkles class="h-5 w-5" />
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl md:text-2xl font-bold tracking-tight text-neutral-900 dark:text-white font-serif">
                        Carousel Studio
                    </h1>
                    <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-500/20">
                        Canva Preview Mode
                    </span>
                </div>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                    Live interactive slide preview on top with Canva-style filmstrip deck at the bottom.
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- View Mode Switcher -->
            <div class="flex items-center rounded-lg bg-neutral-100 dark:bg-neutral-900 p-1 border border-neutral-200 dark:border-neutral-800 text-xs">
                <button
                    type="button"
                    onclick={() => viewMode = 'canva'}
                    class="flex items-center gap-1.5 rounded-md px-3 py-1.5 font-medium transition-all {viewMode === 'canva' ? 'bg-white text-amber-600 shadow-sm dark:bg-neutral-800 dark:text-amber-400' : 'text-neutral-500 hover:text-neutral-900 dark:hover:text-neutral-200'}"
                >
                    <Layers class="h-3.5 w-3.5" /> Canva Studio
                </button>
                <button
                    type="button"
                    onclick={() => viewMode = 'table'}
                    class="flex items-center gap-1.5 rounded-md px-3 py-1.5 font-medium transition-all {viewMode === 'table' ? 'bg-white text-amber-600 shadow-sm dark:bg-neutral-800 dark:text-amber-400' : 'text-neutral-500 hover:text-neutral-900 dark:hover:text-neutral-200'}"
                >
                    <LayoutGrid class="h-3.5 w-3.5" /> Table List
                </button>
            </div>

            <Link
                href="/admin/carousels/create"
                class="inline-flex items-center gap-2 rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-amber-600/20 hover:bg-amber-500 transition-all active:scale-95"
            >
                <Plus class="h-4 w-4" /> Add New Slide
            </Link>
        </div>
    </div>

    <!-- Toolbar & Filters -->
    <div class="flex flex-col md:flex-row gap-3 items-center justify-between rounded-2xl border border-neutral-200/80 bg-white p-3 shadow-sm dark:border-neutral-800/80 dark:bg-[#161615]">
        <div class="relative w-full md:w-80">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-neutral-400" />
            <input
                type="text"
                placeholder="Search slide titles or content..."
                bind:value={searchQuery}
                class="w-full rounded-xl border border-neutral-200 pl-9 pr-4 py-1.5 text-xs outline-none focus:border-amber-500 dark:border-neutral-800 dark:bg-[#0a0a0a]"
            />
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto justify-end">
            <!-- Filter by location -->
            <select
                bind:value={filterDept}
                class="rounded-xl border border-neutral-200 px-3 py-1.5 text-xs outline-none focus:border-amber-500 dark:border-neutral-800 dark:bg-[#0a0a0a] text-neutral-700 dark:text-neutral-200 font-medium"
            >
                <option value="">All Display Locations ({carouselItems.length})</option>
                <option value="main">Main Page Only</option>
                {#each departments as dept}
                    <option value={String(dept.id)}>{dept.name} Page</option>
                {/each}
            </select>

            <button
                type="button"
                onclick={resetFilters}
                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-xl border border-neutral-200 hover:bg-neutral-50 dark:border-neutral-800 dark:hover:bg-neutral-900 text-neutral-500 transition-colors"
                title="Reset filters"
            >
                <RefreshCw class="h-3 w-3" /> Reset
            </button>
        </div>
    </div>

    {#if viewMode === 'canva'}
        <!-- Canva Layout: Preview on Top, Slide Deck Filmstrip at Bottom -->
        <div class="flex flex-col gap-6">
            <!-- TOP SECTION: Canva Stage / Live Hero Preview -->
            <div class="relative flex flex-col overflow-hidden rounded-2xl border border-neutral-800 bg-neutral-950 shadow-2xl">
                <!-- Canva Stage Top Bar -->
                <div class="flex items-center justify-between border-b border-neutral-800/80 bg-neutral-900/80 px-4 py-2.5 backdrop-blur-md text-xs">
                    <div class="flex items-center gap-2 text-neutral-400 font-medium">
                        <Monitor class="h-4 w-4 text-amber-500" />
                        <span>Live Viewport Preview</span>
                        <span class="text-neutral-600 dark:text-neutral-700">•</span>
                        <span class="text-neutral-300 font-mono text-[11px]">1920 × 600 Hero Ratio</span>
                    </div>

                    <!-- Slide Counter & Controls -->
                    <div class="flex items-center gap-3">
                        {#if filteredSlides.length > 0}
                            <span class="text-xs font-mono font-semibold text-neutral-300 bg-neutral-800 px-2.5 py-0.5 rounded-full border border-neutral-700">
                                Slide {selectedIndex + 1} of {filteredSlides.length}
                            </span>
                            <button
                                type="button"
                                onclick={togglePlay}
                                class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-semibold transition-colors {isPlaying ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-neutral-800 text-neutral-300 hover:bg-neutral-700'}"
                                title={isPlaying ? 'Pause slideshow' : 'Play slideshow preview'}
                            >
                                {#if isPlaying}
                                    <Pause class="h-3.5 w-3.5 text-amber-400" />
                                    <span>Pause</span>
                                {:else}
                                    <Play class="h-3.5 w-3.5 text-neutral-300" />
                                    <span>Auto-Play</span>
                                {/if}
                            </button>
                        {/if}
                    </div>
                </div>

                <!-- Preview Viewport Canvas -->
                <div class="relative h-[380px] md:h-[460px] w-full overflow-hidden bg-neutral-900 flex items-center justify-center">
                    {#if activeSlide}
                        <!-- Background Slide Image with Gradient Overlay -->
                        <div
                            class="absolute inset-0 transition-all duration-700 ease-in-out"
                            style="background-image: linear-gradient(to right, rgba(0, 0, 0, 0.75) 0%, rgba(0, 0, 0, 0.4) 60%, rgba(0, 0, 0, 0.6) 100%), url('{activeSlide.image_path}'); background-size: cover; background-position: center;"
                        >
                            <!-- Stage Content Overlay -->
                            <div class="mx-auto flex h-full max-w-5xl flex-col justify-center px-8 md:px-12 text-white">
                                <div class="max-w-2xl space-y-3">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        {#if activeSlide.department}
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/20 px-3 py-1 text-xs font-semibold text-amber-300 border border-amber-500/30 backdrop-blur-md">
                                                <CheckCircle2 class="h-3.5 w-3.5" />
                                                {activeSlide.department.name} Page
                                            </span>
                                        {/if}
                                        {#if activeSlide.show_on_main}
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-semibold text-emerald-300 border border-emerald-500/30 backdrop-blur-md">
                                                <CheckCircle2 class="h-3.5 w-3.5" />
                                                Main Page
                                            </span>
                                        {/if}
                                    </div>

                                    <h2 class="text-2xl md:text-4xl font-extrabold tracking-tight text-white leading-tight font-serif">
                                        {activeSlide.title || 'Untitled Slide'}
                                    </h2>

                                    {#if activeSlide.subtitle}
                                        <p class="text-sm md:text-base text-neutral-200 line-clamp-3 font-normal leading-relaxed">
                                            {activeSlide.subtitle}
                                        </p>
                                    {/if}

                                    {#if activeSlide.link_url}
                                        <div class="pt-2">
                                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-amber-600 px-4 py-2 text-xs font-semibold text-white shadow-md shadow-amber-600/30">
                                                Explore More <ExternalLink class="h-3.5 w-3.5" />
                                            </span>
                                        </div>
                                    {/if}
                                </div>
                            </div>
                        </div>

                        <!-- On-Stage Navigation Arrows -->
                        <button
                            type="button"
                            onclick={prevSlide}
                            class="absolute left-4 top-1/2 z-20 -translate-y-1/2 rounded-full bg-black/40 p-3 text-white backdrop-blur-md transition-transform hover:scale-110 hover:bg-black/70 border border-white/10"
                            aria-label="Previous slide"
                        >
                            <ChevronLeft class="h-5 w-5" />
                        </button>
                        <button
                            type="button"
                            onclick={nextSlide}
                            class="absolute right-4 top-1/2 z-20 -translate-y-1/2 rounded-full bg-black/40 p-3 text-white backdrop-blur-md transition-transform hover:scale-110 hover:bg-black/70 border border-white/10"
                            aria-label="Next slide"
                        >
                            <ChevronRight class="h-5 w-5" />
                        </button>

                        <!-- Canva Stage Quick Action Bar -->
                        <div class="absolute bottom-4 right-4 z-20 flex items-center gap-2 rounded-xl bg-black/60 p-1.5 backdrop-blur-md border border-white/10">
                            <a
                                href={`/admin/carousels/${activeSlide.id}/edit`}
                                class="inline-flex items-center gap-1.5 rounded-lg bg-neutral-800/90 px-3 py-1.5 text-xs font-semibold text-neutral-200 hover:bg-neutral-700 hover:text-white transition-colors"
                            >
                                <SquarePen class="h-3.5 w-3.5 text-amber-400" /> Edit Slide
                            </a>
                            <button
                                type="button"
                                onclick={() => deleteSlide(activeSlide.id)}
                                class="inline-flex items-center gap-1.5 rounded-lg bg-red-950/40 px-3 py-1.5 text-xs font-semibold text-red-400 hover:bg-red-900/60 hover:text-red-200 transition-colors border border-red-500/20"
                            >
                                <Trash class="h-3.5 w-3.5" /> Delete
                            </button>
                        </div>
                    {:else}
                        <!-- Empty State Viewport -->
                        <div class="flex flex-col items-center justify-center p-8 text-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-neutral-800 text-neutral-500 border border-neutral-700">
                                <ImageIcon class="h-6 w-6" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-base font-semibold text-neutral-200">No Carousel Slides Available</span>
                                <span class="text-xs text-neutral-400">There are no slides matching your search or location filter.</span>
                            </div>
                            <Link
                                href="/admin/carousels/create"
                                class="mt-2 inline-flex items-center gap-1.5 rounded-xl bg-amber-600 px-4 py-2 text-xs font-semibold text-white shadow-md hover:bg-amber-500"
                            >
                                <Plus class="h-4 w-4" /> Create First Slide
                            </Link>
                        </div>
                    {/if}
                </div>
            </div>

            <!-- BOTTOM SECTION: Canva Filmstrip Slide Deck -->
            <div class="flex flex-col gap-3 rounded-2xl border border-neutral-200/80 bg-white p-4 shadow-sm dark:border-neutral-800/80 dark:bg-[#161615]">
                <div class="flex items-center justify-between border-b border-neutral-200/80 pb-2.5 dark:border-neutral-800">
                    <div class="flex items-center gap-2">
                        <Layers class="h-4 w-4 text-amber-500" />
                        <span class="text-sm font-bold text-neutral-900 dark:text-white">Slides Deck</span>
                        <span class="text-xs text-neutral-400 font-mono">({filteredSlides.length} slides)</span>
                    </div>
                    <span class="text-[11px] text-neutral-400">Click a slide thumbnail to preview</span>
                </div>

                <!-- Filmstrip Horizontal Cards Slider -->
                <div class="flex items-center gap-3 overflow-x-auto pb-2 pt-1 scrollbar-thin scrollbar-thumb-neutral-300 dark:scrollbar-thumb-neutral-700">
                    {#each filteredSlides as slide, idx}
                        {@const isSelected = idx === selectedIndex}
                        <div
                            role="button"
                            tabindex="0"
                            onclick={() => selectedIndex = idx}
                            onkeydown={(e) => { if (e.key === 'Enter' || e.key === ' ') selectedIndex = idx; }}
                            class="group relative flex flex-col flex-shrink-0 w-52 overflow-hidden rounded-xl border-2 transition-all cursor-pointer text-left {isSelected ? 'border-amber-500 shadow-lg shadow-amber-500/10 ring-2 ring-amber-500/20 bg-amber-500/5' : 'border-neutral-200 dark:border-neutral-800 hover:border-amber-400/50 bg-neutral-50 dark:bg-neutral-900/60'}"
                        >
                            <!-- Thumbnail Image Area -->
                            <div class="relative h-28 w-full overflow-hidden bg-neutral-900">
                                <img
                                    src={slide.image_path}
                                    alt={slide.title || 'Slide thumbnail'}
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                />

                                <!-- Index Badge -->
                                <span class="absolute left-2 top-2 z-10 flex h-6 w-6 items-center justify-center rounded-lg bg-black/70 text-[11px] font-bold font-mono text-white backdrop-blur-md border border-white/10">
                                    {idx + 1}
                                </span>

                                <!-- Quick Hover Action Bar -->
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2 backdrop-blur-[2px]">
                                    <a
                                        href={`/admin/carousels/${slide.id}/edit`}
                                        onclick={(e) => e.stopPropagation()}
                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-neutral-900/90 text-amber-400 hover:bg-neutral-800 hover:scale-110 transition-all shadow-md"
                                        title="Edit Slide"
                                    >
                                        <SquarePen class="h-4 w-4" />
                                    </a>
                                    <button
                                        type="button"
                                        onclick={(e) => { e.stopPropagation(); deleteSlide(slide.id); }}
                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-950/90 text-red-400 hover:bg-red-900 hover:scale-110 transition-all shadow-md"
                                        title="Delete Slide"
                                    >
                                        <Trash class="h-4 w-4" />
                                    </button>
                                </div>
                            </div>

                            <!-- Thumbnail Details Footer -->
                            <div class="flex flex-col p-2.5 gap-1">
                                <span class="text-xs font-semibold text-neutral-900 dark:text-white line-clamp-1">
                                    {slide.title || 'Untitled Slide'}
                                </span>

                                <div class="flex items-center justify-between text-[10px] text-neutral-400 font-medium">
                                    <span>Order: #{slide.order}</span>
                                    <div class="flex items-center gap-1 truncate">
                                        {#if slide.department}
                                            <span class="text-amber-600 dark:text-amber-400 font-semibold truncate">{slide.department.name}</span>
                                        {/if}
                                        {#if slide.show_on_main}
                                            <span class="rounded bg-emerald-100 px-1 text-[9px] font-bold text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">Main</span>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/each}

                    <!-- Canva Add Slide Card -->
                    <Link
                        href="/admin/carousels/create"
                        class="flex flex-col items-center justify-center flex-shrink-0 w-52 h-44 rounded-xl border-2 border-dashed border-neutral-300 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-900/30 hover:border-amber-500 hover:bg-amber-500/5 transition-all text-neutral-400 hover:text-amber-500 gap-2 text-center p-4 cursor-pointer"
                    >
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-500 shadow-sm">
                            <Plus class="h-5 w-5" />
                        </div>
                        <span class="text-xs font-semibold">Add New Slide</span>
                    </Link>
                </div>
            </div>
        </div>
    {:else}
        <!-- Traditional Table List View -->
        <div class="rounded-2xl border border-neutral-200/80 bg-white shadow-sm dark:border-neutral-800/80 dark:bg-[#161615] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-neutral-200 bg-neutral-50/50 text-xs font-bold text-neutral-500 dark:border-neutral-800 dark:bg-[#0a0a0a]">
                            <th class="px-6 py-4 w-28">Preview</th>
                            <th class="px-6 py-4">Slide Details</th>
                            <th class="px-6 py-4">Display Location</th>
                            <th class="px-6 py-4 w-24">Order</th>
                            <th class="px-6 py-4 text-right w-24">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 text-sm text-neutral-600 dark:divide-neutral-800 dark:text-neutral-350">
                        {#each filteredSlides as item}
                            <tr class="hover:bg-neutral-50/50 dark:hover:bg-neutral-900/20">
                                <td class="px-6 py-4">
                                    <div class="h-12 w-20 overflow-hidden rounded-lg border border-neutral-250 bg-neutral-100 dark:border-neutral-800">
                                        <img src={item.image_path} alt={item.title} class="h-full w-full object-cover" />
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="max-w-md">
                                        <span class="block font-semibold text-neutral-900 dark:text-white leading-snug">
                                            {item.title || 'Untitled Slide'}
                                        </span>
                                        {#if item.subtitle}
                                            <span class="block text-xs text-neutral-400 mt-0.5 line-clamp-1">{item.subtitle}</span>
                                        {/if}
                                        {#if item.link_url}
                                            <span class="inline-flex items-center gap-1 text-[10px] text-neutral-400 mt-1 font-mono">
                                                <LinkIcon class="h-2.5 w-2.5" /> {item.link_url}
                                            </span>
                                        {/if}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    {#if item.department}
                                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-950/30 dark:text-amber-400">
                                            {item.department.name} Page
                                        </span>
                                    {:else}
                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-950/30 dark:text-blue-400">
                                            Main Page
                                        </span>
                                    {/if}
                                </td>
                                <td class="px-6 py-4 font-mono font-bold text-xs text-neutral-850 dark:text-neutral-200">
                                    #{item.order}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a
                                            href={`/admin/carousels/${item.id}/edit`}
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-neutral-400 hover:bg-neutral-100 hover:text-neutral-900 dark:hover:bg-neutral-800 dark:hover:text-white"
                                            title="Edit Slide"
                                        >
                                            <SquarePen class="h-4 w-4" />
                                        </a>
                                        <button
                                            onclick={() => deleteSlide(item.id)}
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-neutral-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/30"
                                            title="Delete Slide"
                                        >
                                            <Trash class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        {:else}
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-neutral-400 italic">
                                    No carousel slides match your criteria.
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
        </div>
    {/if}
</div>
