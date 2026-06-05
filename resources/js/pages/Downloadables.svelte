<script lang="ts">
    import ChevronRight from 'lucide-svelte/icons/chevron-right';
    import Download from 'lucide-svelte/icons/download';
    import Eye from 'lucide-svelte/icons/eye';
    import FileIcon from 'lucide-svelte/icons/file';
    import FileArchive from 'lucide-svelte/icons/file-archive';
    import FileImage from 'lucide-svelte/icons/file-image';
    import FileSpreadsheet from 'lucide-svelte/icons/file-spreadsheet';
    import FileText from 'lucide-svelte/icons/file-text';
    import Folder from 'lucide-svelte/icons/folder';
    import FolderOpen from 'lucide-svelte/icons/folder-open';
    import X from 'lucide-svelte/icons/x';
    import { SvelteMap } from 'svelte/reactivity';

    import AppHead from '@/components/AppHead.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import {
        Collapsible,
        CollapsibleContent,
        CollapsibleTrigger,
    } from '@/components/ui/collapsible';
    import {
        Dialog,
        DialogContent,
        DialogDescription,
        DialogTitle,
    } from '@/components/ui/dialog';
    import { Separator } from '@/components/ui/separator';
    import {
        Tooltip,
        TooltipContent,
        TooltipProvider,
        TooltipTrigger,
    } from '@/components/ui/tooltip';
    import PageLayout from '@/layouts/PageLayout.svelte';
    import type {
        DownloadableFile,
        DownloadableFolder,
    } from '@/types/downloadable';

    let { folders = [] }: { folders: DownloadableFolder[] } = $props();

    let folderOpenStates = new SvelteMap<number, boolean>();

    let previewFile = $state<DownloadableFile | null>(null);

    let previewOpen = $state(false);

    function isFolderOpen(folderId: number): boolean {
        return folderOpenStates.get(folderId) ?? false;
    }

    function toggleFolder(folderId: number) {
        folderOpenStates.set(folderId, !isFolderOpen(folderId));
    }

    function formatFileSize(bytes: number | null): string {
        if (!bytes) {
            return '—';
        }

        const units = ['B', 'KB', 'MB', 'GB'];
        let size = bytes;
        let unitIndex = 0;

        while (size >= 1024 && unitIndex < units.length - 1) {
            size /= 1024;
            unitIndex++;
        }

        return `${size.toFixed(unitIndex === 0 ? 0 : 1)} ${units[unitIndex]}`;
    }

    function getFileIcon(fileType: string | null) {
        if (!fileType) {
            return FileIcon;
        }

        if (fileType.startsWith('image/')) {
            return FileImage;
        }

        if (fileType === 'application/pdf') {
            return FileText;
        }

        if (
            fileType.includes('spreadsheet') ||
            fileType.includes('excel') ||
            fileType === 'application/vnd.ms-excel'
        ) {
            return FileSpreadsheet;
        }

        if (
            fileType.includes('word') ||
            fileType.includes('document') ||
            fileType === 'text/plain'
        ) {
            return FileText;
        }

        if (
            fileType.includes('zip') ||
            fileType.includes('rar') ||
            fileType.includes('archive')
        ) {
            return FileArchive;
        }

        return FileIcon;
    }

    function getFileExtension(filePath: string): string {
        const parts = filePath.split('.');

        return parts.length > 1 ? parts[parts.length - 1].toUpperCase() : '';
    }

    function resolveFileUrl(filePath: string): string {
        return filePath.startsWith('http') ? filePath : `/storage/${filePath}`;
    }

    function isPreviewable(fileType: string | null): boolean {
        if (!fileType) {
            return false;
        }

        return fileType.startsWith('image/') || fileType === 'application/pdf';
    }

    function openPreview(file: DownloadableFile) {
        previewFile = file;
        previewOpen = true;
    }

    function closePreview() {
        previewOpen = false;
        previewFile = null;
    }
</script>

<AppHead title="Downloadables" />

<PageLayout>
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <Badge variant="secondary" class="mb-4">Resources</Badge>
            <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                Downloadables
            </h1>
            <p class="mt-2 text-muted-foreground">
                Browse and download files, forms, and documents from San Isidro
                College.
            </p>
        </div>

        <!-- Folder List -->
        {#if folders.length > 0}
            <div class="space-y-4">
                {#each folders as folder (folder.id)}
                    {@render folderCard(folder, 0)}
                {/each}
            </div>
        {:else}
            <Card class="py-16 text-center">
                <CardContent>
                    <Folder
                        class="mx-auto mb-4 h-12 w-12 text-muted-foreground/40"
                    />
                    <h3 class="text-lg font-semibold">No downloadables yet</h3>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Check back soon for downloadable files and resources.
                    </p>
                </CardContent>
            </Card>
        {/if}
    </section>

    <!-- Preview Dialog -->
    <Dialog
        bind:open={previewOpen}
        onOpenChange={(v) => {
            if (!v) {
                closePreview();
            }
        }}
    >
        <DialogContent
            class="flex max-h-[90vh] max-w-4xl flex-col overflow-hidden"
        >
            {#if previewFile}
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <DialogTitle class="truncate text-lg font-semibold">
                            {previewFile.name}
                        </DialogTitle>
                        {#if previewFile.description}
                            <DialogDescription
                                class="mt-1 text-sm text-muted-foreground"
                            >
                                {previewFile.description}
                            </DialogDescription>
                        {:else}
                            <DialogDescription class="sr-only">
                                Preview of {previewFile.name}
                            </DialogDescription>
                        {/if}
                    </div>
                    <div class="ml-4 flex shrink-0 items-center gap-2">
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        href={`/downloadables/download/${previewFile.id}`}
                                    >
                                        <Download class="mr-1 h-4 w-4" />
                                        Download
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>Download {previewFile.name}</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                        <Button
                            variant="ghost"
                            size="icon"
                            onclick={closePreview}
                        >
                            <X class="h-4 w-4" />
                            <span class="sr-only">Close preview</span>
                        </Button>
                    </div>
                </div>
                <Separator />
                <div class="min-h-0 flex-1 overflow-auto">
                    {#if previewFile.file_type?.startsWith('image/')}
                        <img
                            src={resolveFileUrl(previewFile.file_path)}
                            alt={previewFile.name}
                            class="mx-auto max-h-[70vh] rounded-lg object-contain"
                        />
                    {:else if previewFile.file_type === 'application/pdf'}
                        <iframe
                            src={resolveFileUrl(previewFile.file_path)}
                            title={previewFile.name}
                            class="h-[70vh] w-full rounded-lg border-0"
                        ></iframe>
                    {/if}
                </div>
            {/if}
        </DialogContent>
    </Dialog>
</PageLayout>

{#snippet folderCard(folder: DownloadableFolder, depth: number)}
    <Card
        class="overflow-hidden transition hover:shadow-md {depth > 0
            ? 'ml-6'
            : ''}"
    >
        <Collapsible open={isFolderOpen(folder.id)}>
            <!-- Folder Header -->
            <CollapsibleTrigger
                class="flex w-full items-center gap-3 px-5 py-4 text-left transition hover:bg-muted/50"
                onclick={() => toggleFolder(folder.id)}
            >
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
                >
                    {#if isFolderOpen(folder.id)}
                        <FolderOpen class="h-5 w-5" />
                    {:else}
                        <Folder class="h-5 w-5" />
                    {/if}
                </div>
                <div class="min-w-0 flex-1">
                    <CardHeader class="p-0">
                        <CardTitle class="text-base font-semibold"
                            >{folder.name}</CardTitle
                        >
                    </CardHeader>
                    {#if folder.description}
                        <p
                            class="mt-0.5 line-clamp-1 text-sm text-muted-foreground"
                        >
                            {folder.description}
                        </p>
                    {/if}
                </div>
                <div class="flex shrink-0 items-center gap-3">
                    {#if folder.files.length > 0}
                        <Badge variant="secondary">
                            {folder.files.length} file{folder.files.length !== 1
                                ? 's'
                                : ''}
                        </Badge>
                    {/if}
                    {#if folder.children.length > 0}
                        <Badge variant="outline">
                            {folder.children.length} subfolder{folder.children
                                .length !== 1
                                ? 's'
                                : ''}
                        </Badge>
                    {/if}
                    <ChevronRight
                        class="h-5 w-5 text-muted-foreground transition-transform duration-200 {isFolderOpen(
                            folder.id,
                        )
                            ? 'rotate-90'
                            : ''}"
                    />
                </div>
            </CollapsibleTrigger>

            <!-- Folder Content (expanded) -->
            {#if isFolderOpen(folder.id)}
                <CollapsibleContent>
                    <Separator />
                    <div class="bg-muted/20">
                        <!-- Files -->
                        {#if folder.files.length > 0}
                            <div class="divide-y">
                                {#each folder.files as file (file.id)}
                                    {@render fileRow(file)}
                                {/each}
                            </div>
                        {/if}

                        <!-- Empty state -->
                        {#if folder.files.length === 0 && folder.children.length === 0}
                            <div
                                class="px-5 py-8 text-center text-sm text-muted-foreground"
                            >
                                This folder is empty.
                            </div>
                        {/if}

                        <!-- Subfolders -->
                        {#if folder.children.length > 0}
                            <div class="space-y-3 p-4">
                                {#each folder.children as child (child.id)}
                                    {@render folderCard(child, depth + 1)}
                                {/each}
                            </div>
                        {/if}
                    </div>
                </CollapsibleContent>
            {/if}
        </Collapsible>
    </Card>
{/snippet}

{#snippet fileRow(file: DownloadableFile)}
    {@const Icon = getFileIcon(file.file_type)}
    <div class="flex items-center gap-4 px-5 py-3 transition hover:bg-muted/30">
        <!-- File Icon -->
        <div
            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-muted text-muted-foreground"
        >
            <Icon class="h-5 w-5" />
        </div>

        <!-- File Info -->
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-medium">{file.name}</p>
            <div class="mt-0.5 flex items-center gap-3">
                {#if getFileExtension(file.file_path)}
                    <Badge variant="outline" class="px-1.5 py-0 text-xs">
                        {getFileExtension(file.file_path)}
                    </Badge>
                {/if}
                <span class="text-xs text-muted-foreground">
                    {formatFileSize(file.file_size)}
                </span>
                {#if file.description}
                    <span
                        class="hidden truncate text-xs text-muted-foreground sm:inline"
                    >
                        {file.description}
                    </span>
                {/if}
            </div>
        </div>

        <!-- Actions -->
        <div class="flex shrink-0 items-center gap-1">
            <TooltipProvider>
                {#if isPreviewable(file.file_type)}
                    <Tooltip>
                        <TooltipTrigger>
                            <Button
                                variant="ghost"
                                size="icon"
                                class="h-8 w-8"
                                onclick={() => openPreview(file)}
                            >
                                <Eye class="h-4 w-4" />
                                <span class="sr-only">Preview</span>
                            </Button>
                        </TooltipTrigger>
                        <TooltipContent>
                            <p>Preview file</p>
                        </TooltipContent>
                    </Tooltip>
                {/if}
                <Tooltip>
                    <TooltipTrigger>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="h-8 w-8"
                            href={`/downloadables/download/${file.id}`}
                        >
                            <Download class="h-4 w-4" />
                            <span class="sr-only">Download</span>
                        </Button>
                    </TooltipTrigger>
                    <TooltipContent>
                        <p>Download file</p>
                    </TooltipContent>
                </Tooltip>
            </TooltipProvider>
        </div>
    </div>
{/snippet}
