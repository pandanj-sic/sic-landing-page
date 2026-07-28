<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Hero Sections', href: '/admin/hero-sections' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { Link, router } from '@inertiajs/svelte';
    import { PlusCircle, Edit, Trash, Search, ArrowUpDown, RefreshCw, Eye, EyeOff, Sparkles } from 'lucide-svelte';

    let {
        heroSections = [],
    }: {
        heroSections?: any[];
    } = $props();

    // Search, Filter, Sort state
    let searchQuery = $state('');
    let filterDept = $state('');
    let sortField = $state('order');
    let sortDirection = $state<'asc' | 'desc'>('asc');

    function deleteHeroSection(id: number) {
        if (confirm('Are you sure you want to delete this hero section?')) {
            router.delete(`/admin/hero-sections/${id}`);
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
        filterDept = '';
        sortField = 'order';
        sortDirection = 'asc';
    }

    // Unique departments listing for filter
    const departmentsList = $derived.by(() => {
        const map = new Map();
        heroSections.forEach(hs => {
            if (hs.department) {
                map.set(hs.department.id, hs.department.name);
            }
        });
        return Array.from(map.entries()).map(([id, name]) => ({ id, name }));
    });

    const filteredSections = $derived.by(() => {
        let result = [...heroSections];

        // Search text
        if (searchQuery.trim()) {
            const q = searchQuery.toLowerCase();
            result = result.filter(hs => 
                (hs.title && hs.title.toLowerCase().includes(q)) ||
                (hs.content && hs.content.toLowerCase().includes(q))
            );
        }

        // Department filter
        if (filterDept) {
            if (filterDept === 'welcome') {
                result = result.filter(hs => !hs.department_id);
            } else {
                result = result.filter(hs => String(hs.department_id) === filterDept);
            }
        }

        // Sorting
        result.sort((a, b) => {
            let valA = a[sortField];
            let valB = b[sortField];

            if (sortField === 'department') {
                valA = a.department?.name || 'Welcome Page';
                valB = b.department?.name || 'Welcome Page';
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

<AppHead title="Manage Hero Sections" />

<div class="p-6 max-w-7xl mx-auto w-full space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-white font-serif">Hero Sections</h1>
            <p class="text-xs text-neutral-500 mt-1">Manage multiple rich MDX-formatted hero section content blocks for landing and department pages.</p>
        </div>
        <Link 
            href="/admin/hero-sections/create"
            class="inline-flex items-center gap-1.5 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-amber-600/10 hover:bg-amber-700 self-start md:self-auto"
        >
            <PlusCircle class="h-4.5 w-4.5" /> Create Hero Section
        </Link>
    </div>

    <!-- Filter Toolbar -->
    <div class="rounded-xl border border-neutral-100 bg-white p-4 shadow-sm dark:border-neutral-900 dark:bg-[#161615] flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="relative w-full md:max-w-xs">
            <Search class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-neutral-400" />
            <input 
                type="text" 
                placeholder="Search content..." 
                bind:value={searchQuery}
                class="w-full rounded-xl border border-neutral-200 pl-10 pr-4 py-2 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
            />
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto items-stretch sm:items-center">
            <!-- Department filter -->
            <select 
                bind:value={filterDept}
                class="rounded-xl border border-neutral-200 px-3 py-2 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
            >
                <option value="">All Pages / Departments</option>
                <option value="welcome">Welcome Page (Main Landing)</option>
                {#each departmentsList as dept}
                    <option value={String(dept.id)}>{dept.name}</option>
                {/each}
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

    <!-- Table -->
    <div class="rounded-xl border border-neutral-100 bg-white shadow-sm dark:border-neutral-900 dark:bg-[#161615] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-neutral-100 bg-neutral-50/50 text-xs font-bold text-neutral-500 dark:border-neutral-800 dark:bg-[#0a0a0a]">
                        <th class="px-6 py-4">
                            <button onclick={() => toggleSort('title')} class="flex items-center gap-1 hover:text-amber-600">
                                Section Info <ArrowUpDown class="h-3.5 w-3.5" />
                            </button>
                        </th>
                        <th class="px-6 py-4">
                            <button onclick={() => toggleSort('department')} class="flex items-center gap-1 hover:text-amber-600">
                                Page Location <ArrowUpDown class="h-3.5 w-3.5" />
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
                    {#each filteredSections as hs}
                        <tr class="hover:bg-neutral-50/50 dark:hover:bg-neutral-900/20">
                            <!-- Title / Snippet -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-600/10 text-amber-600">
                                        <Sparkles class="h-4.5 w-4.5" />
                                    </div>
                                    <div class="min-w-0">
                                        <span class="block font-semibold text-neutral-900 dark:text-white truncate max-w-sm">
                                            {hs.title || 'Untitled Section'}
                                        </span>
                                        <span class="block text-[11px] text-neutral-400 mt-0.5 truncate max-w-sm">
                                            {hs.content.substring(0, 75)}...
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <!-- Location -->
                            <td class="px-6 py-4 font-semibold text-neutral-700 dark:text-neutral-300">
                                {#if hs.department}
                                    <span class="text-xs rounded bg-neutral-100 px-2 py-1 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-450">{hs.department.name}</span>
                                {:else}
                                    <span class="text-xs rounded bg-amber-50 px-2 py-1 text-amber-700 dark:bg-amber-950/20 dark:text-amber-400">Welcome Page</span>
                                {/if}
                            </td>
                            <!-- Order -->
                            <td class="px-6 py-4 font-mono text-xs text-neutral-500">
                                #{hs.order}
                            </td>
                            <!-- Status -->
                            <td class="px-6 py-4">
                                {#if hs.is_active}
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-450">
                                        <Eye class="h-3 w-3" /> Active
                                    </span>
                                {:else}
                                    <span class="inline-flex items-center gap-1 rounded-full bg-neutral-100 px-2 py-0.5 text-xs font-semibold text-neutral-500 dark:bg-neutral-800 dark:text-neutral-450">
                                        <EyeOff class="h-3 w-3" /> Inactive
                                    </span>
                                {/if}
                            </td>
                            <!-- Actions -->
                            <td class="px-6 py-4 text-right flex justify-end gap-1.5">
                                <Link 
                                    href={`/admin/hero-sections/${hs.id}/edit`}
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800"
                                    title="Edit Section"
                                >
                                    <Edit class="h-4 w-4" />
                                </Link>
                                <button 
                                    onclick={() => deleteHeroSection(hs.id)}
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-neutral-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/30"
                                    title="Delete Section"
                                >
                                    <Trash class="h-4 w-4" />
                                </button>
                            </td>
                        </tr>
                    {:else}
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-neutral-400 italic">
                                No hero sections found.
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>
    </div>
</div>
