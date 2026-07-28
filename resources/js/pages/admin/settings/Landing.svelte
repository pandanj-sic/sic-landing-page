<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Landing Settings', href: '/admin/settings/landing' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { useForm } from '@inertiajs/svelte';
    import { Save, Info } from 'lucide-svelte';
    import MarkdownEditor from '@/components/MarkdownEditor.svelte';
    import { untrack } from 'svelte';

    let {
        welcomeHeroContent = '',
    }: {
        welcomeHeroContent?: string;
    } = $props();

    const form = useForm(untrack(() => ({
        welcome_hero_content: welcomeHeroContent,
    })));

    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        form.post('/admin/settings/landing');
    }
</script>

<AppHead title="Landing Page Settings" />

<div class="p-6 max-w-5xl mx-auto w-full space-y-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-white font-serif">Landing Page Settings</h1>
        <p class="text-xs text-neutral-500 mt-1">Configure and style the main Welcome page hero section using MDX components.</p>
    </div>

    {#if form.recentlySuccessful}
        <div class="rounded-xl bg-emerald-50 border border-emerald-200/50 p-4 text-emerald-800 dark:bg-emerald-950/20 dark:border-emerald-900/30 dark:text-emerald-450 text-sm font-medium">
            Settings saved successfully.
        </div>
    {/if}

    <form onsubmit={handleSubmit} class="space-y-6">
        <div class="rounded-xl border border-neutral-100 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-[#161615] space-y-6">
            
            <!-- MDX Documentation Tips -->
            <div class="rounded-xl bg-amber-50/50 border border-amber-200/30 p-4 text-amber-900 dark:bg-amber-950/10 dark:border-amber-900/20 dark:text-amber-400 text-xs space-y-2">
                <div class="flex items-center gap-1.5 font-bold">
                    <Info class="h-4 w-4 text-amber-600 dark:text-amber-500" />
                    <span>MDX Predefined Component Library Guide</span>
                </div>
                <p class="leading-relaxed">You can embed rich layout components directly inside the hero content. Available custom components:</p>
                <ul class="list-disc pl-5 space-y-1 mt-1 font-mono">
                    <li><strong>&lt;Card title="My Title"&gt;Content&lt;/Card&gt;</strong> - Renders a stylized content card block.</li>
                    <li><strong>&lt;Center&gt;Content&lt;/Center&gt;</strong> - Aligns nested text and elements to the center.</li>
                    <li><strong>&lt;Flex&gt;Content&lt;/Flex&gt;</strong> - Responsive column-to-row side-by-side flex grid layout.</li>
                    <li><strong>&lt;Accordion title="Summary Header"&gt;Content&lt;/Accordion&gt;</strong> - Native expand/collapse accordion.</li>
                    <li><strong>&lt;Badges&gt;Tag1, Tag2, Tag3&lt;/Badges&gt;</strong> - Renders a horizontal flex row of tag badges.</li>
                </ul>
            </div>

            <!-- Welcome Hero Content Editor -->
            <div class="space-y-1.5">
                <label for="welcome_hero_content" class="block text-sm font-semibold text-neutral-900 dark:text-white">Hero Content (Supports MDX & Markdown)</label>
                <MarkdownEditor 
                    id="welcome_hero_content"
                    bind:value={form.welcome_hero_content}
                    placeholder="Enter welcome hero content markdown/MDX here..."
                />
                {#if form.errors.welcome_hero_content}
                    <p class="text-xs text-red-600">{form.errors.welcome_hero_content}</p>
                {/if}
            </div>

        </div>

        <div class="flex justify-end">
            <button 
                type="submit" 
                disabled={form.processing}
                class="inline-flex items-center gap-1.5 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-amber-600/10 hover:bg-amber-700 disabled:opacity-50 cursor-pointer"
            >
                <Save class="h-4 w-4" /> Save Settings
            </button>
        </div>
    </form>
</div>
