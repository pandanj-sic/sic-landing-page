<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Departments', href: '/admin/departments' },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { router } from '@inertiajs/svelte';
    import { Edit, Users, School, Plus, Trash, ZoomIn } from 'lucide-svelte';
    import ImageViewerModal from '@/components/ImageViewerModal.svelte';

    let {
        departments = [],
    }: {
        departments?: any[];
    } = $props();

    let isViewerOpen = $state(false);
    let viewerImageSrc = $state<string | null>(null);

    function openViewer(src: string) {
        viewerImageSrc = src;
        isViewerOpen = true;
    }

    function deleteDept(id: number) {
        if (confirm('Are you sure you want to delete this department?')) {
            router.delete(`/admin/departments/${id}`);
        }
    }
</script>

<AppHead title="Manage Departments" />

<ImageViewerModal 
    isOpen={isViewerOpen}
    imageSrc={viewerImageSrc}
    title="Department Logo Preview"
    onClose={() => isViewerOpen = false}
/>

<div class="p-6 max-w-7xl mx-auto w-full space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-white">Departments Management</h1>
            <p class="text-xs text-neutral-500 mt-1">Edit department descriptions, hero MDX sections, logos, and manage faculty organizational charts.</p>
        </div>
        <a 
            href="/admin/departments/create"
            class="inline-flex items-center gap-1.5 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-amber-600/10 hover:bg-amber-700 transition-colors"
        >
            <Plus class="h-4 w-4" /> Add Department
        </a>
    </div>

    <!-- Departments Grid -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        {#each departments as dept}
            <div class="rounded-xl border border-neutral-100 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-[#161615] flex flex-col justify-between hover:border-neutral-200 dark:hover:border-neutral-800 transition-colors">
                <div>
                    <div class="flex items-center gap-3">
                        {#if dept.logo_path}
                            <button 
                                type="button" 
                                onclick={() => openViewer(dept.logo_path)}
                                class="relative group h-10 w-10 shrink-0 overflow-hidden rounded-lg border border-neutral-100 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-0.5"
                                title="Click to expand logo"
                            >
                                <img src={dept.logo_path} alt={dept.name} class="h-full w-full object-contain" />
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity">
                                    <ZoomIn class="h-3.5 w-3.5" />
                                </div>
                            </button>
                        {:else}
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-600/10 text-amber-600">
                                <School class="h-5 w-5" />
                            </div>
                        {/if}
                        <h3 class="font-bold text-neutral-900 dark:text-white leading-tight">{dept.name}</h3>
                    </div>
                    
                    <p class="mt-4 text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed line-clamp-3">
                        {dept.description || 'No description provided.'}
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-neutral-50 dark:border-neutral-850 flex items-center gap-2">
                    <a 
                        href={`/admin/departments/${dept.id}/edit`}
                        class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-lg border border-neutral-200 py-2 text-xs font-bold text-neutral-600 hover:bg-neutral-50 dark:border-neutral-850 dark:text-neutral-400 dark:hover:bg-neutral-900 transition-colors"
                    >
                        <Edit class="h-3.5 w-3.5" /> Edit Info
                    </a>
                    <a 
                        href={`/admin/departments/${dept.id}/members`}
                        class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-lg bg-amber-600 py-2 text-xs font-bold text-white hover:bg-amber-700 shadow-sm transition-colors"
                    >
                        <Users class="h-3.5 w-3.5" /> Faculty Chart
                    </a>
                    <button 
                        onclick={() => deleteDept(dept.id)}
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-neutral-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/30 transition-colors"
                        title="Delete Department"
                    >
                        <Trash class="h-4 w-4" />
                    </button>
                </div>
            </div>
        {/each}
    </div>
</div>
