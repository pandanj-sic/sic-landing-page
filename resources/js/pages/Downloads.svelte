<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import PublicLayout from '@/components/PublicLayout.svelte';
    import { router } from '@inertiajs/svelte';
    import { 
        Download as DownloadIcon, 
        FileText, 
        Search, 
        Tag, 
        Folder, 
        FolderOpen,
        ChevronRight, 
        X, 
        HardDrive, 
        FileSpreadsheet, 
        FileImage, 
        FileArchive, 
        File,
        RefreshCw
    } from 'lucide-svelte';
    import { untrack } from 'svelte';

    let {
        downloads = [],
        departments = [],
        customDirectories = [],
        navigationLinks = [],
        filters = {},
    }: {
        downloads?: any[];
        departments?: any[];
        customDirectories?: any[];
        navigationLinks?: any[];
        filters?: any;
    } = $props();

    let search = $state(untrack(() => filters.search || ''));
    let departmentId = $state(untrack(() => filters.department_id || ''));
    let selectedDirectory = $state<string>('');

    function formatBytes(bytes: number, decimals = 2) {
        if (!bytes || bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    function getFileTypeInfo(fileName: string) {
        const ext = fileName.split('.').pop()?.toLowerCase() || '';
        if (['pdf'].includes(ext)) {
            return { ext: 'PDF', bg: 'bg-red-500/10 text-red-600 border-red-500/20 dark:bg-red-500/20 dark:text-red-400', type: 'pdf' };
        }
        if (['doc', 'docx', 'txt', 'rtf'].includes(ext)) {
            return { ext: 'DOC', bg: 'bg-blue-500/10 text-blue-600 border-blue-500/20 dark:bg-blue-500/20 dark:text-blue-400', type: 'doc' };
        }
        if (['xls', 'xlsx', 'csv'].includes(ext)) {
            return { ext: 'SHEET', bg: 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20 dark:bg-emerald-500/20 dark:text-emerald-400', type: 'sheet' };
        }
        if (['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'].includes(ext)) {
            return { ext: 'IMAGE', bg: 'bg-purple-500/10 text-purple-600 border-purple-500/20 dark:bg-purple-500/20 dark:text-purple-400', type: 'image' };
        }
        if (['zip', 'rar', '7z', 'tar', 'gz'].includes(ext)) {
            return { ext: 'ARCHIVE', bg: 'bg-amber-500/10 text-amber-600 border-amber-500/20 dark:bg-amber-500/20 dark:text-amber-400', type: 'archive' };
        }
        return { ext: ext.toUpperCase() || 'FILE', bg: 'bg-neutral-500/10 text-neutral-600 border-neutral-500/20 dark:bg-neutral-500/20 dark:text-neutral-400', type: 'file' };
    }

    function handleFilter() {
        router.get('/downloads', {
            search,
            department_id: departmentId
        }, {
            preserveState: true,
            replace: true
        });
    }

    function selectTag(tag: string) {
        search = tag;
        handleFilter();
    }

    // Merge persistent directories & directories found on files
    const directoriesWithCount = $derived.by(() => {
        const map = new Map<string, number>();

        customDirectories.forEach(cd => {
            map.set(cd.name, 0);
        });

        downloads.forEach(d => {
            const dir = d.directory || 'Uncategorized';
            map.set(dir, (map.get(dir) || 0) + 1);
        });

        return Array.from(map.entries()).map(([name, count]) => ({ name, count }));
    });

    // Filter downloads by search, department, and active directory folder
    const filteredDownloads = $derived.by(() => {
        let list = [...downloads];

        if (selectedDirectory) {
            if (selectedDirectory === 'Uncategorized') {
                list = list.filter(d => !d.directory);
            } else {
                list = list.filter(d => d.directory === selectedDirectory);
            }
        }

        return list;
    });

    function resetDirectoryFilter() {
        selectedDirectory = '';
        search = '';
        departmentId = '';
        handleFilter();
    }
</script>

<AppHead title="Downloads Directory Portal - San Isidro College" />

<PublicLayout {navigationLinks} {departments}>
    <!-- Page Hero Banner -->
    <div class="bg-gradient-to-b from-amber-500/5 via-neutral-50 to-neutral-50 py-12 dark:from-amber-500/10 dark:via-[#161615] dark:to-[#161615]">
        <div class="mx-auto max-w-7xl px-6 space-y-4">
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                <HardDrive class="h-4 w-4" /> Downloads Directory Portal
            </div>
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-neutral-900 dark:text-white font-serif">
                School Document & File Directory
            </h1>
            <p class="text-sm md:text-base text-neutral-500 dark:text-neutral-400 max-w-2xl">
                Browse through official folders, download forms, academic guides, curriculum checklists, and department schedules.
            </p>
        </div>
    </div>

    <section class="py-8 bg-neutral-50/50 dark:bg-[#161615]/50 min-h-[60vh]">
        <div class="mx-auto max-w-7xl px-6 space-y-8">
            <!-- Search & Department Bar -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center justify-between rounded-2xl border border-neutral-200/80 bg-white p-4 shadow-sm dark:border-neutral-800 dark:bg-[#161615]">
                <!-- Search input -->
                <div class="relative flex-1">
                    <Search class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-400" />
                    <input 
                        type="text" 
                        bind:value={search} 
                        oninput={handleFilter}
                        placeholder="Search document name, tags, or folder..." 
                        class="w-full rounded-xl border border-neutral-200 py-2.5 pl-10 pr-4 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                    />
                    {#if search}
                        <button 
                            onclick={() => { search = ''; handleFilter(); }}
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200"
                        >
                            <X class="h-4 w-4" />
                        </button>
                    {/if}
                </div>

                <!-- Department Selector & Reset -->
                <div class="flex items-center gap-3">
                    <select 
                        bind:value={departmentId} 
                        onchange={handleFilter}
                        class="rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a] text-neutral-700 dark:text-neutral-200 font-medium"
                    >
                        <option value="">All Departments</option>
                        {#each departments as dept}
                            <option value={dept.id}>{dept.name}</option>
                        {/each}
                    </select>

                    {#if search || departmentId || selectedDirectory}
                        <button
                            type="button"
                            onclick={resetDirectoryFilter}
                            class="inline-flex items-center gap-1.5 rounded-xl border border-neutral-200 px-3.5 py-2.5 text-xs font-semibold text-neutral-600 hover:bg-neutral-50 dark:border-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-900 transition-colors"
                        >
                            <RefreshCw class="h-3.5 w-3.5" /> Reset
                        </button>
                    {/if}
                </div>
            </div>

            <!-- Directory Path / Breadcrumb Bar -->
            <div class="flex items-center gap-2 text-xs font-semibold text-neutral-600 dark:text-neutral-400 border-b border-neutral-200/80 pb-3 dark:border-neutral-800">
                <button 
                    type="button" 
                    onclick={() => selectedDirectory = ''}
                    class="hover:text-amber-600 flex items-center gap-1.5 transition-colors {selectedDirectory === '' ? 'text-amber-600 font-bold' : ''}"
                >
                    <HardDrive class="h-4 w-4 text-amber-500" /> Root Directory
                </button>

                {#if selectedDirectory}
                    <ChevronRight class="h-3.5 w-3.5 text-neutral-400" />
                    <span class="inline-flex items-center gap-1 rounded-md bg-amber-500/10 px-2.5 py-1 text-xs font-bold text-amber-600 dark:bg-amber-500/20 dark:text-amber-400 border border-amber-500/20">
                        <FolderOpen class="h-3.5 w-3.5" /> {selectedDirectory}
                    </span>
                    <button 
                        type="button" 
                        onclick={() => selectedDirectory = ''}
                        class="text-neutral-400 hover:text-red-500 ml-1"
                        title="Clear directory filter"
                    >
                        <X class="h-3.5 w-3.5" />
                    </button>
                {/if}
            </div>

            <!-- DIRECTORY / FOLDERS NAVIGATION DECK -->
            {#if directoriesWithCount.length > 0}
                <div class="space-y-3">
                    <span class="block text-xs font-bold uppercase tracking-wider text-neutral-400">Folders Navigation</span>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                        <button
                            type="button"
                            onclick={() => selectedDirectory = ''}
                            class="flex items-center gap-3 rounded-2xl border p-3.5 text-left transition-all cursor-pointer {selectedDirectory === '' ? 'border-amber-500 bg-amber-500/10 dark:bg-amber-500/20 shadow-xs' : 'border-neutral-200 bg-white hover:border-amber-400 dark:border-neutral-800 dark:bg-[#161615]'}"
                        >
                            <Folder class="h-5 w-5 text-amber-500 shrink-0 fill-amber-500/20" />
                            <div class="truncate">
                                <span class="block font-bold text-xs text-neutral-900 dark:text-white truncate">All Files</span>
                                <span class="block text-[10px] text-neutral-500 dark:text-neutral-400">{downloads.length} files</span>
                            </div>
                        </button>

                        {#each directoriesWithCount as dir}
                            <button
                                type="button"
                                onclick={() => selectedDirectory = dir.name}
                                class="flex items-center gap-3 rounded-2xl border p-3.5 text-left transition-all cursor-pointer {selectedDirectory === dir.name ? 'border-amber-500 bg-amber-500/10 dark:bg-amber-500/20 shadow-xs' : 'border-neutral-200 bg-white hover:border-amber-400 dark:border-neutral-800 dark:bg-[#161615]'}"
                            >
                                {#if selectedDirectory === dir.name}
                                    <FolderOpen class="h-5 w-5 text-amber-500 shrink-0 fill-amber-500/30" />
                                {:else}
                                    <Folder class="h-5 w-5 text-amber-500 shrink-0 fill-amber-500/20" />
                                {/if}
                                <div class="truncate">
                                    <span class="block font-bold text-xs text-neutral-900 dark:text-white truncate">{dir.name}</span>
                                    <span class="block text-[10px] text-neutral-500 dark:text-neutral-400">{dir.count} files</span>
                                </div>
                            </button>
                        {/each}
                    </div>
                </div>
            {/if}

            <!-- FILES DISPLAY GRID -->
            <div class="space-y-3 pt-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-neutral-400">
                        {selectedDirectory ? `Files in "${selectedDirectory}"` : 'All Files'} ({filteredDownloads.length})
                    </span>
                </div>

                {#if filteredDownloads.length > 0}
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        {#each filteredDownloads as download}
                            {@const typeInfo = getFileTypeInfo(download.file_name)}
                            <div class="group relative flex flex-col justify-between rounded-2xl border border-neutral-200/80 bg-white p-5 shadow-xs transition-all hover:border-amber-500/40 hover:shadow-md dark:border-neutral-800 dark:bg-[#161615]">
                                <div>
                                    <!-- File Card Header -->
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border font-bold text-xs shadow-xs {typeInfo.bg}">
                                            {#if typeInfo.type === 'sheet'}
                                                <FileSpreadsheet class="h-5 w-5" />
                                            {:else if typeInfo.type === 'image'}
                                                <FileImage class="h-5 w-5" />
                                            {:else if typeInfo.type === 'archive'}
                                                <FileArchive class="h-5 w-5" />
                                            {:else if typeInfo.type === 'pdf' || typeInfo.type === 'doc'}
                                                <FileText class="h-5 w-5" />
                                            {:else}
                                                <File class="h-5 w-5" />
                                            {/if}
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-bold text-sm text-neutral-900 dark:text-white truncate" title={download.file_name}>
                                                {download.file_name}
                                            </h3>
                                            <div class="mt-1 flex items-center gap-2 text-[11px] text-neutral-500 dark:text-neutral-400">
                                                <span class="font-mono">{formatBytes(download.file_size)}</span>
                                                <span>•</span>
                                                <span class="inline-flex items-center gap-1 font-medium text-neutral-600 dark:text-neutral-300">
                                                    <Folder class="h-3 w-3 text-amber-500" /> {download.directory || 'General'}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Department & Tags -->
                                    <div class="mt-4 space-y-2 border-t border-neutral-100 pt-3 dark:border-neutral-800/80">
                                        {#if download.department}
                                            <div class="text-xs">
                                                <span class="text-neutral-400">Department:</span>{' '}
                                                <span class="font-semibold text-amber-600 dark:text-amber-400">{download.department.name}</span>
                                            </div>
                                        {/if}

                                        {#if download.tags && download.tags.length > 0}
                                            <div class="flex flex-wrap gap-1">
                                                {#each download.tags as tag}
                                                    <button 
                                                        onclick={() => selectTag(tag)}
                                                        class="inline-flex items-center gap-0.5 rounded bg-neutral-100 px-1.5 py-0.5 text-[10px] font-medium text-neutral-600 hover:bg-amber-50 hover:text-amber-700 dark:bg-neutral-800 dark:text-neutral-400"
                                                    >
                                                        <Tag class="h-2.5 w-2.5 text-neutral-400" /> {tag}
                                                    </button>
                                                {/each}
                                            </div>
                                        {/if}
                                    </div>
                                </div>

                                <!-- Action Footer -->
                                <div class="mt-5 flex items-center justify-between border-t border-neutral-100 pt-3 dark:border-neutral-800/80">
                                    <span class="text-[10px] text-neutral-400">
                                        {new Date(download.created_at).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' })}
                                    </span>

                                    <a 
                                        href={`/storage/${download.file_path}`} 
                                        download 
                                        target="_blank"
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-amber-600 px-3.5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-amber-700 transition-colors"
                                    >
                                        <DownloadIcon class="h-3.5 w-3.5" /> Download
                                    </a>
                                </div>
                            </div>
                        {/each}
                    </div>
                {:else}
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-neutral-200 bg-white p-12 text-center dark:border-neutral-800 dark:bg-[#161615]">
                        <Folder class="h-10 w-10 text-neutral-300 dark:text-neutral-700 mb-2" />
                        <p class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">No downloadable files found</p>
                        <p class="text-xs text-neutral-400 mt-1 max-w-sm">No files are in this folder matching your search or department filter.</p>
                        <button 
                            type="button" 
                            onclick={resetDirectoryFilter} 
                            class="mt-4 rounded-xl border border-neutral-200 px-4 py-2 text-xs font-semibold hover:bg-neutral-50 dark:border-neutral-800 dark:hover:bg-neutral-900 text-neutral-600 dark:text-neutral-300"
                        >
                            Show All Files
                        </button>
                    </div>
                {/if}
            </div>
        </div>
    </section>
</PublicLayout>
