<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Administrators', href: '/admin/users' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { Link, router } from '@inertiajs/svelte';
    import { Plus, Edit, Trash, Search, ArrowUpDown, RefreshCw, Key, ShieldCheck } from 'lucide-svelte';

    let {
        users = [],
        departments = [],
    }: {
        users?: any[];
        departments?: any[];
    } = $props();

    // Filters & Sorting state
    let searchQuery = $state('');
    let filterRole = $state('');
    let filterDept = $state('');
    let sortField = $state('name');
    let sortDirection = $state<'asc' | 'desc'>('asc');

    function deleteUser(id: number) {
        if (confirm('Are you sure you want to delete this administrator account? This action is permanent.')) {
            router.delete(`/admin/users/${id}`);
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
        filterRole = '';
        filterDept = '';
        sortField = 'name';
        sortDirection = 'asc';
    }

    // Filter & Sort calculation
    const filteredUsers = $derived.by(() => {
        let result = [...users];

        // Search text
        if (searchQuery.trim()) {
            const q = searchQuery.toLowerCase();
            result = result.filter(user => 
                user.name.toLowerCase().includes(q) ||
                user.email.toLowerCase().includes(q)
            );
        }

        // Role filter
        if (filterRole) {
            result = result.filter(user => user.role === filterRole);
        }

        // Department filter
        if (filterDept) {
            result = result.filter(user => String(user.department_id) === filterDept);
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

<AppHead title="Manage Administrators" />

<div class="p-6 max-w-7xl mx-auto w-full space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-white font-serif">Administrators</h1>
            <p class="text-xs text-neutral-500 mt-1">SuperAdmin Only • Register and manage admin accounts, roles, and department assignments.</p>
        </div>
        <Link 
            href="/admin/users/create"
            class="inline-flex items-center gap-1.5 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-amber-600/10 hover:bg-amber-700 self-start md:self-auto"
        >
            <Plus class="h-4 w-4" /> Add Administrator
        </Link>
    </div>

    <!-- Filters Panel -->
    <div class="rounded-xl border border-neutral-100 bg-white p-4 shadow-sm dark:border-neutral-900 dark:bg-[#161615] flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="relative w-full md:max-w-xs">
            <Search class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-neutral-400" />
            <input 
                type="text" 
                placeholder="Search name, email..." 
                bind:value={searchQuery}
                class="w-full rounded-xl border border-neutral-200 pl-10 pr-4 py-2 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
            />
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto items-stretch sm:items-center">
            <!-- Role filter -->
            <select 
                bind:value={filterRole}
                class="rounded-xl border border-neutral-200 px-3 py-2 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
            >
                <option value="">All Role Tiers</option>
                <option value="superadmin">SuperAdmin</option>
                <option value="department_user">Department Editor</option>
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

    <!-- Users List Table -->
    <div class="rounded-xl border border-neutral-100 bg-white shadow-sm dark:border-neutral-900 dark:bg-[#161615] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-neutral-100 bg-neutral-50/50 text-xs font-bold text-neutral-500 dark:border-neutral-800 dark:bg-[#0a0a0a]">
                        <th class="px-6 py-4">
                            <button onclick={() => toggleSort('name')} class="flex items-center gap-1 hover:text-amber-600">
                                Administrator Info <ArrowUpDown class="h-3.5 w-3.5" />
                            </button>
                        </th>
                        <th class="px-6 py-4">
                            <button onclick={() => toggleSort('role')} class="flex items-center gap-1 hover:text-amber-600">
                                Role Tier <ArrowUpDown class="h-3.5 w-3.5" />
                            </button>
                        </th>
                        <th class="px-6 py-4">
                            <button onclick={() => toggleSort('department')} class="flex items-center gap-1 hover:text-amber-600">
                                Assigned Department <ArrowUpDown class="h-3.5 w-3.5" />
                            </button>
                        </th>
                        <th class="px-6 py-4">
                            <button onclick={() => toggleSort('created_at')} class="flex items-center gap-1 hover:text-amber-600">
                                Registered Date <ArrowUpDown class="h-3.5 w-3.5" />
                            </button>
                        </th>
                        <th class="px-6 py-4 text-right w-28">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 text-sm text-neutral-600 dark:divide-neutral-800 dark:text-neutral-350">
                    {#each filteredUsers as user}
                        <tr class="hover:bg-neutral-50/50 dark:hover:bg-neutral-900/20">
                            <!-- Info -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-neutral-100 font-semibold text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300">
                                        {user.name.charAt(0).toUpperCase()}
                                    </div>
                                    <div>
                                        <span class="block font-semibold text-neutral-900 dark:text-white leading-none">{user.name}</span>
                                        <span class="block text-xs text-neutral-450 mt-1">{user.email}</span>
                                    </div>
                                </div>
                            </td>
                            <!-- Role -->
                            <td class="px-6 py-4">
                                {#if user.role === 'superadmin'}
                                    <span class="inline-flex items-center gap-1 rounded bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-950/30 dark:text-amber-400">
                                        <ShieldCheck class="h-3.5 w-3.5" /> SuperAdmin
                                    </span>
                                {:else}
                                    <span class="inline-flex items-center gap-1 rounded bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-950/30 dark:text-blue-400">
                                        <Key class="h-3.5 w-3.5" /> Department Editor
                                    </span>
                                {/if}
                            </td>
                            <!-- Assigned Department -->
                            <td class="px-6 py-4">
                                {#if user.department}
                                    <span class="text-xs font-semibold text-amber-600 dark:text-amber-500">{user.department.name}</span>
                                {:else}
                                    <span class="text-xs text-neutral-400">General Access</span>
                                {/if}
                            </td>
                            <!-- Registered Date -->
                            <td class="px-6 py-4 text-xs">
                                {new Date(user.created_at).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' })}
                            </td>
                            <!-- Actions -->
                            <td class="px-6 py-4 text-right flex justify-end gap-1.5">
                                <Link 
                                    href={`/admin/users/${user.id}/edit`}
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800"
                                    title="Edit Account"
                                >
                                    <Edit class="h-4 w-4" />
                                </Link>
                                <button 
                                    onclick={() => deleteUser(user.id)}
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-neutral-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/30"
                                    title="Delete Account"
                                >
                                    <Trash class="h-4 w-4" />
                                </button>
                            </td>
                        </tr>
                    {:else}
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-neutral-400 italic">
                                No administrators match your criteria.
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>
    </div>
</div>
