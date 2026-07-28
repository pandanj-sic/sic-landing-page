<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import PublicLayout from '@/components/PublicLayout.svelte';
    import { Link, router } from '@inertiajs/svelte';
    import { Search, Calendar, Megaphone, FileText } from 'lucide-svelte';
    import { untrack } from 'svelte';

    let {
        posts = [],
        departments = [],
        navigationLinks = [],
        filters = {},
    }: {
        posts?: any[];
        departments?: any[];
        navigationLinks?: any[];
        filters?: any;
    } = $props();

    let search = $state(untrack(() => filters.search || ''));
    let departmentId = $state(untrack(() => filters.department_id || ''));
    let selectedType = $state(untrack(() => filters.type || ''));
    let sortBy = $state('newest'); // newest | oldest | title

    function handleFilter() {
        router.get('/posts', {
            search,
            department_id: departmentId,
            type: selectedType
        }, {
            preserveState: true,
            replace: true
        });
    }

    function changeType(type: string) {
        selectedType = type;
        handleFilter();
    }

    const sortedPosts = $derived.by(() => {
        let result = [...posts];
        if (sortBy === 'newest') {
            result.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime());
        } else if (sortBy === 'oldest') {
            result.sort((a, b) => new Date(a.created_at).getTime() - new Date(b.created_at).getTime());
        } else if (sortBy === 'title') {
            result.sort((a, b) => a.title.localeCompare(b.title));
        }
        return result;
    });
</script>

<AppHead title="News & Events - San Isidro College" />

<PublicLayout {navigationLinks} {departments}>
    <div class="bg-neutral-50 py-12 dark:bg-[#161615]">
        <div class="mx-auto max-w-7xl px-6">
            <h1 class="text-4xl font-extrabold tracking-tight text-neutral-900 dark:text-white">News, Events & Announcements</h1>
            <p class="mt-2 text-neutral-500 dark:text-neutral-400">Keep up to date with the latest stories and official advisories from our campus.</p>
        </div>
    </div>

    <section class="py-12">
        <div class="mx-auto max-w-7xl px-6">
            <!-- Filter Bar -->
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <!-- Tabs for Post Types -->
                <div class="flex flex-wrap gap-2 border-b border-neutral-100 pb-2 md:border-none md:pb-0 dark:border-neutral-900">
                    <button 
                        onclick={() => changeType('')}
                        class="rounded-lg px-4 py-2 text-sm font-semibold transition-colors {selectedType === '' ? 'bg-amber-600 text-white' : 'text-neutral-600 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-neutral-900'}"
                    >
                        All Posts
                    </button>
                    <button 
                        onclick={() => changeType('article')}
                        class="rounded-lg px-4 py-2 text-sm font-semibold transition-colors {selectedType === 'article' ? 'bg-amber-600 text-white' : 'text-neutral-600 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-neutral-900'}"
                    >
                        Articles
                    </button>
                    <button 
                        onclick={() => changeType('event')}
                        class="rounded-lg px-4 py-2 text-sm font-semibold transition-colors {selectedType === 'event' ? 'bg-amber-600 text-white' : 'text-neutral-600 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-neutral-900'}"
                    >
                        Events
                    </button>
                    <button 
                        onclick={() => changeType('announcement')}
                        class="rounded-lg px-4 py-2 text-sm font-semibold transition-colors {selectedType === 'announcement' ? 'bg-amber-600 text-white' : 'text-neutral-600 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-neutral-900'}"
                    >
                        Announcements
                    </button>
                </div>

                <!-- Inputs -->
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                    <div class="relative w-full sm:w-64">
                        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-400" />
                        <input 
                            type="text" 
                            bind:value={search}
                            oninput={handleFilter}
                            placeholder="Search stories..." 
                            class="w-full rounded-xl border border-neutral-200 py-2 pl-9 pr-4 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#161615]"
                        />
                    </div>

                    <select 
                        bind:value={departmentId} 
                        onchange={handleFilter}
                        class="rounded-xl border border-neutral-200 px-4 py-2 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#161615]"
                    >
                        <option value="">All Departments</option>
                        {#each departments as dept}
                            <option value={dept.id}>{dept.name}</option>
                        {/each}
                    </select>

                    <select 
                        bind:value={sortBy} 
                        class="rounded-xl border border-neutral-200 px-4 py-2 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#161615]"
                    >
                        <option value="newest">Sort by: Newest</option>
                        <option value="oldest">Sort by: Oldest</option>
                        <option value="title">Sort by: Title (A-Z)</option>
                    </select>
                </div>
            </div>

            <!-- Posts Grid -->
            {#if sortedPosts.length > 0}
                <div class="mt-10 grid grid-cols-1 gap-8 md:grid-cols-3">
                    {#each sortedPosts as post}
                        <article class="flex flex-col overflow-hidden rounded-2xl border border-black/5 bg-white shadow-sm transition-all hover:shadow-md dark:border-white/5 dark:bg-[#161615]">
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
                                    {post.content.replace(/[#*`>]/g, '')}
                                </p>

                                {#if post.type === 'event' && post.start_date}
                                    <div class="mt-4 text-xs font-semibold text-amber-700 dark:text-amber-500 flex items-center gap-1">
                                        <Calendar class="h-3.5 w-3.5" />
                                        <span>
                                            {new Date(post.start_date).toLocaleDateString()}
                                            {#if post.end_date} - {new Date(post.end_date).toLocaleDateString()}{/if}
                                        </span>
                                    </div>
                                {/if}

                                <div class="mt-6 flex items-center justify-between border-t border-neutral-100 pt-4 text-xs text-neutral-500 dark:border-neutral-800 dark:text-neutral-400">
                                    <span>{new Date(post.created_at).toLocaleDateString()}</span>
                                    <Link href={`/posts/${post.slug}`} class="font-bold text-amber-600 dark:text-amber-500">Read Article</Link>
                                </div>
                            </div>
                        </article>
                    {/each}
                </div>
            {:else}
                <div class="mt-12 text-center py-20 bg-neutral-50 rounded-2xl border border-dashed border-neutral-200 dark:bg-[#161615] dark:border-neutral-800">
                    <p class="text-neutral-500">No posts found matching your search criteria.</p>
                </div>
            {/if}
        </div>
    </section>
</PublicLayout>
