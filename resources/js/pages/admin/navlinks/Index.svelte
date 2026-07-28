<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Navigation Links', href: '/admin/navigation-links' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { Link, router } from '@inertiajs/svelte';
    import { Plus, Edit, Trash, Link2, Search, ArrowUpDown, RefreshCw, Eye, EyeOff, School, ChevronDown } from 'lucide-svelte';

    let {
        navigationLinks = [],
    }: {
        navigationLinks?: any[];
    } = $props();

    // Filters & Sorting state
    let searchQuery = $state('');
    let filterStatus = $state(''); // active | inactive
    let sortField = $state('order');
    let sortDirection = $state<'asc' | 'desc'>('asc');

    // Header Navbar preview data structure
    const activeTopLevelLinks = $derived.by(() => {
        const topLevel = navigationLinks.filter(l => !l.parent_id && l.is_active);
        topLevel.sort((a, b) => (a.order || 0) - (b.order || 0));
        return topLevel.map(parent => ({
            ...parent,
            children: navigationLinks
                .filter(c => String(c.parent_id) === String(parent.id) && c.is_active)
                .sort((a, b) => (a.order || 0) - (b.order || 0))
        }));
    });

    function deleteLink(id: number) {
        if (confirm('Are you sure you want to delete this navigation link?')) {
            router.delete(`/admin/navigation-links/${id}`);
        }
    }

    function toggleSort(field: string) {
        if (sortField === field) {
            sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            sortField = field;
            sortDirection = 'asc';
        }
    }

    function resetFilters() {
        searchQuery = '';
        filterStatus = '';
        sortField = 'order';
        sortDirection = 'asc';
    }

    // Filter & Sort calculation
    const filteredLinks = $derived.by(() => {
        let result = [...navigationLinks];

        // Search text
        if (searchQuery.trim()) {
            const q = searchQuery.toLowerCase();
            result = result.filter(link => 
                link.label.toLowerCase().includes(q) ||
                link.url.toLowerCase().includes(q)
            );
        }

        // Status filter
        if (filterStatus) {
            const isActiveVal = filterStatus === 'active';
            result = result.filter(link => link.is_active === isActiveVal);
        }

        // Sorting
        result.sort((a, b) => {
            let valA = a[sortField];
            let valB = b[sortField];

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

<AppHead title="Manage Navigation Links" />

<div class="p-6 max-w-7xl mx-auto w-full space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-white font-serif">Navigation Links</h1>
            <p class="text-xs text-neutral-500 mt-1">SuperAdmin Only • Customize links that appear in the public header navigation bar.</p>
        </div>
        <Link 
            href="/admin/navigation-links/create"
            class="inline-flex items-center gap-1.5 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-amber-600/10 hover:bg-amber-700 self-start md:self-auto"
        >
            <Plus class="h-4 w-4" /> Add Navigation Link
        </Link>
    </div>

    <!-- Live Header Preview Panel -->
    <div class="rounded-xl border border-neutral-100 bg-white p-4 shadow-sm dark:border-neutral-900 dark:bg-[#161615] space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-wider text-neutral-400">Header Navigation Live Preview</h3>
            <span class="text-[11px] text-neutral-400">Active header navigation layout</span>
        </div>

        <div class="rounded-xl border border-black/5 bg-neutral-50/80 p-4 dark:border-white/5 dark:bg-[#0a0a0a]">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-600 text-white shadow-sm">
                        <School class="h-4 w-4" />
                    </div>
                    <span class="font-bold text-xs text-amber-700 dark:text-amber-500">San Isidro College</span>
                </div>

                <nav class="flex flex-wrap items-center gap-6 text-xs">
                    {#each activeTopLevelLinks as link}
                        {#if link.children && link.children.length > 0}
                            <div class="relative group py-1">
                                <span class="flex items-center gap-1 font-medium text-neutral-600 hover:text-amber-600 dark:text-neutral-300 dark:hover:text-amber-500 cursor-pointer">
                                    {link.label} <ChevronDown class="h-3.5 w-3.5 transition-transform group-hover:rotate-180" />
                                </span>
                                <div class="absolute left-0 mt-1 w-48 rounded-lg border border-black/10 bg-white/95 p-1.5 shadow-xl backdrop-blur-md dark:border-white/10 dark:bg-[#161615]/95 hidden group-hover:block z-[9999]">
                                    {#each link.children as child}
                                        <div class="rounded px-3 py-1.5 text-xs text-neutral-700 hover:bg-neutral-50 dark:text-neutral-300 dark:hover:bg-neutral-800">
                                            {child.label}
                                        </div>
                                    {/each}
                                </div>
                            </div>
                        {:else}
                            <span class="font-medium text-neutral-600 hover:text-amber-600 dark:text-neutral-300 dark:hover:text-amber-500 py-1">
                                {link.label}
                            </span>
                        {/if}
                    {:else}
                        <span class="text-xs text-neutral-400 italic">No active navigation links.</span>
                    {/each}
                </nav>
            </div>
        </div>
    </div>

    <!-- Filters Panel -->
    <div class="rounded-xl border border-neutral-100 bg-white p-4 shadow-sm dark:border-neutral-900 dark:bg-[#161615] flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="relative w-full md:max-w-xs">
            <Search class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-neutral-400" />
            <input 
                type="text" 
                placeholder="Search link labels, URLs..." 
                bind:value={searchQuery}
                class="w-full rounded-xl border border-neutral-200 pl-10 pr-4 py-2 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
            />
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto items-stretch sm:items-center">
            <!-- Status filter -->
            <select 
                bind:value={filterStatus}
                class="rounded-xl border border-neutral-200 px-3 py-2 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
            >
                <option value="">All Statuses</option>
                <option value="active">Active (Visible)</option>
                <option value="inactive">Inactive (Hidden)</option>
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

    <!-- Navigation Links List Table -->
    <div class="rounded-xl border border-neutral-100 bg-white shadow-sm dark:border-neutral-900 dark:bg-[#161615] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-neutral-100 bg-neutral-50/50 text-xs font-bold text-neutral-500 dark:border-neutral-800 dark:bg-[#0a0a0a]">
                        <th class="px-6 py-4">
                            <button onclick={() => toggleSort('label')} class="flex items-center gap-1 hover:text-amber-600">
                                Navigation Label <ArrowUpDown class="h-3.5 w-3.5" />
                            </button>
                        </th>
                        <th class="px-6 py-4">
                            <button onclick={() => toggleSort('url')} class="flex items-center gap-1 hover:text-amber-600">
                                Target Route / URL <ArrowUpDown class="h-3.5 w-3.5" />
                            </button>
                        </th>
                        <th class="px-6 py-4">
                            <button onclick={() => toggleSort('parent')} class="flex items-center gap-1 hover:text-amber-600">
                                Parent Link <ArrowUpDown class="h-3.5 w-3.5" />
                            </button>
                        </th>
                        <th class="px-6 py-4 w-28">
                            <button onclick={() => toggleSort('order')} class="flex items-center gap-1 hover:text-amber-600">
                                Sort Order <ArrowUpDown class="h-3.5 w-3.5" />
                            </button>
                        </th>
                        <th class="px-6 py-4 w-28">
                            <button onclick={() => toggleSort('is_active')} class="flex items-center gap-1 hover:text-amber-600">
                                Status <ArrowUpDown class="h-3.5 w-3.5" />
                            </button>
                        </th>
                        <th class="px-6 py-4 text-right w-28">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 text-sm text-neutral-600 dark:divide-neutral-800 dark:text-neutral-350">
                    {#each filteredLinks as link}
                        <tr class="hover:bg-neutral-50/50 dark:hover:bg-neutral-900/20">
                            <!-- Label -->
                            <td class="px-6 py-4 font-semibold text-neutral-900 dark:text-white">
                                {link.label}
                            </td>
                            <!-- URL -->
                            <td class="px-6 py-4 font-mono text-xs text-neutral-500 dark:text-neutral-400">
                                <div class="flex items-center gap-1.5">
                                    <Link2 class="h-3.5 w-3.5 text-neutral-400" />
                                    {link.url}
                                </div>
                            </td>
                            <!-- Parent -->
                            <td class="px-6 py-4 text-xs font-medium text-neutral-500">
                                {#if link.parent}
                                    <span class="text-amber-600 dark:text-amber-500 font-semibold">{link.parent.label}</span>
                                {:else}
                                    <span class="text-neutral-400 italic">None (Top-Level)</span>
                                {/if}
                            </td>
                            <!-- Sort Order -->
                            <td class="px-6 py-4 font-mono text-xs">
                                #{link.order}
                            </td>
                            <!-- Status -->
                            <td class="px-6 py-4">
                                {#if link.is_active}
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-450">
                                        <Eye class="h-3 w-3" /> Active
                                    </span>
                                {:else}
                                    <span class="inline-flex items-center gap-1 rounded-full bg-neutral-100 px-2 py-0.5 text-xs font-semibold text-neutral-550 dark:bg-neutral-800 dark:text-neutral-450">
                                        <EyeOff class="h-3 w-3" /> Inactive
                                    </span>
                                {/if}
                            </td>
                            <!-- Actions -->
                            <td class="px-6 py-4 text-right flex justify-end gap-1.5">
                                <Link 
                                    href={`/admin/navigation-links/${link.id}/edit`}
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800"
                                    title="Edit Link"
                                >
                                    <Edit class="h-4 w-4" />
                                </Link>
                                <button 
                                    onclick={() => deleteLink(link.id)}
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-neutral-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/30"
                                    title="Delete Link"
                                >
                                    <Trash class="h-4 w-4" />
                                </button>
                            </td>
                        </tr>
                    {:else}
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-neutral-400 italic">
                                No navigation links match your criteria.
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>
    </div>
</div>
