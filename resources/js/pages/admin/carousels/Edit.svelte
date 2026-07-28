<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Carousels', href: '/admin/carousels' },
            { title: 'Edit', href: '' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { useForm } from '@inertiajs/svelte';
    import { ArrowLeft, Upload, Save, Image as ImageIcon, Crop, Loader2, ZoomIn } from 'lucide-svelte';
    import { untrack } from 'svelte';
    import ImageCropperModal from '@/components/ImageCropperModal.svelte';
    import ImageViewerModal from '@/components/ImageViewerModal.svelte';

    let {
        carouselItem,
        departments = [],
    }: {
        carouselItem: any;
        departments?: any[];
    } = $props();

    const form = useForm(untrack(() => ({
        title: carouselItem.title || '',
        subtitle: carouselItem.subtitle || '',
        image: null as File | null,
        link_url: carouselItem.link_url || '',
        order: carouselItem.order || 1,
        department_id: carouselItem.department_id || '',
        show_on_main: carouselItem.show_on_main ?? true,
    })));

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
        form.image = file;
        previewImage = previewUrl;
        isCropperOpen = false;
    }

    function cancelCropper() {
        isCropperOpen = false;
    }

    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        // Use POST request to support file uploads on update
        form.post(`/admin/carousels/${carouselItem.id}`);
    }
</script>

<AppHead title="Edit Carousel Slide" />

<ImageCropperModal 
    isOpen={isCropperOpen}
    imageSrc={cropperImageSrc}
    aspectRatio={16 / 9}
    title="Crop Slide Image (16:9)"
    onCrop={handleCropped}
    onCancel={cancelCropper}
/>

<ImageViewerModal 
    isOpen={isViewerOpen}
    imageSrc={viewerImageSrc}
    title="Slide Image Preview"
    onClose={() => isViewerOpen = false}
/>

<div class="p-6 max-w-3xl mx-auto w-full space-y-6">
    <div class="flex items-center gap-4">
        <a 
            href="/admin/carousels"
            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-neutral-200 hover:bg-neutral-50 text-neutral-600 dark:border-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-900"
        >
            <ArrowLeft class="h-4.5 w-4.5" />
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-white">Edit Carousel Slide</h1>
            <p class="text-xs text-neutral-500 mt-1">Make changes to "{carouselItem.title || 'Carousel Slide'}".</p>
        </div>
    </div>

    <div class="rounded-xl border border-neutral-100 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-[#161615] relative overflow-hidden">
        {#if form.processing}
            <div class="absolute inset-0 bg-white/80 dark:bg-black/80 backdrop-blur-xs z-30 flex flex-col items-center justify-center space-y-3">
                <Loader2 class="h-8 w-8 animate-spin text-amber-600" />
                <div class="text-center">
                    <p class="text-sm font-bold text-neutral-900 dark:text-white">Updating Slide Image...</p>
                    <p class="text-xs text-neutral-500 mt-0.5">Uploading new media asset. Please wait.</p>
                </div>
            </div>
        {/if}
        <form onsubmit={handleSubmit} class="space-y-6">
            <!-- Current & New Slide Image -->
            <div class="space-y-3">
                <label for="image" class="block text-sm font-semibold text-neutral-900 dark:text-white">Slide Image</label>
                {#if previewImage}
                    <div class="relative h-44 w-full overflow-hidden rounded-xl border border-neutral-200 bg-neutral-900 dark:border-neutral-800 group">
                        <img src={previewImage} alt="New cropped slide preview" class="h-full w-full object-cover" />
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                            <button 
                                type="button" 
                                onclick={() => openViewer(previewImage!)}
                                class="rounded-lg bg-white/90 px-3 py-1.5 text-xs font-semibold text-neutral-900 hover:bg-white flex items-center gap-1.5 shadow-md"
                            >
                                <ZoomIn class="h-3.5 w-3.5" /> Full Preview
                            </button>
                            <button 
                                type="button" 
                                onclick={() => isCropperOpen = true}
                                class="rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-amber-700 flex items-center gap-1.5 shadow-md"
                            >
                                <Crop class="h-3.5 w-3.5" /> Recrop
                            </button>
                        </div>
                    </div>
                {:else if carouselItem.image_path}
                    <div class="relative h-36 w-full overflow-hidden rounded-xl border border-neutral-200 bg-neutral-100 dark:border-neutral-800 group">
                        <img src={carouselItem.image_path} alt={carouselItem.title} class="h-full w-full object-cover" />
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <button 
                                type="button" 
                                onclick={() => openViewer(carouselItem.image_path)}
                                class="rounded-lg bg-white/90 px-3 py-1.5 text-xs font-semibold text-neutral-900 hover:bg-white flex items-center gap-1.5 shadow-md"
                            >
                                <ZoomIn class="h-3.5 w-3.5" /> Full Preview
                            </button>
                        </div>
                    </div>
                {/if}
                <div class="flex items-center justify-center w-full">
                    <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-neutral-200 border-dashed rounded-xl cursor-pointer hover:bg-neutral-50 dark:hover:bg-neutral-900/50 dark:border-neutral-800">
                        <div class="flex flex-col items-center justify-center pt-3 pb-3">
                            <Upload class="w-5 h-5 mb-1 text-neutral-400" />
                            <p class="mb-0.5 text-xs text-neutral-500 dark:text-neutral-400"><span class="font-semibold">{previewImage ? 'Change Image' : 'Click to replace and crop image'}</span> (Optional)</p>
                            <p class="text-[10px] text-neutral-400">PNG, JPG, or WEBP (Max 5MB)</p>
                        </div>
                        <input id="image" type="file" onchange={handleFileSelect} class="hidden" accept="image/*" />
                    </label>
                </div>
                {#if form.image}
                    <p class="text-xs text-emerald-600 dark:text-emerald-450 font-semibold">New cropped image ready for upload ({form.image.name})</p>
                {/if}
                {#if form.errors.image}
                    <p class="text-xs text-red-600">{form.errors.image}</p>
                {/if}
            </div>

            <!-- Slide Title -->
            <div class="space-y-1.5">
                <label for="title" class="block text-sm font-semibold text-neutral-900 dark:text-white">Slide Title (Optional)</label>
                <input 
                    id="title"
                    type="text" 
                    bind:value={form.title}
                    class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                    placeholder="e.g. Welcome to San Isidro College"
                />
                {#if form.errors.title}
                    <p class="text-xs text-red-600">{form.errors.title}</p>
                {/if}
            </div>

            <!-- Slide Subtitle -->
            <div class="space-y-1.5">
                <label for="subtitle" class="block text-sm font-semibold text-neutral-900 dark:text-white">Slide Subtitle (Optional)</label>
                <input 
                    id="subtitle"
                    type="text" 
                    bind:value={form.subtitle}
                    class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                    placeholder="e.g. Values, Integrity, and Excellence since 1952"
                />
                {#if form.errors.subtitle}
                    <p class="text-xs text-red-600">{form.errors.subtitle}</p>
                {/if}
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Order -->
                <div class="space-y-1.5">
                    <label for="order" class="block text-sm font-semibold text-neutral-900 dark:text-white">Display Order</label>
                    <input 
                        id="order"
                        type="number" 
                        required
                        bind:value={form.order}
                        class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                        placeholder="1"
                    />
                    {#if form.errors.order}
                        <p class="text-xs text-red-600">{form.errors.order}</p>
                    {/if}
                </div>

                <!-- Link URL -->
                <div class="space-y-1.5 md:col-span-2">
                    <label for="link_url" class="block text-sm font-semibold text-neutral-900 dark:text-white">Link URL / Route (Optional)</label>
                    <input 
                        id="link_url"
                        type="text" 
                        bind:value={form.link_url}
                        class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                        placeholder="e.g. /posts, /downloads, or https://..."
                    />
                    {#if form.errors.link_url}
                        <p class="text-xs text-red-600">{form.errors.link_url}</p>
                    {/if}
                </div>
            </div>

            <!-- Owning Department (If any) -->
            <div class="space-y-1.5">
                <label for="department" class="block text-sm font-semibold text-neutral-900 dark:text-white">Target Department page (Optional)</label>
                <select 
                    id="department"
                    bind:value={form.department_id}
                    class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                >
                    <option value="">None / Department Only (if assigned)</option>
                    {#each departments as dept}
                        <option value={dept.id}>{dept.name}</option>
                    {/each}
                </select>
                {#if form.errors.department_id}
                    <p class="text-xs text-red-600">{form.errors.department_id}</p>
                {/if}
            </div>

            <!-- Visibility Settings -->
            <div class="rounded-xl border border-neutral-200/80 bg-neutral-50/50 p-4 dark:border-neutral-800 dark:bg-neutral-900/40 space-y-2">
                <span class="block text-xs font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Visibility Options</span>
                <label class="flex items-start gap-3 cursor-pointer">
                    <input 
                        type="checkbox" 
                        bind:checked={form.show_on_main}
                        class="mt-0.5 h-4.5 w-4.5 rounded border-neutral-300 text-amber-600 focus:ring-amber-500 dark:border-neutral-700 dark:bg-neutral-900"
                    />
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-neutral-900 dark:text-white">Display on Main Page</span>
                        <span class="text-xs text-neutral-500 dark:text-neutral-400">Check this if you want this carousel slide to appear on the main home page.</span>
                    </div>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t border-neutral-100 dark:border-neutral-800">
                <a 
                    href="/admin/carousels"
                    class="rounded-xl border border-neutral-200 px-5 py-2.5 text-sm font-semibold text-neutral-600 hover:bg-neutral-50 dark:border-neutral-850 dark:text-neutral-400 dark:hover:bg-neutral-900"
                >
                    Cancel
                </a>
                <button 
                    type="submit" 
                    disabled={form.processing}
                    class="rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-700 flex items-center gap-1.5"
                >
                    <Save class="h-4 w-4" /> {form.processing ? 'Saving...' : 'Save Changes'}
                </button>
            </div>
        </form>
    </div>
</div>
