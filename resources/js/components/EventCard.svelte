<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import Calendar from 'lucide-svelte/icons/calendar';
    import ChevronRight from 'lucide-svelte/icons/chevron-right';
    import Clock from 'lucide-svelte/icons/clock';
    import MapPin from 'lucide-svelte/icons/map-pin';

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
    import type { Event } from '@/types';

    let { event }: { event: Event } = $props();

    function formatDate(dateString: string | null): string {
        if (!dateString) {
            return '';
        }

        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    }

    function formatDateRange(start: string | null, end: string | null): string {
        if (!start) {
            return '';
        }

        const startFormatted = formatDate(start);

        if (!end || start === end) {
            return startFormatted;
        }

        return `${startFormatted} – ${formatDate(end)}`;
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

    let isPast = $derived(
        event.end_date ? new Date(event.end_date) < new Date() : false,
    );
</script>

<Card
    class="group overflow-hidden transition hover:shadow-lg {isPast
        ? 'opacity-75'
        : ''}"
>
    <div class="aspect-video overflow-hidden bg-muted relative">
        {#if event.image_url}
            <img
                src={resolveImageUrl(event.image_url)}
                alt={event.title}
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
            />
        {:else}
            <div
                class="flex h-full items-center justify-center bg-gradient-to-br from-primary/10 to-primary/5"
            >
                <Calendar class="h-12 w-12 text-primary/40" />
            </div>
        {/if}
        {#if isPast}
            <div class="absolute top-2 right-2">
                <Badge variant="secondary" class="bg-muted/90">Past Event</Badge
                >
            </div>
        {/if}
    </div>
    <CardHeader class="pb-3">
        <div class="flex flex-wrap items-center gap-2 mb-2">
            {#if event.department}
                <Badge variant="outline">{event.department.name}</Badge>
            {/if}
            {#each event.tags ?? [] as tag (tag.id)}
                <Badge variant="secondary">{tag.name}</Badge>
            {/each}
        </div>
        <CardTitle class="line-clamp-2 text-lg leading-snug"
            >{event.title}</CardTitle
        >
    </CardHeader>
    <CardContent class="space-y-2 pb-3">
        <!-- Date -->
        {#if event.start_date}
            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                <Clock class="h-4 w-4 shrink-0" />
                <span>{formatDateRange(event.start_date, event.end_date)}</span>
            </div>
        {/if}

        <!-- Location -->
        {#if event.location}
            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                <MapPin class="h-4 w-4 shrink-0" />
                <span class="line-clamp-1">{event.location}</span>
            </div>
        {/if}

        <!-- Description -->
        <CardDescription class="line-clamp-2 pt-1">
            {stripHtml(event.content)}
        </CardDescription>
    </CardContent>
    <CardFooter>
        <Button variant="ghost" size="sm" class="px-0 text-primary" asChild>
            <Link href={`/posts/${event.id}`}>
                View details
                <ChevronRight class="ml-1 h-4 w-4" />
            </Link>
        </Button>
    </CardFooter>
</Card>
