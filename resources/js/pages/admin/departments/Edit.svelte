<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Departments', href: '/admin/departments' },
            { title: 'Edit', href: '#' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { useForm } from '@inertiajs/svelte';
    import { ArrowLeft, Save, Upload, Crop, ZoomIn, Loader2, FileImage, Trash2, Image as ImageIcon } from 'lucide-svelte';
    import { untrack } from 'svelte';
    import MarkdownEditor from '@/components/MarkdownEditor.svelte';
    import ImageCropperModal from '@/components/ImageCropperModal.svelte';
    import ImageViewerModal from '@/components/ImageViewerModal.svelte';

    let {
        department,
    }: {
        department: {
            id: number;
            name: string;
            slug: string;
            description?: string;
            hero_content?: string;
            logo_path?: string;
        };
    } = $props();

    const form = useForm(untrack(() => ({
        name: department?.name || '',
        description: department?.description || '',
        hero_content: department?.hero_content || '',
        logo: null as File | null,
    })));

    let cropperImageSrc = $state<string | null>(null);
    let isCropperOpen = $state(false);
    let previewImage = $state<string | null>(null);
    let isViewerOpen = $state(false);
    let viewerImageSrc = $state<string | null>(null);
    let fileInputRef = $state<HTMLInputElement | null>(null);

    function formatFileSize(bytes: number): string {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function openViewer(src: string) {
        viewerImageSrc = src;
        isViewerOpen = true;
    }

    function handleFileSelect(e: Event) {
        const target = e.target as HTMLInputElement;
        if (target.files && target.files[0]) {
            const file = target.files[0];
            cropperImageSrc = URL.createObjectURL(file);
            isCropperOpen = true;
        }
    }

    function handleCropped(file: File, previewUrl: string) {
        form.logo = file;
        previewImage = previewUrl;
        isCropperOpen = false;
    }

    function cancelCropper() {
        isCropperOpen = false;
    }

    function removeNewLogo() {
        form.logo = null;
        previewImage = null;
        cropperImageSrc = null;
        if (fileInputRef) fileInputRef.value = '';
    }

    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        form.post(`/admin/departments/${department.id}`);
    }
</script>

<AppHead title={`Edit ${department?.name || 'Department'}`} />

<ImageCropperModal 
    isOpen={isCropperOpen}
    imageSrc={cropperImageSrc}
    aspectRatio={1}
    title="Crop Department Logo (1:1)"
    onCrop={handleCropped}
    onCancel={cancelCropper}
/>

<ImageViewerModal 
    isOpen={isViewerOpen}
    imageSrc={viewerImageSrc}
    title="Department Logo Preview"
    onClose={() => isViewerOpen = false}
/>

<div class="p-6 max-w-4xl mx-auto w-full space-y-6">
    <div class="flex items-center gap-4">
        <a 
            href="/admin/departments"
            class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-neutral-200 hover:bg-neutral-50 text-neutral-600 dark:border-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-900 transition-colors"
        >
            <ArrowLeft class="h-4.5 w-4.5" />
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-white">Edit Department Details</h1>
            <p class="text-xs text-neutral-500 mt-1">Update description, hero MDX content, and logo for {department?.name}.</p>
        </div>
    </div>

    <div class="rounded-2xl border border-neutral-100 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-[#161615] relative overflow-hidden">
        {#if form.processing}
            <div class="absolute inset-0 z-20 bg-white/70 dark:bg-[#161615]/70 backdrop-blur-xs flex items-center justify-center">
                <div class="flex items-center gap-2 text-amber-600 font-semibold text-sm">
                    <Loader2 class="h-5 w-5 animate-spin" /> Saving department changes...
                </div>
            </div>
        {/if}

        <form onsubmit={handleSubmit} class="space-y-6">
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Name -->
                <div class="space-y-1.5 md:col-span-2">
                    <label for="name" class="block text-sm font-semibold text-neutral-900 dark:text-white">Department Name *</label>
                    <input 
                        id="name"
                        type="text" 
                        required
                        bind:value={form.name}
                        class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                        placeholder="e.g. Department of Computer Science"
                    />
                    {#if form.errors.name}
                        <p class="text-xs text-red-600">{form.errors.name}</p>
                    {/if}
                </div>

                <!-- Short Description -->
                <div class="space-y-1.5 md:col-span-2">
                    <label for="description" class="block text-sm font-semibold text-neutral-900 dark:text-white">Short Description</label>
                    <textarea 
                        id="description"
                        rows="3"
                        bind:value={form.description}
                        class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                        placeholder="Brief overview of department degree programs, mission, or objectives..."
                    ></textarea>
                    {#if form.errors.description}
                        <p class="text-xs text-red-600">{form.errors.description}</p>
                    {/if}
                </div>

                <!-- Logo Section: Current & New Preview -->
                <div class="space-y-3 md:col-span-2">
                    <label for="logo" class="block text-sm font-semibold text-neutral-900 dark:text-white">Department Logo</label>
                    
                    {#if department.logo_path && !previewImage}
                        <!-- Existing Logo Display -->
                        <div class="rounded-xl border border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-[#0a0a0a] p-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="relative group h-14 w-14 shrink-0 overflow-hidden rounded-xl border border-neutral-200 bg-white p-1 dark:border-neutral-800 dark:bg-neutral-900">
                                    <img src={department.logo_path} alt={department.name} class="h-full w-full object-contain" />
                                    <button 
                                        type="button" 
                                        onclick={() => openViewer(department.logo_path!)}
                                        class="absolute inset-0 bg-black/40 flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity"
                                        title="View current logo"
                                    >
                                        <ZoomIn class="h-4 w-4" />
                                    </button>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-neutral-900 dark:text-white">Current Logo</p>
                                    <p class="text-xs text-neutral-500">Active logo on website header & department landing page.</p>
                                </div>
                            </div>
                        </div>
                    {/if}

                    {#if previewImage && form.logo}
                        <!-- New Uploaded File Live Preview Box -->
                        <div class="rounded-xl border border-amber-600/30 bg-amber-600/5 p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="relative group h-20 w-20 shrink-0 overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
                                    <img src={previewImage} alt="New cropped logo preview" class="h-full w-full object-contain p-1" />
                                    <button 
                                        type="button" 
                                        onclick={() => openViewer(previewImage!)}
                                        class="absolute inset-0 bg-black/40 flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity"
                                        title="View full size preview"
                                    >
                                        <ZoomIn class="h-4 w-4" />
                                    </button>
                                </div>
                                <div class="space-y-1 text-left">
                                    <div class="flex items-center gap-2">
                                        <FileImage class="h-4 w-4 text-amber-600" />
                                        <span class="text-xs font-bold text-neutral-900 dark:text-white truncate max-w-[200px] sm:max-w-[300px]">{form.logo.name}</span>
                                    </div>
                                    <p class="text-xs text-neutral-500">Size: {formatFileSize(form.logo.size)}</p>
                                    <p class="text-xs text-emerald-600 dark:text-emerald-450 font-semibold">New cropped logo ready to replace current logo</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button" 
                                    onclick={() => isCropperOpen = true}
                                    class="inline-flex items-center gap-1 rounded-lg border border-neutral-200 px-3 py-1.5 text-xs font-semibold text-neutral-700 hover:bg-neutral-100 dark:border-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-850"
                                >
                                    <Crop class="h-3.5 w-3.5" /> Recrop
                                </button>
                                <button 
                                    type="button" 
                                    onclick={removeNewLogo}
                                    class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-100 dark:border-red-900/30 dark:bg-red-950/20 dark:text-red-400"
                                >
                                    <Trash2 class="h-3.5 w-3.5" /> Undo
                                </button>
                            </div>
                        </div>
                    {:else}
                        <!-- File Upload Selector / Dropzone -->
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-neutral-200 border-dashed rounded-xl cursor-pointer hover:bg-neutral-50 dark:hover:bg-neutral-900/50 dark:border-neutral-800 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-4 pb-5">
                                    <Upload class="w-7 h-7 mb-1 text-neutral-400" />
                                    <p class="mb-1 text-xs text-neutral-600 dark:text-neutral-300"><span class="font-semibold text-amber-600">Click to upload new logo</span> or drag and drop</p>
                                    <p class="text-[11px] text-neutral-400">PNG, JPG, SVG, or WEBP (Max 2MB)</p>
                                </div>
                                <input 
                                    bind:this={fileInputRef}
                                    id="logo" 
                                    type="file" 
                                    accept="image/*"
                                    class="hidden" 
                                    onchange={handleFileSelect} 
                                />
                            </label>
                        </div>
                    {/if}
                    {#if form.errors.logo}
                        <p class="text-xs text-red-600">{form.errors.logo}</p>
                    {/if}
                </div>

                <!-- Hero Content (Markdown / MDX) -->
                <div class="space-y-1.5 md:col-span-2">
                    <MarkdownEditor 
                        bind:value={form.hero_content} 
                        label="Hero Content (Markdown / MDX)"
                        placeholder="### Welcome to the Department&#10;&#10;Describe faculty achievements, degree overview, or announcements..."
                        rows={8}
                    />
                    {#if form.errors.hero_content}
                        <p class="text-xs text-red-600">{form.errors.hero_content}</p>
                    {/if}
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-neutral-100 dark:border-neutral-800">
                <a 
                    href="/admin/departments"
                    class="rounded-xl border border-neutral-200 px-5 py-2.5 text-xs font-semibold text-neutral-600 hover:bg-neutral-50 dark:border-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-900 transition-colors"
                >
                    Cancel
                </a>
                <button 
                    type="submit" 
                    disabled={form.processing}
                    class="inline-flex items-center gap-2 rounded-xl bg-amber-600 px-6 py-2.5 text-xs font-semibold text-white shadow-md shadow-amber-600/10 hover:bg-amber-700 disabled:opacity-50 transition-colors"
                >
                    {#if form.processing}
                        <Loader2 class="h-4 w-4 animate-spin" /> Saving...
                    {:else}
                        <Save class="h-4 w-4" /> Save Department
                    {/if}
                </button>
            </div>
        </form>
    </div>
</div>
