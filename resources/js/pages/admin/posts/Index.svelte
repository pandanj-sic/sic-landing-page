<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Posts', href: '/admin/posts' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { Link, router } from '@inertiajs/svelte';
    import { PlusCircle, Edit, Trash, Calendar, Megaphone, FileText, Search, ArrowUpDown, RefreshCw, Star } from 'lucide-svelte';

    let {
        posts = [],
        departments = [],
    }: {
        posts?: any[];
        departments?: any[];
    } = $props();

    // Filters & Sorting state
    let searchQuery = $state('');
    let filterType = $state('');
    let filterDept = $state('');
    let filterFeatured = $state('');
    let sortField = $state('created_at');
    let sortDirection = $state<'asc' | 'desc'>('desc');

    function deletePost(id: number) {
        if (confirm('Are you sure you want to delete this post? This cannot be undone.')) {
            router.delete(`/admin/posts/${id}`);
        }
    }

    function toggleSort(field: string) {
        if (sortField === field) {
            sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            sortField = field;
            sortDirection = 'desc';
        }
    }

    function resetFilters() {
        searchQuery = '';
        filterType = '';
        filterDept = '';
        filterFeatured = '';
        sortField = 'created_at';
        sortDirection = 'desc';
    }

    // Filter & Sort calculation
    const filteredPosts = $derived.by(() => {
        let result = [...posts];

        // Search text
        if (searchQuery.trim()) {
            const q = searchQuery.toLowerCase();
            result = result.filter(post => 
                post.title.toLowerCase().includes(q) ||
                (post.content && post.content.toLowerCase().includes(q))
            );
        }

        // Type filter
        if (filterType) {
            result = result.filter(post => post.type === filterType);
        }

        // Department filter
        if (filterDept) {
            result = result.filter(post => String(post.department_id) === filterDept);
        }

        // Featured filter
        if (filterFeatured) {
            const isFeaturedVal = filterFeatured === 'yes';
            result = result.filter(post => post.is_featured === isFeaturedVal);
        }

        // Sorting
        result.sort((a, b) => {
            let valA = a[sortField];
            let valB = b[sortField];

            if (sortField === 'department') {
                valA = a.department?.name || '';
                valB = b.department?.name || '';
            }

            if (valA === undefined || valA === null) valA = '';
            if (valB === undefined || valB === null) valB = '';

            if (typeof valA === 'string') {
                return sortDirection === 'asc' 
                    ? valA.localeCompare(valB) 
                    : valB.localeCompare(valA);
            }

            if (valA < valB) return sortDirection === 'asc' ? -1 : 1;
            if (valA > valB) return sortDirection === 'asc' ? 1 : -1;
            return 0;
        });

        return result;
    });
</script>

<AppHead title="Manage Posts" />

<div class="p-6 max-w-7xl mx-auto w-full space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-white font-serif">Posts Management</h1>
            <p class="text-xs text-neutral-500 mt-1">Create, edit, or delete articles, announcements, and campus events.</p>
        </div>
        <Link 
            href="/admin/posts/create"
            class="inline-flex items-center gap-1.5 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-amber-600/10 hover:bg-amber-700 self-start md:self-auto"
        >
            <PlusCircle class="h-4.5 w-4.5" /> Create Post
        </Link>
    </div>

    <!-- Filters Panel -->
    <div class="rounded-xl border border-neutral-100 bg-white p-4 shadow-sm dark:border-neutral-900 dark:bg-[#161615] flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="relative w-full md:max-w-xs">
            <Search class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-neutral-400" />
            <input 
                type="text" 
                placeholder="Search posts..." 
                bind:value={searchQuery}
                class="w-full rounded-xl border border-neutral-200 pl-10 pr-4 py-2 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
            />
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto items-stretch sm:items-center">
            <!-- Type filter -->
            <select 
                bind:value={filterType}
                class="rounded-xl border border-neutral-200 px-3 py-2 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
            >
                <option value="">All Types</option>
                <option value="article">Article</option>
                <option value="event">Event</option>
                <option value="announcement">Announcement</option>
            </select>

            <!-- Department filter -->
            <select 
                bind:value={filterDept}
                class="rounded-xl border border-neutral-200 px-3 py-2 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
            >
                <option value="">All Departments</option>
                {#each departments as dept}
                    <option value={String(dept.id)}>{dept.name}</option>
                {/each}
            </select>

            <!-- Featured filter -->
            <select 
                bind:value={filterFeatured}
                class="rounded-xl border border-neutral-200 px-3 py-2 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
            >
                <option value="">All Featured</option>
                <option value="yes">Featured Only</option>
                <option value="no">Non-Featured</option>
            </select>

            <!-- Reset Filters -->
            <button 
                onclick={resetFilters}
                class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg border border-neutral-200 hover:bg-neutral-50 dark:border-neutral-800 dark:hover:bg-neutral-950 text-neutral-500"
                title="Reset Filters"
            >
                <RefreshCw class="h-3 w-3" /> Reset
            </button>
        </div>
    </div>

    <!-- Tabular Posts Data -->
    <div class="rounded-xl border border-neutral-100 bg-white shadow-sm dark:border-neutral-900 dark:bg-[#161615] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-neutral-100 bg-neutral-50/50 text-xs font-bold text-neutral-500 dark:border-neutral-800 dark:bg-[#0a0a0a]">
                        <th class="px-6 py-4">
                            <button onclick={() => toggleSort('title')} class="flex items-center gap-1 hover:text-amber-600">
                                Post Info <ArrowUpDown class="h-3.5 w-3.5" />
                            </button>
                        </th>
                        <th class="px-6 py-4 w-28">
                            <button onclick={() => toggleSort('type')} class="flex items-center gap-1 hover:text-amber-600">
                                Type <ArrowUpDown class="h-3.5 w-3.5" />
                            </button>
                        </th>
                        <th class="px-6 py-4">
                            <button onclick={() => toggleSort('department')} class="flex items-center gap-1 hover:text-amber-600">
                                Department <ArrowUpDown class="h-3.5 w-3.5" />
                            </button>
                        </th>
                        <th class="px-6 py-4 w-28">
                            <button onclick={() => toggleSort('is_featured')} class="flex items-center gap-1 hover:text-amber-600">
                                Featured <ArrowUpDown class="h-3.5 w-3.5" />
                            </button>
                        </th>
                        <th class="px-6 py-4 w-36">Author</th>
                        <th class="px-6 py-4 text-right w-28">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 text-sm text-neutral-600 dark:divide-neutral-800 dark:text-neutral-350">
                    {#each filteredPosts as post}
                        <tr class="hover:bg-neutral-50/50 dark:hover:bg-neutral-900/20">
                            <!-- Title & Image -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    {#if post.highlight_image_path}
                                        <div class="h-10 w-16 overflow-hidden rounded-md border border-neutral-200 bg-neutral-100 shrink-0 dark:border-neutral-800">
                                            <img src={post.highlight_image_path} alt={post.title} class="h-full w-full object-cover" />
                                        </div>
                                    {:else}
                                        <div class="flex h-10 w-16 shrink-0 items-center justify-center rounded-md bg-amber-600/10 text-amber-600">
                                            {#if post.type === 'event'}
                                                <Calendar class="h-5 w-5" />
                                            {:else if post.type === 'announcement'}
                                                <Megaphone class="h-5 w-5" />
                                            {:else}
                                                <FileText class="h-5 w-5" />
                                            {/if}
                                        </div>
                                    {/if}
                                    <div class="min-w-0">
                                        <span class="block font-semibold text-neutral-900 dark:text-white truncate max-w-sm">
                                            {post.title}
                                        </span>
                                        <span class="block text-[11px] text-neutral-400 mt-0.5">
                                            Created {new Date(post.created_at).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' })}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <!-- Type -->
                            <td class="px-6 py-4">
                                <span class="capitalize font-semibold text-neutral-700 dark:text-neutral-300">{post.type}</span>
                            </td>
                            <!-- Department -->
                            <td class="px-6 py-4 text-neutral-550 dark:text-neutral-405">
                                {post.department ? post.department.name : 'General (All Departments)'}
                            </td>
                            <!-- Featured -->
                            <td class="px-6 py-4">
                                {#if post.is_featured}
                                    <span class="inline-flex items-center gap-1 rounded bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-950/30 dark:text-amber-400">
                                        <Star class="h-3 w-3 fill-current" /> Featured
                                    </span>
                                {:else}
                                    <span class="text-neutral-400">-</span>
                                {/if}
                            </td>
                            <!-- Author -->
                            <td class="px-6 py-4 text-neutral-500">
                                {post.user ? post.user.name : 'System'}
                            </td>
                            <!-- Actions -->
                            <td class="px-6 py-4 text-right flex justify-end gap-1.5">
                                <Link 
                                    href={`/admin/posts/${post.id}/edit`}
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800"
                                    title="Edit Post"
                                >
                                    <Edit class="h-4 w-4" />
                                </Link>
                                <button 
                                    onclick={() => deletePost(post.id)}
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-neutral-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/30"
                                    title="Delete Post"
                                >
                                    <Trash class="h-4 w-4" />
                                </button>
                            </td>
                        </tr>
                    {:else}
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-neutral-400 italic">
                                No posts match your filtering criteria.
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>
    </div>
</div>
