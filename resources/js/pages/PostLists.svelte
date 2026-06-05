<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import ChevronLeft from 'lucide-svelte/icons/chevron-left';
    import ChevronRight from 'lucide-svelte/icons/chevron-right';
    import Newspaper from 'lucide-svelte/icons/newspaper';
    import Search from 'lucide-svelte/icons/search';
    import X from 'lucide-svelte/icons/x';

    import AppHead from '@/components/AppHead.svelte';
    import PostCard from '@/components/PostCard.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import {
        Select,
        SelectContent,
        SelectItem,
        SelectTrigger,
    } from '@/components/ui/select';
    import PageLayout from '@/layouts/PageLayout.svelte';
    import type { Post } from '@/types';

    type PaginatedPosts = {
        data: Post[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number | null;
        to: number | null;
        links: { url: string | null; label: string; active: boolean }[];
    };

    type FilterValues = {
        search?: string;
        type?: string;
        department?: string;
        tag?: string;
        sort?: string;
    };

    let {
        posts,
        departments = [],
        tags = [],
        filters = {} as FilterValues,
    }: {
        posts: PaginatedPosts;
        departments: { id: number; name: string }[];
        tags: { id: number; name: string }[];
        filters: FilterValues;
    } = $props();

    let searchValue = $state('');
    let typeValue = $state('');
    let departmentValue = $state('');
    let tagValue = $state('');
    let sortValue = $state('latest');

    $effect(() => {
        searchValue = filters.search ?? '';
        typeValue = filters.type ?? '';
        departmentValue = filters.department ?? '';
        tagValue = filters.tag ?? '';
        sortValue = filters.sort ?? 'latest';
    });

    let searchTimeout: ReturnType<typeof setTimeout>;

    function applyFilters(overrides: Record<string, string> = {}) {
        const params: Record<string, string> = {
            ...(searchValue && { search: searchValue }),
            ...(typeValue && { type: typeValue }),
            ...(departmentValue && { department: departmentValue }),
            ...(tagValue && { tag: tagValue }),
            ...(sortValue && sortValue !== 'latest' && { sort: sortValue }),
            ...overrides,
        };

        // Remove empty values from overrides
        for (const key of Object.keys(params)) {
            if (!params[key]) {
                delete params[key];
            }
        }

        router.get('/posts', params, {
            preserveState: true,
            preserveScroll: false,
        });
    }

    function handleSearch(event: Event) {
        const target = event.target as HTMLInputElement;
        searchValue = target.value;
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            applyFilters();
        }, 400);
    }

    function clearSearch() {
        searchValue = '';
        applyFilters({ search: '' });
    }

    function handleTypeChange(value: string) {
        typeValue = value;
        applyFilters({ type: value });
    }

    function handleDepartmentChange(value: string) {
        departmentValue = value;
        applyFilters({ department: value });
    }

    function handleTagChange(value: string) {
        tagValue = value;
        applyFilters({ tag: value });
    }

    function handleSortChange(value: string) {
        sortValue = value;
        applyFilters({ sort: value });
    }

    function clearAllFilters() {
        searchValue = '';
        typeValue = '';
        departmentValue = '';
        tagValue = '';
        sortValue = 'latest';
        router.get(
            '/posts',
            {},
            { preserveState: true, preserveScroll: false },
        );
    }

    function goToPage(url: string | null) {
        if (url) {
            router.get(url, {}, { preserveState: true, preserveScroll: false });
        }
    }

    let hasActiveFilters = $derived(
        !!searchValue ||
            !!typeValue ||
            !!departmentValue ||
            !!tagValue ||
            sortValue !== 'latest',
    );

    let typeLabel = $derived(
        typeValue
            ? ({
                  article: 'Articles',
                  announcement: 'Announcements',
                  event: 'Events',
              }[typeValue] ?? typeValue)
            : 'All Types',
    );

    let departmentLabel = $derived(
        departmentValue
            ? (departments.find((d) => String(d.id) === departmentValue)
                  ?.name ?? 'Department')
            : 'All Departments',
    );

    let tagLabel = $derived(
        tagValue
            ? (tags.find((t) => String(t.id) === tagValue)?.name ?? 'Tag')
            : 'All Tags',
    );

    let sortLabel = $derived(
        { latest: 'Latest', oldest: 'Oldest', title: 'Title A–Z' }[sortValue] ??
            'Latest',
    );
</script>

<AppHead title="Posts" />

<PageLayout>
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <Badge variant="secondary" class="mb-4">All Posts</Badge>
            <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                News & Updates
            </h1>
            <p class="mt-2 text-muted-foreground">
                Browse articles, announcements, and events from San Isidro
                College.
            </p>
        </div>

        <!-- Filters -->
        <div class="mb-8 space-y-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                <!-- Search -->
                <div class="relative flex-1">
                    <Search
                        class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        type="text"
                        placeholder="Search posts..."
                        class="pl-9 pr-9"
                        value={searchValue}
                        oninput={handleSearch}
                    />
                    {#if searchValue}
                        <button
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                            onclick={clearSearch}
                        >
                            <X class="h-4 w-4" />
                        </button>
                    {/if}
                </div>

                <!-- Sort -->
                <Select value={sortValue} onValueChange={handleSortChange}>
                    <SelectTrigger class="w-full sm:w-[160px]">
                        {sortLabel}
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="latest">Latest</SelectItem>
                        <SelectItem value="oldest">Oldest</SelectItem>
                        <SelectItem value="title">Title A–Z</SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div class="flex flex-wrap gap-3">
                <!-- Type Filter -->
                <Select value={typeValue} onValueChange={handleTypeChange}>
                    <SelectTrigger class="w-full sm:w-[170px]">
                        {typeLabel}
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">All Types</SelectItem>
                        <SelectItem value="article">Articles</SelectItem>
                        <SelectItem value="announcement"
                            >Announcements</SelectItem
                        >
                        <SelectItem value="event">Events</SelectItem>
                    </SelectContent>
                </Select>

                <!-- Department Filter -->
                {#if departments.length > 0}
                    <Select
                        value={departmentValue}
                        onValueChange={handleDepartmentChange}
                    >
                        <SelectTrigger class="w-full sm:w-[200px]">
                            {departmentLabel}
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">All Departments</SelectItem>
                            {#each departments as dept (dept.id)}
                                <SelectItem value={String(dept.id)}
                                    >{dept.name}</SelectItem
                                >
                            {/each}
                        </SelectContent>
                    </Select>
                {/if}

                <!-- Tag Filter -->
                {#if tags.length > 0}
                    <Select value={tagValue} onValueChange={handleTagChange}>
                        <SelectTrigger class="w-full sm:w-[170px]">
                            {tagLabel}
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">All Tags</SelectItem>
                            {#each tags as tag (tag.id)}
                                <SelectItem value={String(tag.id)}
                                    >{tag.name}</SelectItem
                                >
                            {/each}
                        </SelectContent>
                    </Select>
                {/if}

                <!-- Clear Filters -->
                {#if hasActiveFilters}
                    <Button
                        variant="ghost"
                        size="sm"
                        class="h-9"
                        onclick={clearAllFilters}
                    >
                        <X class="mr-1 h-4 w-4" />
                        Clear filters
                    </Button>
                {/if}
            </div>
        </div>

        <!-- Results summary -->
        {#if posts.total > 0}
            <p class="mb-6 text-sm text-muted-foreground">
                Showing {posts.from}–{posts.to} of {posts.total} post{posts.total !==
                1
                    ? 's'
                    : ''}
            </p>
        {/if}

        <!-- Posts Grid -->
        {#if posts.data.length > 0}
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                {#each posts.data as post (post.id)}
                    <PostCard {post} />
                {/each}
            </div>
        {:else}
            <Card class="py-16 text-center">
                <CardContent>
                    <Newspaper
                        class="mx-auto mb-4 h-12 w-12 text-muted-foreground/40"
                    />
                    <h3 class="text-lg font-semibold">No posts found</h3>
                    <p class="mt-1 text-sm text-muted-foreground">
                        {#if hasActiveFilters}
                            Try adjusting your filters or search term.
                        {:else}
                            Check back soon for new content!
                        {/if}
                    </p>
                    {#if hasActiveFilters}
                        <Button
                            variant="outline"
                            class="mt-4"
                            onclick={clearAllFilters}
                        >
                            Clear all filters
                        </Button>
                    {/if}
                </CardContent>
            </Card>
        {/if}

        <!-- Pagination -->
        {#if posts.last_page > 1}
            <nav
                class="mt-10 flex items-center justify-center gap-2"
                aria-label="Pagination"
            >
                <!-- Previous -->
                <Button
                    variant="outline"
                    size="sm"
                    disabled={posts.current_page === 1}
                    onclick={() => goToPage(posts.links[0]?.url)}
                >
                    <ChevronLeft class="mr-1 h-4 w-4" />
                    Previous
                </Button>

                <!-- Page numbers -->
                <div class="hidden items-center gap-1 sm:flex">
                    {#each posts.links.slice(1, -1) as link (link.label)}
                        {#if link.url}
                            <Button
                                variant={link.active ? 'default' : 'outline'}
                                size="sm"
                                class="min-w-9"
                                onclick={() => goToPage(link.url)}
                            >
                                {link.label}
                            </Button>
                        {:else}
                            <span class="px-2 text-sm text-muted-foreground"
                                >…</span
                            >
                        {/if}
                    {/each}
                </div>

                <!-- Mobile page indicator -->
                <span class="text-sm text-muted-foreground sm:hidden">
                    Page {posts.current_page} of {posts.last_page}
                </span>

                <!-- Next -->
                <Button
                    variant="outline"
                    size="sm"
                    disabled={posts.current_page === posts.last_page}
                    onclick={() =>
                        goToPage(posts.links[posts.links.length - 1]?.url)}
                >
                    Next
                    <ChevronRight class="ml-1 h-4 w-4" />
                </Button>
            </nav>
        {/if}
    </section>
</PageLayout>
