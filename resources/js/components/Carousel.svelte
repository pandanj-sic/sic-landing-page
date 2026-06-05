<script lang="ts">
    import ChevronLeft from 'lucide-svelte/icons/chevron-left';
    import ChevronRight from 'lucide-svelte/icons/chevron-right';
    import type { Snippet } from 'svelte';

    type Slide = {
        image: string;
        title: string;
        subtitle: string;
    };

    let {
        slides = [],
        interval = 5000,
        overlay,
    }: {
        slides: Slide[];
        interval?: number;
        overlay?: Snippet<[Slide, number]>;
    } = $props();

    let current = $state(0);
    let paused = $state(false);

    function next() {
        current = (current + 1) % slides.length;
    }

    function prev() {
        current = (current - 1 + slides.length) % slides.length;
    }

    function goTo(index: number) {
        current = index;
    }

    $effect(() => {
        if (paused || slides.length <= 1) {
            return;
        }

        const timer = setInterval(next, interval);

        return () => clearInterval(timer);
    });
</script>

<div
    class="relative w-full overflow-hidden rounded-xl"
    role="region"
    aria-label="Image carousel"
    onmouseenter={() => (paused = true)}
    onmouseleave={() => (paused = false)}
>
    <!-- Slides -->
    <div class="relative aspect-video w-full bg-muted">
        {#each slides as slide, i (slide.title)}
            <div
                class="absolute inset-0 transition-opacity duration-700 ease-in-out {i ===
                current
                    ? 'z-10 opacity-100'
                    : 'z-0 opacity-0'}"
                aria-hidden={i !== current}
            >
                <img
                    src={slide.image}
                    alt={slide.title}
                    class="h-full w-full object-cover"
                />
                <!-- Dark overlay for text readability -->
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"
                ></div>

                <!-- Text overlay -->
                <div
                    class="absolute inset-0 z-10 flex flex-col justify-end p-6 sm:p-10 md:p-14"
                >
                    {#if overlay}
                        {@render overlay(slide, i)}
                    {:else}
                        <h2
                            class="text-2xl font-bold text-white drop-shadow-lg sm:text-3xl md:text-4xl lg:text-5xl"
                        >
                            {slide.title}
                        </h2>
                        {#if slide.subtitle}
                            <p
                                class="mt-2 max-w-2xl text-sm text-white/90 drop-shadow sm:text-base md:text-lg"
                            >
                                {slide.subtitle}
                            </p>
                        {/if}
                    {/if}
                </div>
            </div>
        {/each}
    </div>

    <!-- Prev / Next buttons -->
    {#if slides.length > 1}
        <button
            onclick={prev}
            class="absolute top-1/2 left-3 z-20 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/20 text-white backdrop-blur-sm transition hover:bg-white/40 focus:ring-2 focus:ring-white focus:outline-none"
            aria-label="Previous slide"
        >
            <ChevronLeft class="h-5 w-5" />
        </button>
        <button
            onclick={next}
            class="absolute top-1/2 right-3 z-20 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/20 text-white backdrop-blur-sm transition hover:bg-white/40 focus:ring-2 focus:ring-white focus:outline-none"
            aria-label="Next slide"
        >
            <ChevronRight class="h-5 w-5" />
        </button>

        <!-- Dots -->
        <div
            class="absolute bottom-4 left-1/2 z-20 flex -translate-x-1/2 gap-2"
        >
            {#each slides as _, i (i)}
                <button
                    onclick={() => goTo(i)}
                    class="h-2.5 w-2.5 rounded-full transition-all {i ===
                    current
                        ? 'w-6 bg-white'
                        : 'bg-white/50 hover:bg-white/80'}"
                    aria-label="Go to slide {i + 1}"
                    aria-current={i === current ? 'true' : undefined}
                ></button>
            {/each}
        </div>
    {/if}
</div>
