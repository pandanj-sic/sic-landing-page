<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import Calendar from 'lucide-svelte/icons/calendar';
    import ChevronRight from 'lucide-svelte/icons/chevron-right';
    import Newspaper from 'lucide-svelte/icons/newspaper';

    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardDescription,
        CardFooter,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import type { Post } from '@/types';

    let { post }: { post: Post } = $props();

    function formatDate(dateString: string): string {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    }

    function stripHtml(html: string): string {
        const div = document.createElement('div');
        div.innerHTML = html;

        return div.textContent || div.innerText || '';
    }

    function resolveImageUrl(url: string | null): string | null {
        if (!url) {
            return null;
        }

        return url.startsWith('http') ? url : `/storage/public/${url}`;
    }
</script>

<Card class="group overflow-hidden transition hover:shadow-lg">
    <div class="aspect-video overflow-hidden bg-muted">
        {#if post.image_url}
            <img
                src={resolveImageUrl(post.image_url)}
                alt={post.title}
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
            />
        {:else}
            <div class="flex h-full items-center justify-center">
                <Newspaper class="h-12 w-12 text-muted-foreground/40" />
            </div>
        {/if}
    </div>
    <CardHeader>
        <div class="flex flex-wrap items-center gap-2">
            {#if post.department}
                <Badge variant="outline">{post.department.name}</Badge>
            {/if}
            {#each post.tags ?? [] as tag (tag.id)}
                <Badge variant="secondary">{tag.name}</Badge>
            {/each}
            <span class="flex items-center gap-1 text-xs text-muted-foreground">
                <Calendar class="h-3 w-3" />
                {formatDate(post.created_at)}
            </span>
        </div>
        <CardTitle class="line-clamp-2 text-lg leading-snug"
            >{post.title}</CardTitle
        >
    </CardHeader>
    <CardContent>
        <CardDescription class="line-clamp-3"
            >{stripHtml(post.content)}</CardDescription
        >
    </CardContent>
    <CardFooter>
        <Button variant="ghost" size="sm" class="px-0 text-primary" asChild>
            <Link href={`/posts/${post.id}`}>
                Read more
                <ChevronRight class="ml-1 h-4 w-4" />
            </Link>
        </Button>
    </CardFooter>
</Card>
