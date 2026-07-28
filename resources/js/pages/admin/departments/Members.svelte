<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Departments', href: '/admin/departments' },
            { title: 'Faculty Chart', href: '' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { Link, router } from '@inertiajs/svelte';
    import { Plus, Edit, Trash, ArrowLeft, Search, ArrowUpDown, RefreshCw, User } from 'lucide-svelte';

    let {
        department,
        members = [],
    }: {
        department: any;
        members?: any[];
    } = $props();

    // Filters & Sorting state
    let searchQuery = $state('');
    let sortField = $state('order');
    let sortDirection = $state<'asc' | 'desc'>('asc');

    function deleteMember(id: number) {
        if (confirm('Are you sure you want to remove this member from the organization chart?')) {
            router.delete(`/admin/departments/${department.id}/members/${id}`);
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
        sortField = 'order';
        sortDirection = 'asc';
    }

    // Filter & Sort computation
    const filteredMembers = $derived.by(() => {
        let result = [...members];

        // Search text
        if (searchQuery.trim()) {
            const q = searchQuery.toLowerCase();
            result = result.filter(m => 
                m.name.toLowerCase().includes(q) ||
                m.role.toLowerCase().includes(q)
            );
        }

        // Sorting
        result.sort((a, b) => {
            let valA = a[sortField];
            let valB = b[sortField];

            // Resolve supervisor hierarchy display sort
            if (sortField === 'parent') {
                const supA = members.find(m => m.id === a.parent_id);
                const supB = members.find(m => m.id === b.parent_id);
                valA = supA?.name || '';
                valB = supB?.name || '';
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

<AppHead title={`Faculty Chart - ${department.name}`} />

<div class="p-6 max-w-7xl mx-auto w-full space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a 
                href="/admin/departments"
                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-neutral-200 hover:bg-neutral-50 text-neutral-600 dark:border-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-900 animate-in fade-in slide-in-from-left-2"
            >
                <ArrowLeft class="h-4.5 w-4.5" />
            </a>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-white font-serif">Faculty & Org Chart</h1>
                <p class="text-xs text-neutral-500 mt-1">Manage the organizational structure and members for {department.name}.</p>
            </div>
        </div>
        <Link 
            href={`/admin/departments/${department.id}/members/create`}
            class="inline-flex items-center gap-1.5 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-amber-600/10 hover:bg-amber-700 self-start md:self-auto"
        >
            <Plus class="h-4 w-4" /> Add Member
        </Link>
    </div>

    <!-- Filters Panel -->
    <div class="rounded-xl border border-neutral-100 bg-white p-4 shadow-sm dark:border-neutral-900 dark:bg-[#161615] flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="relative w-full md:max-w-xs">
            <Search class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-neutral-400" />
            <input 
                type="text" 
                placeholder="Search member name, role..." 
                bind:value={searchQuery}
                class="w-full rounded-xl border border-neutral-200 pl-10 pr-4 py-2 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
            />
        </div>

        <div class="flex gap-3 w-full md:w-auto items-center justify-end">
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

    <!-- Members Chart Table -->
    <div class="rounded-xl border border-neutral-100 bg-white shadow-sm dark:border-neutral-900 dark:bg-[#161615] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-neutral-100 bg-neutral-50/50 text-xs font-bold text-neutral-500 dark:border-neutral-800 dark:bg-[#0a0a0a]">
                        <th class="px-6 py-4 w-20">Photo</th>
                        <th class="px-6 py-4">
                            <button onclick={() => toggleSort('name')} class="flex items-center gap-1 hover:text-amber-600">
                                Full Name <ArrowUpDown class="h-3.5 w-3.5" />
                            </button>
                        </th>
                        <th class="px-6 py-4">
                            <button onclick={() => toggleSort('role')} class="flex items-center gap-1 hover:text-amber-600">
                                Role / Position <ArrowUpDown class="h-3.5 w-3.5" />
                            </button>
                        </th>
                        <th class="px-6 py-4">
                            <button onclick={() => toggleSort('parent')} class="flex items-center gap-1 hover:text-amber-600">
                                Reports To <ArrowUpDown class="h-3.5 w-3.5" />
                            </button>
                        </th>
                        <th class="px-6 py-4 w-24">
                            <button onclick={() => toggleSort('order')} class="flex items-center gap-1 hover:text-amber-600">
                                Order <ArrowUpDown class="h-3.5 w-3.5" />
                            </button>
                        </th>
                        <th class="px-6 py-4 text-right w-28">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 text-sm text-neutral-600 dark:divide-neutral-800 dark:text-neutral-350">
                    {#each filteredMembers as member}
                        <tr class="hover:bg-neutral-50/50 dark:hover:bg-neutral-900/20">
                            <!-- Photo -->
                            <td class="px-6 py-4">
                                <div class="h-10 w-10 overflow-hidden rounded-full border border-neutral-200 bg-neutral-100 shrink-0 dark:border-neutral-850">
                                    {#if member.image_path}
                                        <img src={member.image_path} alt={member.name} class="h-full w-full object-cover" />
                                    {:else}
                                        <div class="h-full w-full flex items-center justify-center text-neutral-450 bg-neutral-50 dark:bg-[#0a0a0a]">
                                            <User class="h-5 w-5" />
                                        </div>
                                    {/if}
                                </div>
                            </td>
                            <!-- Name -->
                            <td class="px-6 py-4 font-semibold text-neutral-900 dark:text-white">
                                {member.name}
                            </td>
                            <!-- Role -->
                            <td class="px-6 py-4 text-neutral-750 dark:text-neutral-300">
                                {member.role}
                            </td>
                            <!-- Reports To -->
                            <td class="px-6 py-4 text-xs font-medium text-neutral-500">
                                {#if member.parent_id}
                                    {@const supervisor = members.find(m => m.id === member.parent_id)}
                                    {#if supervisor}
                                        <span class="text-amber-600 dark:text-amber-500">{supervisor.name}</span>
                                    {:else}
                                        <span class="text-neutral-400 italic">Unknown supervisor</span>
                                    {/if}
                                {:else}
                                    <span class="text-neutral-400 italic">None (Top Level)</span>
                                {/if}
                            </td>
                            <!-- Order -->
                            <td class="px-6 py-4 font-mono text-xs">
                                #{member.order}
                            </td>
                            <!-- Actions -->
                            <td class="px-6 py-4 text-right flex justify-end gap-1.5">
                                <Link 
                                    href={`/admin/departments/${department.id}/members/${member.id}/edit`}
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800"
                                    title="Edit Member"
                                >
                                    <Edit class="h-4 w-4" />
                                </Link>
                                <button 
                                    onclick={() => deleteMember(member.id)}
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-neutral-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/30"
                                    title="Remove Member"
                                >
                                    <Trash class="h-4 w-4" />
                                </button>
                            </td>
                        </tr>
                    {:else}
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-neutral-400 italic">
                                No department members match your criteria.
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>
    </div>
</div>
