<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import type { Snippet } from 'svelte';
    import { toUrl } from '@/lib/utils';
    import { login, dashboard } from '@/routes';
    import { School, Menu, X, ChevronDown, MapPin, Mail, Phone, Calendar, Download as DownloadIcon, FileText, Sun, Moon } from 'lucide-svelte';
    import { themeState } from '@/lib/theme.svelte';
    import AppearanceTabs from '@/components/AppearanceTabs.svelte';

    let {
        navigationLinks = [],
        departments = [],
        children,
    }: {
        navigationLinks?: any[];
        departments?: any[];
        children?: Snippet;
    } = $props();

    const auth = $derived(page.props.auth as any);
    const { appearance, updateAppearance } = themeState();
    
    let mobileMenuOpen = $state(false);
    let deptDropdownOpen = $state(false);

    function toggleMobileMenu() {
        mobileMenuOpen = !mobileMenuOpen;
    }

    function toggleDeptDropdown(e: MouseEvent) {
        e.stopPropagation();
        deptDropdownOpen = !deptDropdownOpen;
    }

    function closeAll() {
        mobileMenuOpen = false;
        deptDropdownOpen = false;
    }
</script>

<svelte:window onclick={closeAll} />

<div class="flex min-h-screen flex-col bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
    <!-- Navigation Header -->
    <header class="sticky top-0 z-[100] border-b border-[#0f2d62] bg-[#123573] shadow-md text-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
            <!-- Brand / Logo -->
            <Link href="/" class="flex items-center gap-3" onclick={closeAll}>
                <img src="/sic.png" alt="San Isidro College Logo" class="h-10 w-10 object-contain drop-shadow-sm" />
                <div>
                    <span class="block font-bold tracking-tight text-lg leading-tight text-white">San Isidro College</span>
                    <span class="block text-xs text-blue-200/90">Values. Excellence. Service.</span>
                </div>
            </Link>

            <!-- Desktop Nav Links -->
            <nav class="hidden items-center gap-8 md:flex">
                {#each navigationLinks as link}
                    {#if link.url === '#departments'}
                        <div class="relative group">
                            <button 
                                onclick={toggleDeptDropdown}
                                class="flex items-center gap-1 text-sm font-medium text-white/90 transition-colors hover:text-amber-400 cursor-pointer"
                            >
                                Departments <ChevronDown class="h-4 w-4 transition-transform group-hover:rotate-180 {deptDropdownOpen ? 'rotate-180' : ''}" />
                            </button>

                            <div 
                                class="absolute left-0 mt-2 w-64 rounded-xl border border-blue-900/80 bg-[#123573] p-2 shadow-2xl z-[9999] hidden group-hover:block transition-all"
                                class:!block={deptDropdownOpen}
                                role="presentation"
                                onclick={(e) => e.stopPropagation()}
                                onkeydown={(e) => e.stopPropagation()}
                            >
                                {#each departments as dept}
                                    <Link 
                                        href={`/departments/${dept.slug}`}
                                        class="block rounded-lg px-4 py-2.5 text-sm transition-colors text-white/90 hover:bg-blue-800/80 hover:text-amber-300"
                                        onclick={closeAll}
                                    >
                                        <span class="block font-medium">{dept.name}</span>
                                    </Link>
                                {/each}
                            </div>
                        </div>
                    {:else if link.children && link.children.length > 0}
                        <div class="relative group">
                            <button 
                                class="flex items-center gap-1 text-sm font-medium text-white/90 transition-colors hover:text-amber-400 cursor-pointer"
                            >
                                {link.label} <ChevronDown class="h-4 w-4 transition-transform group-hover:rotate-180" />
                            </button>

                            <div 
                                class="absolute left-0 mt-2 w-64 rounded-xl border border-blue-900/80 bg-[#123573] p-2 shadow-2xl z-[9999] hidden group-hover:block hover:block transition-all"
                                role="presentation"
                                onclick={(e) => e.stopPropagation()}
                                onkeydown={(e) => e.stopPropagation()}
                            >
                                {#each link.children as child}
                                    <Link 
                                        href={child.url}
                                        class="block rounded-lg px-4 py-2.5 text-sm transition-colors text-white/90 hover:bg-blue-800/80 hover:text-amber-300"
                                        onclick={closeAll}
                                    >
                                        <span class="block font-medium">{child.label}</span>
                                    </Link>
                                {/each}
                            </div>
                        </div>
                    {:else}
                        <Link 
                            href={link.url}
                            class="text-sm font-medium text-white/90 transition-colors hover:text-amber-400"
                        >
                            {link.label}
                        </Link>
                    {/if}
                {/each}
            </nav>

            <!-- Action / Appearance -->
            <div class="hidden items-center gap-4 md:flex">
                <button
                    onclick={() => updateAppearance(appearance.value === 'dark' ? 'light' : 'dark')}
                    class="rounded-lg p-2 hover:bg-blue-800/60 border border-blue-800/50 text-white/90 hover:text-white transition-all cursor-pointer"
                    aria-label="Toggle Theme"
                >
                    {#if appearance.value === 'dark'}
                        <Sun class="h-4.5 w-4.5" />
                    {:else}
                        <Moon class="h-4.5 w-4.5" />
                    {/if}
                </button>
            </div>

            <!-- Mobile Menu Toggle -->
            <button 
                class="flex h-10 w-10 items-center justify-center rounded-lg border border-blue-800/60 text-white md:hidden hover:bg-blue-800/60"
                onclick={(e) => { e.stopPropagation(); toggleMobileMenu(); }}
            >
                {#if mobileMenuOpen}
                    <X class="h-5 w-5" />
                {:else}
                    <Menu class="h-5 w-5" />
                {/if}
            </button>
        </div>

        <!-- Mobile Navigation Menu -->
        {#if mobileMenuOpen}
            <div 
                class="border-t border-blue-900/80 bg-[#123573] px-6 py-4 md:hidden text-white"
                role="presentation"
                onclick={(e) => e.stopPropagation()}
                onkeydown={(e) => e.stopPropagation()}
            >
                <nav class="flex flex-col gap-4">
                    {#each navigationLinks as link}
                        {#if link.url === '#departments'}
                            <div class="flex flex-col gap-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-blue-200">Departments</span>
                                {#each departments as dept}
                                    <Link 
                                        href={`/departments/${dept.slug}`}
                                        class="pl-4 text-sm font-medium text-white/90 hover:text-amber-400"
                                        onclick={closeAll}
                                    >
                                        {dept.name}
                                    </Link>
                                {/each}
                            </div>
                        {:else if link.children && link.children.length > 0}
                            <div class="flex flex-col gap-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-blue-200">{link.label}</span>
                                {#each link.children as child}
                                    <Link 
                                        href={child.url}
                                        class="pl-4 text-sm font-medium text-white/90 hover:text-amber-400"
                                        onclick={closeAll}
                                    >
                                        {child.label}
                                    </Link>
                                {/each}
                            </div>
                        {:else}
                            <Link 
                                href={link.url}
                                class="text-sm font-medium text-white/90 hover:text-amber-400"
                                onclick={closeAll}
                            >
                                {link.label}
                            </Link>
                        {/if}
                    {/each}

                    <div class="flex flex-col gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-blue-200">Appearance</span>
                        <div class="flex justify-start">
                            <AppearanceTabs />
                        </div>
                    </div>
                </nav>
            </div>
        {/if}
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        {@render children?.()}
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-black/5 bg-neutral-50 py-12 dark:border-white/5 dark:bg-[#161615]">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid grid-cols-1 gap-10 md:grid-cols-4">
                <!-- Info -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <img src="/sic.png" alt="San Isidro College Logo" class="h-8 w-8 object-contain" />
                        <span class="font-bold text-amber-700 dark:text-amber-500">San Isidro College</span>
                    </div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                        Founded in 1952, San Isidro College is committed to delivering quality education integrated with Christian values.
                    </p>
                </div>

                <!-- Departments -->
                <div class="space-y-4">
                    <span class="block text-sm font-bold uppercase tracking-wider text-neutral-400">Departments</span>
                    <ul class="space-y-2 text-sm text-neutral-600 dark:text-neutral-400">
                        {#each departments as dept}
                            <li>
                                <Link href={`/departments/${dept.slug}`} class="hover:text-amber-600 dark:hover:text-amber-500">
                                    {dept.name}
                                </Link>
                            </li>
                        {/each}
                    </ul>
                </div>

                <!-- Quick Links -->
                <div class="space-y-4">
                    <span class="block text-sm font-bold uppercase tracking-wider text-neutral-400">Quick Links</span>
                    <ul class="space-y-2 text-sm text-neutral-600 dark:text-neutral-400">
                        <li><Link href="/posts" class="hover:text-amber-600">News & Events</Link></li>
                        <li><Link href="/downloads" class="hover:text-amber-600">Downloads Portal</Link></li>
                        <li><Link href="/contact" class="hover:text-amber-600">Contact & Admissions</Link></li>
                    </ul>
                </div>

                <!-- Contact Details -->
                <div class="space-y-4">
                    <span class="block text-sm font-bold uppercase tracking-wider text-neutral-400">Contact Us</span>
                    <ul class="space-y-2 text-sm text-neutral-600 dark:text-neutral-400">
                        <li class="flex items-center gap-2">
                            <MapPin class="h-4 w-4 shrink-0 text-amber-600" />
                            <span>Impalambong, Malaybalay City, Bukidnon</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <Mail class="h-4 w-4 shrink-0 text-amber-600" />
                            <span>info@sic.edu.ph</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <Phone class="h-4 w-4 shrink-0 text-amber-600" />
                            <span>+63 (88) 813-2244</span>
                        </li>
                    </ul>
                </div>
            </div>

            <hr class="my-8 border-black/5 dark:border-white/5" />

            <div class="flex flex-col items-center justify-between gap-4 sm:flex-row text-xs text-neutral-500 dark:text-neutral-400">
                <span>&copy; {new Date().getFullYear()} San Isidro College. All rights reserved.</span>
                <span class="flex gap-4">
                    <a href="#privacy" class="hover:underline">Privacy Policy</a>
                    <a href="#terms" class="hover:underline">Terms of Service</a>
                </span>
            </div>
        </div>
    </footer>
</div>
