<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Hero Sections', href: '/admin/hero-sections' },
            { title: 'Edit', href: '#' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { useForm } from '@inertiajs/svelte';
    import MarkdownEditor from '@/components/MarkdownEditor.svelte';
    import { ArrowLeft, Save, Info } from 'lucide-svelte';
    import { untrack } from 'svelte';

    let {
        heroSection,
        departments = [],
    }: {
        heroSection: any;
        departments?: any[];
    } = $props();

    const form = useForm(untrack(() => ({
        title: heroSection.title || '',
        content: heroSection.content || '',
        order: heroSection.order || 0,
        is_active: heroSection.is_active,
        department_id: heroSection.department_id || '',
    })));

    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        // Since we are using standard routing but file uploads might not be present,
        // we can POST to update with a standard form post (it is set up as POST in routes)
        form.post(`/admin/hero-sections/${heroSection.id}`);
    }
</script>

<AppHead title="Edit Hero Section" />

<div class="p-6 max-w-5xl mx-auto w-full space-y-6">
    <div class="flex items-center gap-4">
        <a 
            href="/admin/hero-sections"
            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-neutral-200 hover:bg-neutral-50 text-neutral-600 dark:border-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-900"
        >
            <ArrowLeft class="h-4.5 w-4.5" />
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-white font-serif">Edit Hero Section</h1>
            <p class="text-xs text-neutral-500 mt-1">Make changes to "{heroSection.title || 'Hero Section'}".</p>
        </div>
    </div>

    <form onsubmit={handleSubmit} class="space-y-6">
        <div class="rounded-xl border border-neutral-100 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-[#161615] space-y-6">
            
            <div class="rounded-xl bg-amber-50/50 border border-amber-200/30 p-4 text-amber-900 dark:bg-amber-950/10 dark:border-amber-900/20 dark:text-amber-400 text-xs space-y-2">
                <div class="flex items-center gap-1.5 font-bold">
                    <Info class="h-4 w-4 text-amber-600 dark:text-amber-500" />
                    <span>Predefined MDX Elements Library</span>
                </div>
                <p class="leading-relaxed">Available layout containers to create premium page configurations:</p>
                <ul class="list-disc pl-5 space-y-1 mt-1 font-mono">
                    <li><strong>&lt;Card title="Title"&gt;Content&lt;/Card&gt;</strong> - Styles a contained info card block.</li>
                    <li><strong>&lt;Center&gt;Content&lt;/Center&gt;</strong> - Centers aligned nested blocks/text.</li>
                    <li><strong>&lt;Flex&gt;Content&lt;/Flex&gt;</strong> - Places elements side-by-side on desktop.</li>
                    <li><strong>&lt;Accordion title="Header"&gt;Content&lt;/Accordion&gt;</strong> - Expandable summary block.</li>
                    <li><strong>&lt;Badges&gt;Val1, Val2&lt;/Badges&gt;</strong> - Displays comma-separated text as badges.</li>
                </ul>
            </div>

            <!-- Page Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <label for="department_id" class="block text-sm font-semibold text-neutral-900 dark:text-white">Page Location</label>
                    <select 
                        id="department_id"
                        bind:value={form.department_id}
                        class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                    >
                        <option value="">Welcome Page (Main Landing)</option>
                        {#each departments as dept}
                            <option value={dept.id}>{dept.name}</option>
                        {/each}
                    </select>
                    {#if form.errors.department_id}
                        <p class="text-xs text-red-600">{form.errors.department_id}</p>
                    {/if}
                </div>

                <!-- Title label -->
                <div class="space-y-1.5">
                    <label for="title" class="block text-sm font-semibold text-neutral-900 dark:text-white">Section Administrative Title</label>
                    <input 
                        id="title"
                        type="text"
                        bind:value={form.title}
                        class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                        placeholder="e.g. Core Values Spotlight (Only visible to admin)"
                    />
                    {#if form.errors.title}
                        <p class="text-xs text-red-600">{form.errors.title}</p>
                    {/if}
                </div>
            </div>

            <!-- Order & Active -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                <div class="space-y-1.5">
                    <label for="order" class="block text-sm font-semibold text-neutral-900 dark:text-white">Sort Order</label>
                    <input 
                        id="order"
                        type="number"
                        bind:value={form.order}
                        class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                        placeholder="0"
                    />
                    {#if form.errors.order}
                        <p class="text-xs text-red-600">{form.errors.order}</p>
                    {/if}
                </div>

                <div class="flex items-center gap-2.5 pt-6">
                    <input 
                        id="is_active"
                        type="checkbox"
                        bind:checked={form.is_active}
                        class="h-4 w-4 rounded border-neutral-300 text-amber-600 focus:ring-amber-500"
                    />
                    <label for="is_active" class="text-sm font-semibold text-neutral-900 dark:text-white">Visible on Page</label>
                    {#if form.errors.is_active}
                        <p class="text-xs text-red-600">{form.errors.is_active}</p>
                    {/if}
                </div>
            </div>

            <!-- Content Editor -->
            <div class="space-y-1.5">
                <label for="content" class="block text-sm font-semibold text-neutral-900 dark:text-white">Hero Content (Supports MDX & Markdown)</label>
                <MarkdownEditor 
                    id="content"
                    bind:value={form.content}
                    placeholder="Write content here. E.g. # Header \n <Center>Text</Center>"
                />
                {#if form.errors.content}
                    <p class="text-xs text-red-600">{form.errors.content}</p>
                {/if}
            </div>

        </div>

        <div class="flex justify-end gap-3">
            <a 
                href="/admin/hero-sections"
                class="rounded-xl border border-neutral-200 px-5 py-2.5 text-sm font-semibold text-neutral-600 hover:bg-neutral-50 dark:border-neutral-850 dark:text-neutral-450 dark:hover:bg-neutral-900"
            >
                Cancel
            </a>
            <button 
                type="submit"
                disabled={form.processing}
                class="inline-flex items-center gap-1.5 rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-amber-600/10 hover:bg-amber-700 disabled:opacity-50 cursor-pointer"
            >
                <Save class="h-4 w-4" /> Save Changes
            </button>
        </div>
    </form>
</div>
