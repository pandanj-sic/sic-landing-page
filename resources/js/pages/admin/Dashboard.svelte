<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: '/dashboard',
            },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { page, Link } from '@inertiajs/svelte';
    import { FileText, Download, School, Users, PlusCircle, ArrowRight, Upload } from 'lucide-svelte';

    let {
        postsCount = 0,
        downloadsCount = 0,
        departmentsCount = 0,
        usersCount = 0,
    }: {
        postsCount?: number;
        downloadsCount?: number;
        departmentsCount?: number;
        usersCount?: number;
    } = $props();

    const auth = $derived(page.props.auth as any);
    const user = $derived(auth?.user);
</script>

<AppHead title="Admin Dashboard" />

<div class="flex flex-col gap-8 p-6 max-w-7xl mx-auto w-full">
    <!-- Header Greeting -->
    <div class="space-y-2">
        <h1 class="text-3xl font-bold tracking-tight text-neutral-900 dark:text-white">
            Welcome back, {user?.name || 'Administrator'}
        </h1>
        <p class="text-sm text-neutral-500 dark:text-neutral-400">
            {#if user?.role === 'superadmin'}
                System SuperAdmin • Full administrative controls active.
            {:else}
                Department Administrator • Managing content for {user?.department?.name || 'Assigned Department'}.
            {/if}
        </p>
    </div>

    <!-- Stats Grid -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- Posts Stats -->
        <div class="rounded-xl border border-neutral-100 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-[#161615]">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-neutral-400 uppercase tracking-wider">Posts & news</span>
                <div class="rounded-lg bg-amber-600/10 p-2 text-amber-600">
                    <FileText class="h-5 w-5" />
                </div>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-extrabold text-neutral-900 dark:text-white">{postsCount}</span>
                <span class="block text-xs text-neutral-500 mt-1">Total articles & announcements</span>
            </div>
        </div>

        <!-- Downloads Stats -->
        <div class="rounded-xl border border-neutral-100 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-[#161615]">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-neutral-400 uppercase tracking-wider">Downloads</span>
                <div class="rounded-lg bg-amber-600/10 p-2 text-amber-600">
                    <Download class="h-5 w-5" />
                </div>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-extrabold text-neutral-900 dark:text-white">{downloadsCount}</span>
                <span class="block text-xs text-neutral-500 mt-1">Total uploaded public files</span>
            </div>
        </div>

        <!-- Departments Stats -->
        <div class="rounded-xl border border-neutral-100 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-[#161615]">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-neutral-400 uppercase tracking-wider">Departments</span>
                <div class="rounded-lg bg-amber-600/10 p-2 text-amber-600">
                    <School class="h-5 w-5" />
                </div>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-extrabold text-neutral-900 dark:text-white">{departmentsCount}</span>
                <span class="block text-xs text-neutral-500 mt-1">Colleges & academic divisions</span>
            </div>
        </div>

        <!-- Users Stats -->
        <div class="rounded-xl border border-neutral-100 bg-white p-6 shadow-sm dark:border-neutral-900 dark:bg-[#161615]">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-neutral-400 uppercase tracking-wider">Administrators</span>
                <div class="rounded-lg bg-amber-600/10 p-2 text-amber-600">
                    <Users class="h-5 w-5" />
                </div>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-extrabold text-neutral-900 dark:text-white">{usersCount}</span>
                <span class="block text-xs text-neutral-500 mt-1">Total platform admins</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="space-y-4">
        <h2 class="text-xl font-bold tracking-tight text-neutral-900 dark:text-white">Quick Tasks</h2>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <!-- Create Post -->
            <Link 
                href="/admin/posts/create"
                class="group flex flex-col justify-between rounded-xl border border-neutral-100 bg-white p-6 shadow-sm hover:border-amber-600 transition-all dark:border-neutral-900 dark:bg-[#161615]"
            >
                <div class="space-y-2">
                    <div class="rounded-lg bg-amber-600/10 p-2.5 text-amber-600 w-fit">
                        <PlusCircle class="h-6 w-6" />
                    </div>
                    <h3 class="font-bold text-neutral-950 dark:text-white mt-4 group-hover:text-amber-600 transition-colors">Write News or Event</h3>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed">
                        Publish an article, campus event, or announcement.
                    </p>
                </div>
                <div class="mt-6 flex items-center gap-1.5 text-xs font-bold text-amber-600 group-hover:gap-2 transition-all">
                    Get Started <ArrowRight class="h-3.5 w-3.5" />
                </div>
            </Link>

            <!-- Upload File -->
            <Link 
                href="/admin/downloads"
                class="group flex flex-col justify-between rounded-xl border border-neutral-100 bg-white p-6 shadow-sm hover:border-amber-600 transition-all dark:border-neutral-900 dark:bg-[#161615]"
            >
                <div class="space-y-2">
                    <div class="rounded-lg bg-amber-600/10 p-2.5 text-amber-600 w-fit">
                        <Upload class="h-6 w-6" />
                    </div>
                    <h3 class="font-bold text-neutral-950 dark:text-white mt-4 group-hover:text-amber-600 transition-colors">Upload Document</h3>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed">
                        Add forms, calendars, or guides to the public downloads portal.
                    </p>
                </div>
                <div class="mt-6 flex items-center gap-1.5 text-xs font-bold text-amber-600 group-hover:gap-2 transition-all">
                    Upload File <ArrowRight class="h-3.5 w-3.5" />
                </div>
            </Link>

            <!-- View Site -->
            <a 
                href="/" 
                target="_blank"
                class="group flex flex-col justify-between rounded-xl border border-neutral-100 bg-white p-6 shadow-sm hover:border-amber-600 transition-all dark:border-neutral-900 dark:bg-[#161615]"
            >
                <div class="space-y-2">
                    <div class="rounded-lg bg-amber-600/10 p-2.5 text-amber-600 w-fit">
                        <School class="h-6 w-6" />
                    </div>
                    <h3 class="font-bold text-neutral-950 dark:text-white mt-4 group-hover:text-amber-600 transition-colors">Preview Public Site</h3>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed">
                        Open the public landing page in a new window to view your changes.
                    </p>
                </div>
                <div class="mt-6 flex items-center gap-1.5 text-xs font-bold text-amber-600 group-hover:gap-2 transition-all">
                    Open Site <ArrowRight class="h-3.5 w-3.5" />
                </div>
            </a>
        </div>
    </div>
</div>
