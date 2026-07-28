<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Downloads Studio', href: '/admin/downloads' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { Link, router } from '@inertiajs/svelte';
    import { 
        Trash, 
        FileText, 
        Upload, 
        Folder, 
        FolderPlus,
        Tag, 
        Search, 
        ArrowUpDown, 
        RefreshCw, 
        LayoutGrid, 
        List, 
        ExternalLink, 
        Copy, 
        Check, 
        Info, 
        X, 
        HardDrive, 
        FileSpreadsheet, 
        FileImage, 
        FileArchive, 
        File 
    } from 'lucide-svelte';

    let {
        downloads = [],
        departments = [],
        customDirectories = [],
    }: {
        downloads?: any[];
        departments?: any[];
        customDirectories?: any[];
    } = $props();

    // View & Filter State
    let viewMode = $state<'grid' | 'list'>('grid');
    let searchQuery = $state('');
    let filterDept = $state('');
    let filterDir = $state('');
    let sortField = $state('created_at');
    let sortDirection = $state<'asc' | 'desc'>('desc');

    // Selected file for detail inspector panel
    let selectedFile = $state<any | null>(null);
    let isCopied = $state(false);

    // New Folder Modal state
    let isNewFolderModalOpen = $state(false);
    let newFolderName = $state('');
    let newFolderDepartmentId = $state('');

    function formatBytes(bytes: number, decimals = 2) {
        if (!bytes || bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    function parseTags(tags: any): string[] {
        if (!tags) return [];
        if (Array.isArray(tags)) return tags.map(t => String(t).trim()).filter(Boolean);
        if (typeof tags === 'string') return tags.split(',').map(t => t.trim()).filter(Boolean);
        return [];
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

    function deleteFile(id: number, name: string) {
        if (confirm(`Are you sure you want to delete "${name}"? This action cannot be undone.`)) {
            if (selectedFile?.id === id) {
                selectedFile = null;
            }
            router.delete(`/admin/downloads/${id}`);
        }
    }

    function copyShareLink(url: string) {
        const fullUrl = window.location.origin + url;
        navigator.clipboard.writeText(fullUrl);
        isCopied = true;
        setTimeout(() => { isCopied = false; }, 2000);
    }

    function toggleSort(field: string) {
        if (sortField === field) {
            sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            sortField = field;
            sortDirection = 'desc';
        }
    }

    function submitNewFolder(e: SubmitEvent) {
        e.preventDefault();
        if (!newFolderName.trim()) return;

        router.post('/admin/downloads/directories', {
            name: newFolderName.trim(),
            department_id: newFolderDepartmentId || null,
        }, {
            onSuccess: () => {
                isNewFolderModalOpen = false;
                newFolderName = '';
                newFolderDepartmentId = '';
            }
        });
    }

    function deleteCustomFolder(e: MouseEvent, folderId: number, folderName: string) {
        e.stopPropagation();
        if (confirm(`Delete folder "${folderName}"? Associated files will remain under root.`)) {
            if (filterDir === folderName) {
                filterDir = '';
            }
            router.delete(`/admin/downloads/directories/${folderId}`);
        }
    }

    // Extracted & persistent custom directories
    const directoriesWithCount = $derived.by(() => {
        const map = new Map<string, { count: number; customId?: number }>();
        
        customDirectories.forEach(cd => {
            map.set(cd.name, { count: 0, customId: cd.id });
        });

        downloads.forEach(d => {
            const dir = d.directory || 'Uncategorized';
            const existing = map.get(dir) || { count: 0 };
            map.set(dir, { count: existing.count + 1, customId: existing.customId });
        });

        return Array.from(map.entries()).map(([name, data]) => ({ 
            name, 
            count: data.count,
            customId: data.customId
        }));
    });

    // Total size calculation
    const totalStorageBytes = $derived(downloads.reduce((acc, curr) => acc + (curr.file_size || 0), 0));

    // Computed filtered & sorted list
    const filteredDownloads = $derived.by(() => {
        let result = [...downloads];

        if (searchQuery.trim()) {
            const q = searchQuery.toLowerCase();
            result = result.filter(d => 
                d.file_name.toLowerCase().includes(q) ||
                (d.directory && d.directory.toLowerCase().includes(q)) ||
                parseTags(d.tags).some(t => t.toLowerCase().includes(q))
            );
        }

        if (filterDept) {
            result = result.filter(d => String(d.department_id) === filterDept);
        }

        if (filterDir) {
            if (filterDir === 'Uncategorized') {
                result = result.filter(d => !d.directory);
            } else {
                result = result.filter(d => d.directory === filterDir);
            }
        }

        result.sort((a, b) => {
            let valA = a[sortField];
            let valB = b[sortField];

            if (sortField === 'department') {
                valA = a.department?.name || '';
                valB = b.department?.name || '';
            }

            if (valA === undefined || valA === null) valA = '';
            if (valB === undefined || valB === null) valB = '';

            if (typeof valA === 'string') {
                return sortDirection === 'asc' ? valA.localeCompare(valB) : valB.localeCompare(valA);
            }

            if (valA < valB) return sortDirection === 'asc' ? -1 : 1;
            if (valA > valB) return sortDirection === 'asc' ? 1 : -1;
            return 0;
        });

        return result;
    });

    function resetFilters() {
        searchQuery = '';
        filterDept = '';
        filterDir = '';
        sortField = 'created_at';
        sortDirection = 'desc';
    }
</script>

<AppHead title="Google Drive File Manager" />

<div class="flex h-full w-full flex-col gap-6 p-4 md:p-6 max-w-7xl mx-auto">
    <!-- Google Drive Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-neutral-200/80 pb-4 dark:border-neutral-800">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400 border border-amber-500/20 shadow-sm">
                <HardDrive class="h-5 w-5" />
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-bold tracking-tight text-neutral-900 dark:text-white font-serif flex items-center gap-2">
                    Drive Storage
                    <span class="rounded-full bg-neutral-100 px-2.5 py-0.5 text-xs font-sans font-medium text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                        {downloads.length} Files
                    </span>
                </h1>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                    Total Storage Used: <span class="font-semibold text-amber-600 dark:text-amber-400">{formatBytes(totalStorageBytes)}</span> across all departments.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <!-- View Mode Switcher -->
            <div class="flex items-center rounded-xl border border-neutral-200 bg-neutral-50 p-1 dark:border-neutral-800 dark:bg-neutral-900">
                <button
                    type="button"
                    onclick={() => viewMode = 'grid'}
                    class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition-all {viewMode === 'grid' ? 'bg-white text-neutral-900 shadow-sm dark:bg-neutral-800 dark:text-white' : 'text-neutral-500 hover:text-neutral-900 dark:hover:text-white'}"
                >
                    <LayoutGrid class="h-3.5 w-3.5" /> Grid
                </button>
                <button
                    type="button"
                    onclick={() => viewMode = 'list'}
                    class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition-all {viewMode === 'list' ? 'bg-white text-neutral-900 shadow-sm dark:bg-neutral-800 dark:text-white' : 'text-neutral-500 hover:text-neutral-900 dark:hover:text-white'}"
                >
                    <List class="h-3.5 w-3.5" /> List
                </button>
            </div>

            <!-- New Folder Button -->
            <button
                type="button"
                onclick={() => isNewFolderModalOpen = true}
                class="inline-flex items-center gap-2 rounded-xl border border-neutral-200 bg-white px-3.5 py-2.5 text-sm font-semibold text-neutral-800 shadow-xs hover:bg-neutral-50 dark:border-neutral-800 dark:bg-[#161615] dark:text-white dark:hover:bg-neutral-900 transition-all cursor-pointer"
            >
                <FolderPlus class="h-4 w-4 text-amber-500" /> New Folder
            </button>

            <!-- Upload Button -->
            <Link
                href="/admin/downloads/create"
                class="inline-flex items-center gap-2 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-amber-600/20 hover:bg-amber-700 transition-all cursor-pointer"
            >
                <Upload class="h-4 w-4" /> Upload File
            </Link>
        </div>
    </div>

    <!-- Search & Filters Toolbar -->
    <div class="flex flex-col md:flex-row gap-3 items-center justify-between">
        <!-- Search bar -->
        <div class="relative w-full md:max-w-md">
            <Search class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-neutral-400" />
            <input 
                type="text" 
                placeholder="Search in Drive (filename, category, tags)..." 
                bind:value={searchQuery}
                class="w-full rounded-xl border border-neutral-200 bg-white pl-10 pr-4 py-2 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#161615] shadow-xs"
            />
            {#if searchQuery}
                <button 
                    onclick={() => searchQuery = ''}
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200"
                >
                    <X class="h-4 w-4" />
                </button>
            {/if}
        </div>

        <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto justify-end">
            <!-- Department Filter -->
            <select 
                bind:value={filterDept}
                class="rounded-xl border border-neutral-200 bg-white px-3 py-2 text-xs outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#161615] font-medium text-neutral-700 dark:text-neutral-200"
            >
                <option value="">All Departments</option>
                {#each departments as dept}
                    <option value={String(dept.id)}>{dept.name}</option>
                {/each}
            </select>

            <!-- Directory Filter -->
            <select 
                bind:value={filterDir}
                class="rounded-xl border border-neutral-200 bg-white px-3 py-2 text-xs outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#161615] font-medium text-neutral-700 dark:text-neutral-200"
            >
                <option value="">All Categories</option>
                {#each directoriesWithCount as dir}
                    <option value={dir.name}>{dir.name} ({dir.count})</option>
                {/each}
            </select>

            <!-- Reset Filters -->
            <button 
                onclick={resetFilters}
                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl border border-neutral-200 hover:bg-neutral-50 dark:border-neutral-800 dark:hover:bg-neutral-900 text-neutral-500 transition-colors"
                title="Reset filters"
            >
                <RefreshCw class="h-3 w-3" /> Reset
            </button>
        </div>
    </div>

    <!-- Folders Directory Deck -->
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <span class="block text-xs font-bold uppercase tracking-wider text-neutral-400">Drive Directories / Folders</span>
            <button 
                type="button" 
                onclick={() => isNewFolderModalOpen = true}
                class="text-xs font-semibold text-amber-600 hover:text-amber-700 dark:text-amber-400 flex items-center gap-1 cursor-pointer"
            >
                <FolderPlus class="h-3.5 w-3.5" /> + New Folder
            </button>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            <button
                type="button"
                onclick={() => filterDir = ''}
                class="flex items-center gap-3 rounded-xl border p-3 text-left transition-all cursor-pointer {filterDir === '' ? 'border-amber-500 bg-amber-500/10 dark:bg-amber-500/20' : 'border-neutral-200 bg-white hover:border-amber-400 dark:border-neutral-800 dark:bg-[#161615]'}"
            >
                <Folder class="h-5 w-5 text-amber-500 shrink-0 fill-amber-500/20" />
                <div class="truncate">
                    <span class="block font-semibold text-xs text-neutral-900 dark:text-white truncate">All Files</span>
                    <span class="block text-[10px] text-neutral-500 dark:text-neutral-400">{downloads.length} items</span>
                </div>
            </button>

            {#each directoriesWithCount as dir}
                <div 
                    class="group relative flex items-center justify-between rounded-xl border p-3 text-left transition-all cursor-pointer {filterDir === dir.name ? 'border-amber-500 bg-amber-500/10 dark:bg-amber-500/20' : 'border-neutral-200 bg-white hover:border-amber-400 dark:border-neutral-800 dark:bg-[#161615]'}"
                    role="button"
                    tabindex="0"
                    onclick={() => filterDir = dir.name}
                    onkeydown={(e) => e.key === 'Enter' && (filterDir = dir.name)}
                >
                    <div class="flex items-center gap-3 overflow-hidden">
                        <Folder class="h-5 w-5 text-amber-500 shrink-0 fill-amber-500/20" />
                        <div class="truncate">
                            <span class="block font-semibold text-xs text-neutral-900 dark:text-white truncate">{dir.name}</span>
                            <span class="block text-[10px] text-neutral-500 dark:text-neutral-400">{dir.count} items</span>
                        </div>
                    </div>

                    {#if dir.customId}
                        <button
                            type="button"
                            onclick={(e) => deleteCustomFolder(e, dir.customId!, dir.name)}
                            class="opacity-0 group-hover:opacity-100 p-1 rounded hover:bg-red-50 text-neutral-400 hover:text-red-600 dark:hover:bg-red-950/30 transition-all shrink-0 ml-1"
                            title="Delete Folder"
                        >
                            <Trash class="h-3.5 w-3.5" />
                        </button>
                    {/if}
                </div>
            {/each}
        </div>
    </div>

    <!-- Main File Display Section -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-neutral-400">
                Files ({filteredDownloads.length})
            </span>

            {#if filteredDownloads.length > 0}
                <div class="flex items-center gap-2 text-xs text-neutral-500">
                    <span>Sort:</span>
                    <button 
                        onclick={() => toggleSort('file_name')} 
                        class="hover:text-amber-600 font-medium flex items-center gap-0.5 {sortField === 'file_name' ? 'text-amber-600 font-bold' : ''}"
                    >
                        Name
                    </button>
                    <span>•</span>
                    <button 
                        onclick={() => toggleSort('created_at')} 
                        class="hover:text-amber-600 font-medium flex items-center gap-0.5 {sortField === 'created_at' ? 'text-amber-600 font-bold' : ''}"
                    >
                        Date
                    </button>
                    <span>•</span>
                    <button 
                        onclick={() => toggleSort('file_size')} 
                        class="hover:text-amber-600 font-medium flex items-center gap-0.5 {sortField === 'file_size' ? 'text-amber-600 font-bold' : ''}"
                    >
                        Size
                    </button>
                </div>
            {/if}
        </div>

        {#if filteredDownloads.length === 0}
            <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-neutral-200 p-12 text-center dark:border-neutral-800 bg-white dark:bg-[#161615]">
                <HardDrive class="h-12 w-12 text-neutral-300 dark:text-neutral-700 mb-3" />
                <h3 class="text-base font-semibold text-neutral-900 dark:text-white">No documents found</h3>
                <p class="text-xs text-neutral-500 mt-1 max-w-sm">No files match your search filter criteria. Try clearing search or upload a new file.</p>
                <button onclick={resetFilters} class="mt-4 rounded-xl border border-neutral-200 px-4 py-2 text-xs font-semibold hover:bg-neutral-50 dark:border-neutral-800 dark:hover:bg-neutral-900">
                    Clear Filters
                </button>
            </div>
        {:else if viewMode === 'grid'}
            <!-- Google Drive GRID VIEW -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                {#each filteredDownloads as download}
                    {@const typeInfo = getFileTypeInfo(download.file_name)}
                    <div 
                        class="group relative flex flex-col justify-between rounded-2xl border border-neutral-200/80 bg-white p-4 shadow-sm transition-all hover:border-amber-500/50 hover:shadow-md dark:border-neutral-800 dark:bg-[#161615]"
                    >
                        <!-- Card Header -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 overflow-hidden">
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
                                <div class="overflow-hidden">
                                    <a 
                                        href={download.file_path} 
                                        target="_blank" 
                                        class="block font-bold text-sm text-neutral-900 hover:text-amber-600 dark:text-white truncate"
                                        title={download.file_name}
                                    >
                                        {download.file_name}
                                    </a>
                                    <span class="block text-[11px] text-neutral-400 font-mono mt-0.5">
                                        {formatBytes(download.file_size)}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Body / Meta -->
                        <div class="my-3 space-y-2 pt-2 border-t border-neutral-100 dark:border-neutral-800/60">
                            <div class="flex items-center justify-between text-xs">
                                {#if download.directory}
                                    <span class="inline-flex items-center gap-1 rounded-md bg-neutral-100 px-2 py-0.5 text-[10px] font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                                        <Folder class="h-3 w-3 text-amber-500" /> {download.directory}
                                    </span>
                                {:else}
                                    <span class="text-[10px] text-neutral-400 italic">Root</span>
                                {/if}

                                {#if download.department}
                                    <span class="text-[11px] font-semibold text-amber-600 dark:text-amber-400 truncate max-w-[120px]">
                                        {download.department.name}
                                    </span>
                                {:else}
                                    <span class="text-[11px] text-neutral-400">General</span>
                                {/if}
                            </div>

                            {#if parseTags(download.tags).length > 0}
                                <div class="flex flex-wrap gap-1">
                                    {#each parseTags(download.tags).slice(0, 3) as tag}
                                        <span class="inline-flex items-center gap-0.5 rounded bg-neutral-50 px-1.5 py-0.5 text-[10px] text-neutral-500 dark:bg-neutral-900 dark:text-neutral-400 border border-neutral-100 dark:border-neutral-800">
                                            <Tag class="h-2.5 w-2.5" /> {tag}
                                        </span>
                                    {/each}
                                </div>
                            {/if}
                        </div>

                        <!-- Card Quick Action Footer -->
                        <div class="flex items-center justify-between pt-2 border-t border-neutral-100 dark:border-neutral-800/60 text-xs">
                            <span class="text-[10px] text-neutral-400">
                                {new Date(download.created_at).toLocaleDateString(undefined, { month: 'short', day: 'numeric' })}
                            </span>

                            <div class="flex items-center gap-1">
                                <button
                                    type="button"
                                    onclick={() => selectedFile = download}
                                    class="p-1.5 rounded-lg text-neutral-400 hover:bg-neutral-100 hover:text-neutral-800 dark:hover:bg-neutral-800 dark:hover:text-white transition-colors"
                                    title="File Info & Link"
                                >
                                    <Info class="h-4 w-4" />
                                </button>
                                <a
                                    href={download.file_path}
                                    target="_blank"
                                    class="p-1.5 rounded-lg text-neutral-400 hover:bg-amber-50 hover:text-amber-600 dark:hover:bg-amber-950/30 transition-colors"
                                    title="Open / Download"
                                >
                                    <ExternalLink class="h-4 w-4" />
                                </a>
                                <button
                                    type="button"
                                    onclick={() => deleteFile(download.id, download.file_name)}
                                    class="p-1.5 rounded-lg text-neutral-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/30 transition-colors"
                                    title="Delete File"
                                >
                                    <Trash class="h-4 w-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                {/each}
            </div>
        {:else}
            <!-- Google Drive LIST VIEW -->
            <div class="rounded-2xl border border-neutral-200/80 bg-white shadow-sm dark:border-neutral-800 dark:bg-[#161615] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-neutral-200/80 bg-neutral-50/80 text-xs font-bold text-neutral-500 dark:border-neutral-800 dark:bg-[#0a0a0a]">
                                <th class="px-6 py-3.5">
                                    <button onclick={() => toggleSort('file_name')} class="flex items-center gap-1 hover:text-amber-600">
                                        Name <ArrowUpDown class="h-3.5 w-3.5" />
                                    </button>
                                </th>
                                <th class="px-6 py-3.5">Category</th>
                                <th class="px-6 py-3.5">Department</th>
                                <th class="px-6 py-3.5">
                                    <button onclick={() => toggleSort('file_size')} class="flex items-center gap-1 hover:text-amber-600">
                                        Size <ArrowUpDown class="h-3.5 w-3.5" />
                                    </button>
                                </th>
                                <th class="px-6 py-3.5">Date Modified</th>
                                <th class="px-6 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 text-sm text-neutral-600 dark:divide-neutral-800 dark:text-neutral-350">
                            {#each filteredDownloads as download}
                                {@const typeInfo = getFileTypeInfo(download.file_name)}
                                <tr class="hover:bg-neutral-50/60 dark:hover:bg-neutral-900/40 transition-colors">
                                    <!-- Name -->
                                    <td class="px-6 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border font-bold text-[10px] {typeInfo.bg}">
                                                {#if typeInfo.type === 'sheet'}
                                                    <FileSpreadsheet class="h-4 w-4" />
                                                {:else if typeInfo.type === 'image'}
                                                    <FileImage class="h-4 w-4" />
                                                {:else if typeInfo.type === 'archive'}
                                                    <FileArchive class="h-4 w-4" />
                                                {:else if typeInfo.type === 'pdf' || typeInfo.type === 'doc'}
                                                    <FileText class="h-4 w-4" />
                                                {:else}
                                                    <File class="h-4 w-4" />
                                                {/if}
                                            </div>
                                            <div>
                                                <a href={download.file_path} target="_blank" class="font-bold text-neutral-900 hover:text-amber-600 dark:text-white">
                                                    {download.file_name}
                                                </a>
                                                {#if parseTags(download.tags).length > 0}
                                                    <div class="flex flex-wrap gap-1 mt-0.5">
                                                        {#each parseTags(download.tags) as tag}
                                                            <span class="text-[10px] text-neutral-400">#{tag}</span>
                                                        {/each}
                                                    </div>
                                                {/if}
                                            </div>
                                        </div>
                                    </td>
                                    <!-- Category -->
                                    <td class="px-6 py-3.5">
                                        {#if download.directory}
                                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-neutral-700 dark:text-neutral-300">
                                                <Folder class="h-3.5 w-3.5 text-amber-500" /> {download.directory}
                                            </span>
                                        {:else}
                                            <span class="text-xs text-neutral-400 italic">Root</span>
                                        {/if}
                                    </td>
                                    <!-- Department -->
                                    <td class="px-6 py-3.5">
                                        {#if download.department}
                                            <span class="text-xs font-semibold text-amber-600 dark:text-amber-400">{download.department.name}</span>
                                        {:else}
                                            <span class="text-xs text-neutral-400">General</span>
                                        {/if}
                                    </td>
                                    <!-- Size -->
                                    <td class="px-6 py-3.5 font-mono text-xs">
                                        {formatBytes(download.file_size)}
                                    </td>
                                    <!-- Date -->
                                    <td class="px-6 py-3.5 text-xs text-neutral-500">
                                        {new Date(download.created_at).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' })}
                                    </td>
                                    <!-- Actions -->
                                    <td class="px-6 py-3.5 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <button
                                                type="button"
                                                onclick={() => selectedFile = download}
                                                class="p-1.5 rounded-lg text-neutral-400 hover:bg-neutral-100 hover:text-neutral-800 dark:hover:bg-neutral-800 dark:hover:text-white"
                                                title="File Info"
                                            >
                                                <Info class="h-4 w-4" />
                                            </button>
                                            <a 
                                                href={download.file_path} 
                                                target="_blank"
                                                class="p-1.5 rounded-lg text-neutral-400 hover:bg-amber-50 hover:text-amber-600 dark:hover:bg-amber-950/30"
                                                title="Open Document"
                                            >
                                                <ExternalLink class="h-4 w-4" />
                                            </a>
                                            <button 
                                                onclick={() => deleteFile(download.id, download.file_name)}
                                                class="p-1.5 rounded-lg text-neutral-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/30"
                                                title="Delete Document"
                                            >
                                                <Trash class="h-4 w-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>
            </div>
        {/if}
    </div>
</div>

<!-- New Folder Modal -->
{#if isNewFolderModalOpen}
    <div 
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 p-4 backdrop-blur-xs"
        role="presentation"
        onclick={() => isNewFolderModalOpen = false}
        onkeydown={(e) => e.key === 'Escape' && (isNewFolderModalOpen = false)}
    >
        <div 
            class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-[#161615] border border-neutral-200 dark:border-neutral-800 space-y-4"
            role="presentation"
            onclick={(e) => e.stopPropagation()}
            onkeydown={(e) => e.stopPropagation()}
        >
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <FolderPlus class="h-5 w-5 text-amber-500" />
                    <h3 class="font-bold text-lg text-neutral-900 dark:text-white font-serif">New Folder</h3>
                </div>
                <button type="button" onclick={() => isNewFolderModalOpen = false} class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200">
                    <X class="h-5 w-5" />
                </button>
            </div>

            <form onsubmit={submitNewFolder} class="space-y-4">
                <div class="space-y-1.5">
                    <label for="folder_name" class="block text-xs font-semibold uppercase tracking-wider text-neutral-500">Folder Name</label>
                    <input 
                        id="folder_name" 
                        type="text" 
                        required 
                        placeholder="e.g. Student Handbooks, Forms 2026"
                        bind:value={newFolderName}
                        class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                    />
                </div>

                <div class="space-y-1.5">
                    <label for="folder_dept" class="block text-xs font-semibold uppercase tracking-wider text-neutral-500">Owning Department (Optional)</label>
                    <select
                        id="folder_dept"
                        bind:value={newFolderDepartmentId}
                        class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                    >
                        <option value="">General (All Departments)</option>
                        {#each departments as dept}
                            <option value={dept.id}>{dept.name}</option>
                        {/each}
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button 
                        type="button" 
                        onclick={() => isNewFolderModalOpen = false} 
                        class="rounded-xl border border-neutral-200 px-4 py-2 text-xs font-semibold text-neutral-600 hover:bg-neutral-50 dark:border-neutral-800 dark:text-neutral-400"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="rounded-xl bg-amber-600 px-5 py-2 text-xs font-semibold text-white hover:bg-amber-700 shadow-md shadow-amber-600/20 transition-all"
                    >
                        Create Folder
                    </button>
                </div>
            </form>
        </div>
    </div>
{/if}

<!-- Google Drive File Details Side Modal / Drawer -->
{#if selectedFile}
    {@const typeInfo = getFileTypeInfo(selectedFile.file_name)}
    <div 
        class="fixed inset-0 z-[9999] flex justify-end bg-black/50 backdrop-blur-xs transition-opacity"
        role="presentation"
        onclick={() => selectedFile = null}
        onkeydown={(e) => e.key === 'Escape' && (selectedFile = null)}
    >
        <div 
            class="h-full w-full max-w-md bg-white p-6 shadow-2xl dark:bg-[#161615] flex flex-col justify-between border-l border-neutral-200 dark:border-neutral-800 overflow-y-auto"
            role="presentation"
            onclick={(e) => e.stopPropagation()}
            onkeydown={(e) => e.stopPropagation()}
        >
            <div class="space-y-6">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-neutral-100 pb-4 dark:border-neutral-800">
                    <div class="flex items-center gap-2">
                        <Info class="h-5 w-5 text-amber-600" />
                        <h3 class="font-bold text-lg text-neutral-900 dark:text-white font-serif">File Details</h3>
                    </div>
                    <button 
                        type="button" 
                        onclick={() => selectedFile = null}
                        class="rounded-lg p-1 text-neutral-400 hover:bg-neutral-100 hover:text-neutral-800 dark:hover:bg-neutral-800 dark:hover:text-white"
                    >
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <!-- Large Preview Icon Box -->
                <div class="flex flex-col items-center justify-center rounded-2xl border p-8 text-center space-y-3 {typeInfo.bg}">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-md dark:bg-neutral-900">
                        {#if typeInfo.type === 'sheet'}
                            <FileSpreadsheet class="h-8 w-8 text-emerald-500" />
                        {:else if typeInfo.type === 'image'}
                            <FileImage class="h-8 w-8 text-purple-500" />
                        {:else if typeInfo.type === 'archive'}
                            <FileArchive class="h-8 w-8 text-amber-500" />
                        {:else if typeInfo.type === 'pdf'}
                            <FileText class="h-8 w-8 text-red-500" />
                        {:else}
                            <FileText class="h-8 w-8 text-blue-500" />
                        {/if}
                    </div>
                    <div>
                        <h4 class="font-bold text-base text-neutral-900 dark:text-white line-clamp-2">{selectedFile.file_name}</h4>
                        <span class="text-xs font-mono font-medium text-neutral-500">{formatBytes(selectedFile.file_size)} • {typeInfo.ext}</span>
                    </div>
                </div>

                <!-- Attributes Table -->
                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between py-2 border-b border-neutral-100 dark:border-neutral-800">
                        <span class="text-neutral-400 font-medium">Category / Folder:</span>
                        <span class="font-semibold text-neutral-900 dark:text-white flex items-center gap-1">
                            <Folder class="h-3.5 w-3.5 text-amber-500" /> {selectedFile.directory || 'Root / General'}
                        </span>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b border-neutral-100 dark:border-neutral-800">
                        <span class="text-neutral-400 font-medium">Owning Department:</span>
                        <span class="font-semibold text-amber-600 dark:text-amber-400">
                            {selectedFile.department?.name || 'General Public'}
                        </span>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b border-neutral-100 dark:border-neutral-800">
                        <span class="text-neutral-400 font-medium">Uploaded Date:</span>
                        <span class="font-mono text-neutral-700 dark:text-neutral-300">
                            {new Date(selectedFile.created_at).toLocaleString()}
                        </span>
                    </div>

                    {#if parseTags(selectedFile.tags).length > 0}
                        <div class="py-2 border-b border-neutral-100 dark:border-neutral-800 space-y-1.5">
                            <span class="text-neutral-400 font-medium block">File Tags:</span>
                            <div class="flex flex-wrap gap-1">
                                {#each parseTags(selectedFile.tags) as tag}
                                    <span class="inline-flex items-center gap-1 rounded bg-neutral-100 px-2 py-0.5 text-xs font-medium text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                                        <Tag class="h-3 w-3" /> {tag}
                                    </span>
                                {/each}
                            </div>
                        </div>
                    {/if}

                    <div class="space-y-1.5 pt-2">
                        <span class="text-neutral-400 font-medium block">Direct Access URL:</span>
                        <div class="flex items-center gap-2">
                            <input 
                                type="text" 
                                readonly 
                                value={window.location.origin + selectedFile.file_path} 
                                class="w-full rounded-lg border border-neutral-200 bg-neutral-50 px-3 py-1.5 text-xs font-mono text-neutral-600 outline-none dark:border-neutral-800 dark:bg-[#0a0a0a] dark:text-neutral-400"
                            />
                            <button
                                type="button"
                                onclick={() => copyShareLink(selectedFile.file_path)}
                                class="inline-flex items-center gap-1 shrink-0 rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-amber-700 transition-colors"
                            >
                                {#if isCopied}
                                    <Check class="h-3.5 w-3.5 text-white" /> Copied
                                {:else}
                                    <Copy class="h-3.5 w-3.5" /> Copy
                                {/if}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-neutral-100 dark:border-neutral-800">
                <button
                    type="button"
                    onclick={() => deleteFile(selectedFile.id, selectedFile.file_name)}
                    class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-xs font-semibold text-red-600 hover:bg-red-100 dark:border-red-900/40 dark:bg-red-950/20 dark:text-red-400 transition-colors"
                >
                    <Trash class="h-4 w-4" /> Delete File
                </button>

                <a 
                    href={selectedFile.file_path}
                    target="_blank"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-amber-600 px-4 py-2 text-xs font-semibold text-white hover:bg-amber-700 shadow-md shadow-amber-600/20 transition-all"
                >
                    <ExternalLink class="h-4 w-4" /> Open File
                </a>
            </div>
        </div>
    </div>
{/if}
