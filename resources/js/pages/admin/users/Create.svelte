<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Administrators', href: '/admin/users' },
            { title: 'Create', href: '/admin/users/create' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { useForm } from '@inertiajs/svelte';
    import { ArrowLeft, Save } from 'lucide-svelte';

    let {
        departments = [],
    }: {
        departments?: any[];
    } = $props();

    const form = useForm({
        name: '',
        email: '',
        password: '',
        role: 'department_user', // superadmin | department_user
        department_id: '',
    });

    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        form.post('/admin/users');
    }
</script>

<AppHead title="Add Administrator" />

<div class="p-6 max-w-3xl mx-auto w-full space-y-6">
    <div class="flex items-center gap-4">
        <a 
            href="/admin/users"
            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-neutral-200 hover:bg-neutral-50 text-neutral-600 dark:border-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-900"
        >
            <ArrowLeft class="h-4.5 w-4.5" />
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-white">Add Administrator</h1>
            <p class="text-xs text-neutral-500 mt-1">Register a new administrator or department editor.</p>
        </div>
    </div>

    <div class="rounded-xl border border-neutral-100 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-[#161615]">
        <form onsubmit={handleSubmit} class="space-y-6">
            <!-- Name -->
            <div class="space-y-1.5">
                <label for="name" class="block text-sm font-semibold text-neutral-900 dark:text-white">Full Name</label>
                <input 
                    id="name"
                    type="text" 
                    required
                    bind:value={form.name}
                    class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                    placeholder="e.g. Dr. Jose Rizal"
                />
                {#if form.errors.name}
                    <p class="text-xs text-red-600">{form.errors.name}</p>
                {/if}
            </div>

            <!-- Email & Password -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <label for="email" class="block text-sm font-semibold text-neutral-900 dark:text-white">Email Address</label>
                    <input 
                        id="email"
                        type="email" 
                        required
                        bind:value={form.email}
                        class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                        placeholder="name@email.com"
                    />
                    {#if form.errors.email}
                        <p class="text-xs text-red-600">{form.errors.email}</p>
                    {/if}
                </div>

                <div class="space-y-1.5">
                    <label for="password" class="block text-sm font-semibold text-neutral-900 dark:text-white">Password</label>
                    <input 
                        id="password"
                        type="password" 
                        required
                        bind:value={form.password}
                        class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                        placeholder="At least 8 characters..."
                    />
                    {#if form.errors.password}
                        <p class="text-xs text-red-600">{form.errors.password}</p>
                    {/if}
                </div>
            </div>

            <!-- Role & Department Assignment -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <label for="role" class="block text-sm font-semibold text-neutral-900 dark:text-white">Role Tier</label>
                    <select 
                        id="role"
                        bind:value={form.role}
                        class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                    >
                        <option value="superadmin">SuperAdmin (Full Site Control)</option>
                        <option value="department_user">Department Editor (Restricted to Department)</option>
                    </select>
                    {#if form.errors.role}
                        <p class="text-xs text-red-600">{form.errors.role}</p>
                    {/if}
                </div>

                {#if form.role === 'department_user'}
                    <div class="space-y-1.5">
                        <label for="department" class="block text-sm font-semibold text-neutral-900 dark:text-white">Assigned Department</label>
                        <select 
                            id="department"
                            required
                            bind:value={form.department_id}
                            class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                        >
                            <option value="">Select Department...</option>
                            {#each departments as dept}
                                <option value={dept.id}>{dept.name}</option>
                            {/each}
                        </select>
                        {#if form.errors.department_id}
                            <p class="text-xs text-red-600">{form.errors.department_id}</p>
                        {/if}
                    </div>
                {/if}
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t border-neutral-100 dark:border-neutral-800">
                <a 
                    href="/admin/users"
                    class="rounded-xl border border-neutral-200 px-5 py-2.5 text-sm font-semibold text-neutral-600 hover:bg-neutral-50 dark:border-neutral-850 dark:text-neutral-400 dark:hover:bg-neutral-900"
                >
                    Cancel
                </a>
                <button 
                    type="submit" 
                    disabled={form.processing}
                    class="rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-700 flex items-center gap-1.5"
                >
                    <Save class="h-4 w-4" /> {form.processing ? 'Creating...' : 'Create Account'}
                </button>
            </div>
        </form>
    </div>
</div>
