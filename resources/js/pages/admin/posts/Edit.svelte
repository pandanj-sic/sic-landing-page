<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Posts', href: '/admin/posts' },
            { title: 'Edit', href: '#' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { useForm } from '@inertiajs/svelte';
    import MarkdownEditor from '@/components/MarkdownEditor.svelte';
    import { ArrowLeft, Save, Crop, Loader2, ZoomIn } from 'lucide-svelte';
    import { untrack } from 'svelte';
    import ImageCropperModal from '@/components/ImageCropperModal.svelte';
    import ImageViewerModal from '@/components/ImageViewerModal.svelte';

    let {
        post,
        departments = [],
    }: {
        post: any;
        departments?: any[];
    } = $props();

    // Prefill dates in standard datetime-local format (YYYY-MM-DDTHH:MM)
    function formatDate(dateStr: string | null) {
        if (!dateStr) return '';
        const d = new Date(dateStr);
        // timezone offset adjustment
        const tzOffset = d.getTimezoneOffset() * 60000;
        const localISOTime = (new Date(d.getTime() - tzOffset)).toISOString().slice(0, 16);
        return localISOTime;
    }

    const form = useForm(untrack(() => ({
        title: post.title || '',
        slug: post.slug || '',
        content: post.content || '',
        type: post.type || 'article',
        start_date: formatDate(post.start_date),
        end_date: formatDate(post.end_date),
        highlight_image: null as File | null,
        department_id: post.department_id || '',
        is_featured: post.is_featured || false,
    })));

    let manuallyEditedSlug = $state(true);

    let cropperImageSrc = $state<string | null>(null);
    let isCropperOpen = $state(false);
    let previewImage = $state<string | null>(null);
    let isViewerOpen = $state(false);
    let viewerImageSrc = $state<string | null>(null);

    function openViewer(src: string) {
        viewerImageSrc = src;
        isViewerOpen = true;
    }

    function handleFileSelect(e: Event) {
        const target = e.target as HTMLInputElement;
        if (target.files && target.files[0]) {
            cropperImageSrc = URL.createObjectURL(target.files[0]);
            isCropperOpen = true;
        }
    }

    function handleCropped(file: File, previewUrl: string) {
        form.highlight_image = file;
        previewImage = previewUrl;
        isCropperOpen = false;
    }

    function cancelCropper() {
        isCropperOpen = false;
    }

    function slugify(text: string) {
        return text
            .toString()
            .toLowerCase()
            .trim()
            .replace(/\s+/g, '-')
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '-');
    }

    $effect(() => {
        if (!manuallyEditedSlug) {
            form.slug = slugify(form.title);
        }
    });

    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        // Send as POST to support file upload updates directly
        form.post(`/admin/posts/${post.id}`);
    }
</script>

<AppHead title="Edit Post" />

<ImageCropperModal 
    isOpen={isCropperOpen}
    imageSrc={cropperImageSrc}
    title="Crop Highlight Image"
    onCrop={handleCropped}
    onCancel={cancelCropper}
/>

<ImageViewerModal 
    isOpen={isViewerOpen}
    imageSrc={viewerImageSrc}
    title="Highlight Image Preview"
    onClose={() => isViewerOpen = false}
/>

<div class="p-6 max-w-7xl mx-auto w-full space-y-6">
    <div class="flex items-center gap-4">
        <a 
            href="/admin/posts"
            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-neutral-200 hover:bg-neutral-50 text-neutral-600 dark:border-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-900"
        >
            <ArrowLeft class="h-4.5 w-4.5" />
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-white">Edit Post</h1>
            <p class="text-xs text-neutral-500 mt-1">Make changes to "{post.title}".</p>
        </div>
    </div>

    <form onsubmit={handleSubmit} class="grid gap-8 lg:grid-cols-3">
        <!-- Form Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl border border-neutral-100 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-[#161615] space-y-4">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-semibold mb-1.5">Post Title</label>
                    <input 
                        id="title"
                        type="text" 
                        required
                        bind:value={form.title}
                        class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                        placeholder="e.g. Enrollment details..."
                    />
                    {#if form.errors.title}
                        <p class="text-xs text-red-600 mt-1">{form.errors.title}</p>
                    {/if}
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-semibold mb-1.5">Post Slug (URL-friendly path)</label>
                    <input 
                        id="slug"
                        type="text" 
                        required
                        bind:value={form.slug}
                        oninput={() => manuallyEditedSlug = true}
                        class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                        placeholder="e.g. enrollment-details"
                    />
                    {#if form.errors.slug}
                        <p class="text-xs text-red-600 mt-1">{form.errors.slug}</p>
                    {/if}
                    <p class="text-[11px] text-neutral-400 mt-1">URL-friendly path. Clear text to re-generate from the title.</p>
                </div>

                <!-- Content Area -->
                <MarkdownEditor 
                    bind:value={form.content} 
                    label="Post Content (MDX)" 
                    error={form.errors.content}
                    placeholder="Write your article, announcement, or event description in Markdown/MDX..."
                />
            </div>
        </div>

        <!-- Sidebar Options -->
        <div class="space-y-6">
            <div class="rounded-xl border border-neutral-100 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-[#161615] space-y-4">
                <h3 class="font-bold text-neutral-950 dark:text-white">Post Settings</h3>
                
                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-semibold mb-1.5">Post Type</label>
                    <select 
                        id="type"
                        bind:value={form.type}
                        class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                    >
                        <option value="article">Article / Story</option>
                        <option value="event">Campus Event</option>
                        <option value="announcement">Official Announcement</option>
                    </select>
                </div>

                <!-- Event dates if type is event -->
                {#if form.type === 'event'}
                    <div class="space-y-4 pt-2 border-t border-neutral-50 dark:border-neutral-850">
                        <div>
                            <label for="start_date" class="block text-sm font-semibold mb-1.5">Event Start Date</label>
                            <input 
                                id="start_date"
                                type="datetime-local" 
                                bind:value={form.start_date}
                                class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                            />
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-semibold mb-1.5">Event End Date</label>
                            <input 
                                id="end_date"
                                type="datetime-local" 
                                bind:value={form.end_date}
                                class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                            />
                        </div>
                    </div>
                {/if}

                <!-- Department -->
                <div>
                    <label for="department_id" class="block text-sm font-semibold mb-1.5">Department Assignment</label>
                    <select 
                        id="department_id"
                        bind:value={form.department_id}
                        class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                    >
                        <option value="">General Landing Page</option>
                        {#each departments as dept}
                            <option value={dept.id}>{dept.name}</option>
                        {/each}
                    </select>
                </div>

                <!-- Highlight Image -->
                <div>
                    <label for="highlight_image" class="block text-sm font-semibold mb-1.5">Highlight Image</label>
                    {#if previewImage}
                        <div class="relative mb-2 rounded-lg overflow-hidden border border-neutral-100 dark:border-neutral-800 group">
                            <img src={previewImage} alt="New cropped highlight preview" class="w-full h-28 object-cover" />
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                <button 
                                    type="button" 
                                    onclick={() => openViewer(previewImage!)}
                                    class="rounded-lg bg-white/90 px-2.5 py-1 text-xs font-semibold text-neutral-900 hover:bg-white flex items-center gap-1 shadow-md"
                                >
                                    <ZoomIn class="h-3 w-3" /> Preview
                                </button>
                                <button 
                                    type="button" 
                                    onclick={() => isCropperOpen = true}
                                    class="rounded-lg bg-amber-600 px-2.5 py-1 text-xs font-semibold text-white hover:bg-amber-700 flex items-center gap-1 shadow-md"
                                >
                                    <Crop class="h-3 w-3" /> Recrop
                                </button>
                            </div>
                        </div>
                    {:else if post.highlight_image_path}
                        <div class="mb-2 relative rounded-lg overflow-hidden border border-neutral-100 dark:border-neutral-800 group">
                            <img src={post.highlight_image_path} alt="Current highlight" class="w-full h-28 object-cover" />
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <button 
                                    type="button" 
                                    onclick={() => openViewer(post.highlight_image_path)}
                                    class="rounded-lg bg-white/90 px-2.5 py-1 text-xs font-semibold text-neutral-900 hover:bg-white flex items-center gap-1 shadow-md"
                                >
                                    <ZoomIn class="h-3 w-3" /> Full Preview
                                </button>
                            </div>
                        </div>
                    {/if}
                    <input 
                        id="highlight_image"
                        type="file" 
                        accept="image/*"
                        onchange={handleFileSelect}
                        class="w-full text-xs file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100"
                    />
                    {#if form.highlight_image}
                        <p class="text-xs text-emerald-600 dark:text-emerald-450 font-semibold mt-1">New cropped image ready for upload ({form.highlight_image.name})</p>
                    {/if}
                    {#if form.errors.highlight_image}
                        <p class="text-xs text-red-600 mt-1">{form.errors.highlight_image}</p>
                    {/if}
                </div>

                <!-- Featured -->
                <div class="flex items-center gap-2 pt-2">
                    <input 
                        id="is_featured" 
                        type="checkbox" 
                        bind:checked={form.is_featured}
                        class="rounded border-neutral-300 text-amber-600 focus:ring-amber-500 h-4 w-4"
                    />
                    <label for="is_featured" class="text-sm font-semibold cursor-pointer">Feature on Landing Page</label>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    disabled={form.processing}
                    class="w-full inline-flex items-center justify-center gap-1.5 rounded-xl bg-amber-600 py-3 text-sm font-semibold text-white shadow-lg shadow-amber-600/10 hover:bg-amber-700 transition-colors disabled:opacity-50"
                >
                    {#if form.processing}
                        <Loader2 class="h-4 w-4 animate-spin" /> Saving Changes & Uploading...
                    {:else}
                        <Save class="h-4 w-4" /> Save Post Changes
                    {/if}
                </button>
            </div>
        </div>
    </form>
</div>
