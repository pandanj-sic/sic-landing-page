<script lang="ts">
    import { School, ChevronDown, Eye, EyeOff, ExternalLink } from 'lucide-svelte';

    let {
        label = 'Link Label',
        url = '/path',
        isActive = true,
        parentLabel = '',
    }: {
        label?: string;
        url?: string;
        isActive?: boolean;
        parentLabel?: string;
    } = $props();
</script>

<div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-800 dark:bg-[#161615] space-y-3">
    <div class="flex items-center justify-between">
        <span class="text-xs font-bold uppercase tracking-wider text-neutral-400">Live Header Preview</span>
        {#if isActive}
            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">
                <Eye class="h-3 w-3" /> Visible
            </span>
        {:else}
            <span class="inline-flex items-center gap-1 rounded-full bg-neutral-100 px-2 py-0.5 text-[10px] font-semibold text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                <EyeOff class="h-3 w-3" /> Hidden
            </span>
        {/if}
    </div>

    <!-- Header Navbar Mockup -->
    <div class="rounded-lg border border-neutral-200 bg-neutral-50 p-3 dark:border-neutral-800 dark:bg-[#0a0a0a]">
        <div class="flex items-center justify-between">
            <!-- Brand -->
            <div class="flex items-center gap-2">
                <div class="flex h-7 w-7 items-center justify-center rounded bg-amber-600 text-white text-xs">
                    <School class="h-4 w-4" />
                </div>
                <span class="font-bold text-xs text-amber-700 dark:text-amber-500">San Isidro College</span>
            </div>

            <!-- Nav Bar Preview -->
            <div class="flex items-center gap-4 text-xs">
                <span class="text-neutral-400 hidden sm:inline">Home</span>
                
                {#if parentLabel}
                    <div class="relative flex items-center gap-1 text-amber-600 font-semibold dark:text-amber-400 border-b-2 border-amber-600 pb-0.5">
                        <span>{parentLabel}</span>
                        <ChevronDown class="h-3 w-3" />
                        <!-- Dropdown preview popup -->
                        <div class="absolute top-full left-0 mt-1 w-36 rounded-md border border-neutral-200 bg-white p-1.5 shadow-md dark:border-neutral-800 dark:bg-[#161615]">
                            <div class="rounded px-2 py-1 bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 font-bold flex items-center justify-between text-[11px]">
                                <span class="truncate">{label || 'Untitled'}</span>
                                <ExternalLink class="h-2.5 w-2.5 opacity-60" />
                            </div>
                        </div>
                    </div>
                {:else}
                    <div class="flex items-center gap-1 font-bold text-amber-700 dark:text-amber-500 border-b-2 border-amber-600 pb-0.5">
                        <span>{label || 'Untitled Link'}</span>
                    </div>
                {/if}

                <span class="text-neutral-400 hidden sm:inline">Contact</span>
            </div>
        </div>
    </div>

    <!-- URL target indicator -->
    <div class="flex items-center gap-1.5 text-[11px] text-neutral-400 font-mono">
        <span class="font-semibold text-neutral-500">Target:</span>
        <span class="truncate text-amber-600 dark:text-amber-500">{url || '/'}</span>
    </div>
</div>
