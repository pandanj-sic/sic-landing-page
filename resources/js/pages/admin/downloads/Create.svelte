<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Downloads', href: '/admin/downloads' },
            { title: 'Upload', href: '/admin/downloads/create' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { useForm } from '@inertiajs/svelte';
    import { ArrowLeft, Upload, Folder, Tag, Save } from 'lucide-svelte';

    let {
        departments = [],
        customDirectories = [],
    }: {
        departments?: any[];
        customDirectories?: any[];
    } = $props();

    const form = useForm({
        file_name: '',
        file: null as File | null,
        directory: '',
        tags: '',
        department_id: '',
    });

    let fileInputFiles = $state<FileList | null>(null);

    $effect(() => {
        if (fileInputFiles && fileInputFiles.length > 0) {
            form.file = fileInputFiles[0];
            if (!form.file_name) {
                const nameWithoutExt = fileInputFiles[0].name.substring(0, fileInputFiles[0].name.lastIndexOf('.')) || fileInputFiles[0].name;
                form.file_name = nameWithoutExt.replace(/_/g, ' ').replace(/-/g, ' ');
            }
        }
    });

    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        form.post('/admin/downloads', {
            onSuccess: () => {
                form.reset();
                fileInputFiles = null;
            }
        });
    }
</script>

<AppHead title="Upload Document" />

<div class="p-6 max-w-3xl mx-auto w-full space-y-6">
    <div class="flex items-center gap-4">
        <a 
            href="/admin/downloads"
            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-neutral-200 hover:bg-neutral-50 text-neutral-600 dark:border-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-900"
        >
            <ArrowLeft class="h-4.5 w-4.5" />
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-white">Upload Document</h1>
            <p class="text-xs text-neutral-500 mt-1">Publish forms, manuals, or resources to the downloads directory.</p>
        </div>
    </div>

    <div class="rounded-xl border border-neutral-100 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-[#161615]">
        <form onsubmit={handleSubmit} class="space-y-6">
            <!-- File Input -->
            <div class="space-y-1.5">
                <label for="file" class="block text-sm font-semibold text-neutral-900 dark:text-white">Select Document File</label>
                <div class="flex items-center justify-center w-full">
                    <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-neutral-200 border-dashed rounded-2xl cursor-pointer hover:border-amber-500 hover:bg-amber-500/5 transition-all dark:border-neutral-800 dark:hover:border-amber-500/50">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500/10 text-amber-500 border border-amber-500/20 mb-3 shadow-xs">
                                <Upload class="w-6 h-6" />
                            </div>
                            <p class="mb-1 text-sm text-neutral-700 dark:text-neutral-300 font-medium">
                                <span class="font-bold text-amber-600 dark:text-amber-400">Click to choose file</span> or drag & drop here
                            </p>
                            <p class="text-xs text-neutral-400">Supports PDF, DOCX, XLSX, Images, ZIP (Max 20MB)</p>
                        </div>
                        <input id="file" type="file" required class="hidden" bind:files={fileInputFiles} />
                    </label>
                </div>
                {#if form.file}
                    <div class="flex items-center gap-2 rounded-xl bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:border-amber-900/40 dark:text-amber-300">
                        <Upload class="h-4 w-4 shrink-0" />
                        <span>Ready to upload: <strong class="font-bold">{form.file.name}</strong> ({Math.round(form.file.size / 1024)} KB)</span>
                    </div>
                {/if}
                {#if form.errors.file}
                    <p class="text-xs text-red-600">{form.errors.file}</p>
                {/if}
            </div>

            <!-- File Name -->
            <div class="space-y-1.5">
                <label for="file_name" class="block text-sm font-semibold text-neutral-900 dark:text-white">Display Name</label>
                <input 
                    id="file_name"
                    type="text" 
                    required
                    bind:value={form.file_name}
                    class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                    placeholder="e.g. Student Handbook 2026"
                />
                {#if form.errors.file_name}
                    <p class="text-xs text-red-600">{form.errors.file_name}</p>
                {/if}
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Directory / Category -->
                <div class="space-y-1.5">
                    <label for="directory" class="block text-sm font-semibold text-neutral-900 dark:text-white">Category / Subdirectory</label>
                    <div class="relative">
                        <Folder class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-neutral-400" />
                        <input 
                            id="directory"
                            type="text" 
                            list="directory-list"
                            bind:value={form.directory}
                            class="w-full rounded-xl border border-neutral-200 pl-10 pr-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                            placeholder="e.g. Handbook, Enrollment, Grades"
                        />
                        <datalist id="directory-list">
                            {#each customDirectories as cd}
                                <option value={cd.name}></option>
                            {/each}
                        </datalist>
                    </div>
                    {#if form.errors.directory}
                        <p class="text-xs text-red-600">{form.errors.directory}</p>
                    {/if}
                </div>

                <!-- Department -->
                <div class="space-y-1.5">
                    <label for="department" class="block text-sm font-semibold text-neutral-900 dark:text-white">Owning Department</label>
                    <select 
                        id="department"
                        bind:value={form.department_id}
                        class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                    >
                        <option value="">General (No specific department)</option>
                        {#each departments as dept}
                            <option value={dept.id}>{dept.name}</option>
                        {/each}
                    </select>
                    {#if form.errors.department_id}
                        <p class="text-xs text-red-600">{form.errors.department_id}</p>
                    {/if}
                </div>
            </div>

            <!-- Tags -->
            <div class="space-y-1.5">
                <label for="tags" class="block text-sm font-semibold text-neutral-900 dark:text-white">Tags (Comma-separated)</label>
                <div class="relative">
                    <Tag class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-neutral-400" />
                    <input 
                        id="tags"
                        type="text" 
                        bind:value={form.tags}
                        class="w-full rounded-xl border border-neutral-200 pl-10 pr-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                        placeholder="e.g. freshman, manual, 2026"
                    />
                </div>
                {#if form.errors.tags}
                    <p class="text-xs text-red-600">{form.errors.tags}</p>
                {/if}
            </div>

            <!-- Submit buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t border-neutral-100 dark:border-neutral-800">
                <a 
                    href="/admin/downloads"
                    class="rounded-xl border border-neutral-200 px-5 py-2.5 text-sm font-semibold text-neutral-600 hover:bg-neutral-50 dark:border-neutral-850 dark:text-neutral-400 dark:hover:bg-neutral-900"
                >
                    Cancel
                </a>
                <button 
                    type="submit" 
                    disabled={form.processing}
                    class="rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-700 flex items-center gap-1.5"
                >
                    <Save class="h-4 w-4" /> {form.processing ? 'Uploading...' : 'Upload & Publish'}
                </button>
            </div>
        </form>
    </div>
</div>
