<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import ArrowLeft from 'lucide-svelte/icons/arrow-left';
    import Calendar from 'lucide-svelte/icons/calendar';
    import MapPin from 'lucide-svelte/icons/map-pin';

    import AppHead from '@/components/AppHead.svelte';
    import PostCard from '@/components/PostCard.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Separator } from '@/components/ui/separator';
    import PageLayout from '@/layouts/PageLayout.svelte';
    import type { Event, Post } from '@/types';

    let {
        post,
        relatedPosts = [],
    }: {
        post: Event;
        relatedPosts: Post[];
    } = $props();

    function formatDate(dateString: string): string {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    }

    function resolveImageUrl(url: string | null): string | null {
        if (!url) {
            return null;
        }

        return url.startsWith('http') ? url : `/storage/public/${url}`;
    }

    let typeLabel = $derived(
        { article: 'Article', announcement: 'Announcement', event: 'Event' }[
            post.type
        ] ?? post.type,
    );

    let isEvent = $derived(post.type === 'event');
</script>

<AppHead title={post.title} />

<PageLayout>
    <article class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <!-- Back link -->
        <div class="mb-8">
            <Button
                variant="ghost"
                size="sm"
                class="gap-2 px-0 text-muted-foreground hover:text-foreground"
            >
                <Link href="/posts" class="flex items-center gap-2">
                    <ArrowLeft class="h-4 w-4" />
                    Back to posts
                </Link>
            </Button>
        </div>

        <!-- Post Header -->
        <header class="mb-8">
            <div class="mb-4 flex flex-wrap items-center gap-2">
                <Badge variant="default">{typeLabel}</Badge>
                {#if post.department}
                    <Badge variant="outline">{post.department.name}</Badge>
                {/if}
                {#each post.tags ?? [] as tag (tag.id)}
                    <Badge variant="secondary">{tag.name}</Badge>
                {/each}
            </div>

            <h1
                class="text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl"
            >
                {post.title}
            </h1>

            <div
                class="mt-4 flex flex-wrap items-center gap-4 text-sm text-muted-foreground"
            >
                <span class="flex items-center gap-1.5">
                    <Calendar class="h-4 w-4" />
                    {formatDate(post.created_at)}
                </span>

                {#if isEvent && post.start_date}
                    <span class="flex items-center gap-1.5">
                        <Calendar class="h-4 w-4" />
                        <span>
                            {formatDate(post.start_date)}
                            {#if post.end_date && post.end_date !== post.start_date}
                                – {formatDate(post.end_date)}
                            {/if}
                        </span>
                    </span>
                {/if}

                {#if isEvent && post.location}
                    <span class="flex items-center gap-1.5">
                        <MapPin class="h-4 w-4" />
                        {post.location}
                    </span>
                {/if}
            </div>
        </header>

        <!-- Featured Image -->
        {#if post.image_url}
            <div class="mb-10 overflow-hidden rounded-xl bg-muted">
                <img
                    src={resolveImageUrl(post.image_url)}
                    alt={post.title}
                    class="h-auto w-full object-cover"
                />
            </div>
        {/if}

        <!-- Post Content -->
        <div class="prose prose-lg dark:prose-invert max-w-none">
            {@html post.content}
        </div>

        <!-- Tags footer -->
        {#if post.tags && post.tags.length > 0}
            <Separator class="my-10" />
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-medium text-muted-foreground"
                    >Tags:</span
                >
                {#each post.tags as tag (tag.id)}
                    <Badge variant="secondary">{tag.name}</Badge>
                {/each}
            </div>
        {/if}
    </article>

    <!-- Related Posts -->
    {#if relatedPosts.length > 0}
        <Separator class="mx-auto max-w-7xl" />

        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <h2 class="mb-8 text-2xl font-bold tracking-tight">
                Related Posts
            </h2>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                {#each relatedPosts as relatedPost (relatedPost.id)}
                    <PostCard post={relatedPost} />
                {/each}
            </div>
        </section>
    {/if}
</PageLayout>
