<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Navigation Links', href: '/admin/navigation-links' },
            { title: 'Create', href: '/admin/navigation-links/create' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { useForm } from '@inertiajs/svelte';
    import { ArrowLeft, Save } from 'lucide-svelte';
    import NavLinkPreview from '@/components/NavLinkPreview.svelte';

    let {
        parentLinks = [],
    }: {
        parentLinks?: any[];
    } = $props();

    const form = useForm({
        label: '',
        url: '',
        order: 0,
        is_active: true,
        parent_id: '',
    });

    const selectedParentLabel = $derived(
        parentLinks.find((p: any) => String(p.id) === String(form.parent_id))?.label || ''
    );

    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        form.post('/admin/navigation-links');
    }
</script>

<AppHead title="Add Navigation Link" />

<div class="p-6 max-w-3xl mx-auto w-full space-y-6">
    <div class="flex items-center gap-4">
        <a 
            href="/admin/navigation-links"
            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-neutral-200 hover:bg-neutral-50 text-neutral-600 dark:border-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-900"
        >
            <ArrowLeft class="h-4.5 w-4.5" />
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-white font-serif">Add Navigation Link</h1>
            <p class="text-xs text-neutral-500 mt-1">Configure a new link inside the main website navigation bar.</p>
        </div>
    </div>

    <!-- Live Preview -->
    <NavLinkPreview 
        label={form.label}
        url={form.url}
        isActive={form.is_active}
        parentLabel={selectedParentLabel}
    />

    <div class="rounded-xl border border-neutral-100 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-[#161615]">
        <form onsubmit={handleSubmit} class="space-y-6">
            <!-- Label -->
            <div class="space-y-1.5">
                <label for="label" class="block text-sm font-semibold text-neutral-900 dark:text-white">Link Label</label>
                <input 
                    id="label"
                    type="text" 
                    required
                    bind:value={form.label}
                    class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                    placeholder="e.g. Admission"
                />
                {#if form.errors.label}
                    <p class="text-xs text-red-600">{form.errors.label}</p>
                {/if}
            </div>

            <!-- URL -->
            <div class="space-y-1.5">
                <label for="url" class="block text-sm font-semibold text-neutral-900 dark:text-white">Target URL</label>
                <input 
                    id="url"
                    type="text" 
                    required
                    bind:value={form.url}
                    class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                    placeholder="e.g. /admissions, #contact, or https://external.com"
                />
                {#if form.errors.url}
                    <p class="text-xs text-red-600">{form.errors.url}</p>
                {/if}
            </div>

            <!-- Parent Link -->
            <div class="space-y-1.5">
                <label for="parent_id" class="block text-sm font-semibold text-neutral-900 dark:text-white">Parent Link (Optional / Nested Link)</label>
                <select 
                    id="parent_id"
                    bind:value={form.parent_id}
                    class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                >
                    <option value="">None (Top-Level Link)</option>
                    {#each parentLinks as pl}
                        <option value={pl.id}>{pl.label}</option>
                    {/each}
                </select>
                {#if form.errors.parent_id}
                    <p class="text-xs text-red-600">{form.errors.parent_id}</p>
                {/if}
            </div>

            <!-- Order & Active Toggle -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                <div class="space-y-1.5">
                    <label for="order" class="block text-sm font-semibold text-neutral-900 dark:text-white">Sort Order</label>
                    <input 
                        id="order"
                        type="number" 
                        required
                        bind:value={form.order}
                        class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                        placeholder="0"
                    />
                    {#if form.errors.order}
                        <p class="text-xs text-red-600">{form.errors.order}</p>
                    {/if}
                </div>

                <div class="flex items-center gap-2 pt-6">
                    <input 
                        id="is_active"
                        type="checkbox" 
                        bind:checked={form.is_active}
                        class="h-4 w-4 rounded border-neutral-300 text-amber-600 focus:ring-amber-500 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                    />
                    <label for="is_active" class="text-sm font-semibold text-neutral-900 dark:text-white">Active (Visible in header)</label>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end gap-3 pt-4 border-t border-neutral-100 dark:border-neutral-800">
                <a 
                    href="/admin/navigation-links"
                    class="rounded-xl border border-neutral-200 px-5 py-2.5 text-sm font-semibold text-neutral-600 hover:bg-neutral-50 dark:border-neutral-850 dark:text-neutral-400 dark:hover:bg-neutral-900"
                >
                    Cancel
                </a>
                <button 
                    type="submit" 
                    disabled={form.processing}
                    class="rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-700 flex items-center gap-1.5"
                >
                    <Save class="h-4 w-4" /> {form.processing ? 'Saving...' : 'Add Link'}
                </button>
            </div>
        </form>
    </div>
</div>
