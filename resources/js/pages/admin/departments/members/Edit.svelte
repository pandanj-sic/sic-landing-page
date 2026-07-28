<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Departments', href: '/admin/departments' },
            { title: 'Faculty Chart', href: '' },
            { title: 'Edit Member', href: '' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { useForm } from '@inertiajs/svelte';
    import { ArrowLeft, Save, Upload, Crop, Loader2, ZoomIn } from 'lucide-svelte';
    import { untrack } from 'svelte';
    import ImageCropperModal from '@/components/ImageCropperModal.svelte';
    import ImageViewerModal from '@/components/ImageViewerModal.svelte';

    let {
        department,
        member,
        members = [],
    }: {
        department: any;
        member: any;
        members?: any[];
    } = $props();

    const form = useForm(untrack(() => ({
        name: member.name || '',
        role: member.role || '',
        parent_id: member.parent_id || '',
        order: member.order || 1,
        image: null as File | null,
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
        // Post directly to handle file uploads properly in Laravel
        form.post(`/admin/departments/${department.id}/members/${member.id}`);
    }
</script>

<AppHead title={`Edit Faculty Member - ${department.name}`} />

<ImageCropperModal 
    isOpen={isCropperOpen}
    imageSrc={cropperImageSrc}
    aspectRatio={1}
    title="Crop Profile Photo (1:1)"
    onCrop={handleCropped}
    onCancel={cancelCropper}
/>

<ImageViewerModal 
    isOpen={isViewerOpen}
    imageSrc={viewerImageSrc}
    title="Profile Photo Preview"
    onClose={() => isViewerOpen = false}
/>

<div class="p-6 max-w-3xl mx-auto w-full space-y-6">
    <div class="flex items-center gap-4">
        <a 
            href={`/admin/departments/${department.id}/members`}
            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-neutral-200 hover:bg-neutral-50 text-neutral-600 dark:border-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-900"
        >
            <ArrowLeft class="h-4.5 w-4.5" />
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-white font-serif">Edit Faculty Member</h1>
            <p class="text-xs text-neutral-500 mt-1">Make changes to "{member.name}" settings.</p>
        </div>
    </div>

    <div class="rounded-xl border border-neutral-100 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-[#161615]">
        <form onsubmit={handleSubmit} class="space-y-6">
            <!-- Image Display & File -->
            <div class="flex flex-col sm:flex-row gap-6 items-start sm:items-center">
                {#if previewImage}
                    <div class="relative h-20 w-20 overflow-hidden rounded-full border border-neutral-200 bg-neutral-100 shrink-0 group">
                        <img src={previewImage} alt="Cropped preview" class="h-full w-full object-cover" />
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button 
                                type="button" 
                                onclick={() => openViewer(previewImage!)}
                                class="rounded-lg bg-white/90 p-1 text-neutral-900 hover:bg-white shadow-md"
                                title="Full Preview"
                            >
                                <ZoomIn class="h-3.5 w-3.5" />
                            </button>
                            <button 
                                type="button" 
                                onclick={() => isCropperOpen = true}
                                class="rounded-lg bg-amber-600 p-1 text-white hover:bg-amber-700 shadow-md"
                                title="Recrop Photo"
                            >
                                <Crop class="h-3.5 w-3.5" />
                            </button>
                        </div>
                    </div>
                {:else if member.image_path}
                    <div class="relative h-20 w-20 overflow-hidden rounded-full border border-neutral-200 bg-neutral-100 shrink-0 group">
                        <img src={member.image_path} alt={member.name} class="h-full w-full object-cover" />
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <button 
                                type="button" 
                                onclick={() => openViewer(member.image_path)}
                                class="rounded-lg bg-white/90 p-1.5 text-neutral-900 hover:bg-white shadow-md"
                                title="Full Preview"
                            >
                                <ZoomIn class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                {/if}
                <div class="space-y-1.5 w-full">
                    <label for="image" class="block text-sm font-semibold text-neutral-900 dark:text-white">Update Photo (Optional)</label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-neutral-200 border-dashed rounded-xl cursor-pointer hover:bg-neutral-50 dark:hover:bg-neutral-900/50 dark:border-neutral-800">
                            <div class="flex flex-col items-center justify-center pt-2 pb-3">
                                <Upload class="w-6 h-6 mb-1 text-neutral-400" />
                                <p class="text-xs text-neutral-500 dark:text-neutral-400"><span class="font-semibold">{previewImage ? 'Change Photo' : 'Click to upload and crop photo'}</span></p>
                            </div>
                            <input id="image" type="file" onchange={handleFileSelect} class="hidden" accept="image/*" />
                        </label>
                    </div>
                    {#if form.image}
                        <p class="text-xs text-emerald-600 dark:text-emerald-450 font-semibold">Cropped photo ready ({form.image.name})</p>
                    {/if}
                    {#if form.errors.image}
                        <p class="text-xs text-red-600">{form.errors.image}</p>
                    {/if}
                </div>
            </div>

            <!-- Name -->
            <div class="space-y-1.5">
                <label for="name" class="block text-sm font-semibold text-neutral-900 dark:text-white">Full Name</label>
                <input 
                    id="name"
                    type="text" 
                    required
                    bind:value={form.name}
                    class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                    placeholder="e.g. Prof. Juan Dela Cruz"
                />
                {#if form.errors.name}
                    <p class="text-xs text-red-600">{form.errors.name}</p>
                {/if}
            </div>

            <!-- Role / Designation -->
            <div class="space-y-1.5">
                <label for="role" class="block text-sm font-semibold text-neutral-900 dark:text-white">Role / Position Title</label>
                <input 
                    id="role"
                    type="text" 
                    required
                    bind:value={form.role}
                    class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                    placeholder="e.g. Department Dean, Instructor"
                />
                {#if form.errors.role}
                    <p class="text-xs text-red-600">{form.errors.role}</p>
                {/if}
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Supervisor / Reports To -->
                <div class="space-y-1.5">
                    <label for="parent_id" class="block text-sm font-semibold text-neutral-900 dark:text-white">Reports To (Supervisor)</label>
                    <select 
                        id="parent_id"
                        bind:value={form.parent_id}
                        class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                    >
                        <option value="">Top Level (Does not report to anyone here)</option>
                        {#each members as m}
                            <option value={m.id}>{m.name} ({m.role})</option>
                        {/each}
                    </select>
                    {#if form.errors.parent_id}
                        <p class="text-xs text-red-600">{form.errors.parent_id}</p>
                    {/if}
                </div>

                <!-- Display Order -->
                <div class="space-y-1.5">
                    <label for="order" class="block text-sm font-semibold text-neutral-900 dark:text-white">Sort Order</label>
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
            </div>

            <!-- Submit -->
            <div class="flex justify-end gap-3 pt-4 border-t border-neutral-100 dark:border-neutral-800">
                <a 
                    href={`/admin/departments/${department.id}/members`}
                    class="rounded-xl border border-neutral-200 px-5 py-2.5 text-sm font-semibold text-neutral-600 hover:bg-neutral-50 dark:border-neutral-850 dark:text-neutral-400 dark:hover:bg-neutral-900"
                >
                    Cancel
                </a>
                <button 
                    type="submit" 
                    disabled={form.processing}
                    class="rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-700 flex items-center gap-1.5 disabled:opacity-50"
                >
                    {#if form.processing}
                        <Loader2 class="h-4 w-4 animate-spin" /> Saving & Uploading...
                    {:else}
                        <Save class="h-4 w-4" /> Save Member Changes
                    {/if}
                </button>
            </div>
        </form>
    </div>
</div>
