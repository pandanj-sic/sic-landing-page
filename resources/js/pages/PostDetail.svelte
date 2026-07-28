<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import PublicLayout from '@/components/PublicLayout.svelte';
    import { parseMarkdown } from '@/lib/markdown';
    import { Link } from '@inertiajs/svelte';
    import { ArrowLeft, Calendar, User, FileText, Megaphone } from 'lucide-svelte';

    let {
        post,
        departments = [],
        navigationLinks = [],
    }: {
        post: any;
        departments?: any[];
        navigationLinks?: any[];
    } = $props();

    const parsedContentHtml = $derived(parseMarkdown(post.content));
</script>

<AppHead title={`${post.title} - San Isidro College`} />

<PublicLayout {navigationLinks} {departments}>
    <article class="py-16 md:py-24">
        <div class="mx-auto max-w-3xl px-6">
            <!-- Back button -->
            <div class="mb-8">
                <Link 
                    href="/posts" 
                    class="inline-flex items-center gap-1.5 text-sm font-semibold text-amber-600 hover:text-amber-700 dark:text-amber-500"
                >
                    <ArrowLeft class="h-4 w-4" /> Back to News & Events
                </Link>
            </div>

            <!-- Post Header -->
            <header class="space-y-4">
                <div class="flex items-center gap-2 text-xs">
                    <span class="rounded-full px-2.5 py-0.5 font-semibold capitalize 
                        {post.type === 'event' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/35 dark:text-blue-400' : ''}
                        {post.type === 'announcement' ? 'bg-red-50 text-red-700 dark:bg-red-900/35 dark:text-red-400' : ''}
                        {post.type === 'article' ? 'bg-green-50 text-green-700 dark:bg-green-900/35 dark:text-green-400' : ''}
                    ">
                        {post.type}
                    </span>
                    {#if post.department}
                        <span class="text-neutral-400">|</span>
                        <Link href={`/departments/${post.department.slug}`} class="font-medium text-amber-600 hover:underline">
                            {post.department.name}
                        </Link>
                    {/if}
                </div>

                <h1 class="text-3xl font-extrabold tracking-tight text-neutral-900 md:text-5xl dark:text-white leading-tight">
                    {post.title}
                </h1>

                <!-- Meta info -->
                <div class="flex flex-wrap items-center gap-6 border-b border-t border-neutral-100 py-4 text-xs text-neutral-500 dark:border-neutral-900 dark:text-neutral-400">
                    <div class="flex items-center gap-1.5">
                        <User class="h-4 w-4 text-neutral-400" />
                        <span>Published by {post.user ? post.user.name : 'Administrator'}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <Calendar class="h-4 w-4 text-neutral-400" />
                        <span>{new Date(post.created_at).toLocaleDateString(undefined, {month: 'long', day: 'numeric', year: 'numeric'})}</span>
                    </div>
                </div>
            </header>

            <!-- Event Details if type is Event -->
            {#if post.type === 'event' && post.start_date}
                <div class="mt-8 rounded-2xl border border-blue-100 bg-blue-50/50 p-6 dark:border-blue-900/20 dark:bg-blue-950/10">
                    <h3 class="font-bold text-blue-950 dark:text-blue-300">Event Details</h3>
                    <div class="mt-3 flex flex-col gap-2 text-sm text-blue-900 dark:text-blue-400">
                        <div class="flex items-center gap-2">
                            <Calendar class="h-4 w-4 text-blue-600" />
                            <span>
                                <strong>Start:</strong> {new Date(post.start_date).toLocaleString(undefined, {dateStyle: 'medium', timeStyle: 'short'})}
                            </span>
                        </div>
                        {#if post.end_date}
                            <div class="flex items-center gap-2">
                                <Calendar class="h-4 w-4 text-blue-600" />
                                <span>
                                    <strong>End:</strong> {new Date(post.end_date).toLocaleString(undefined, {dateStyle: 'medium', timeStyle: 'short'})}
                                </span>
                            </div>
                        {/if}
                    </div>
                </div>
            {/if}

            <!-- Highlight Image -->
            {#if post.highlight_image_path}
                <div class="mt-8 overflow-hidden rounded-2xl">
                    <img 
                        src={post.highlight_image_path} 
                        alt={post.title} 
                        class="w-full object-cover max-h-[450px]"
                    />
                </div>
            {/if}

            <!-- Content Area -->
            <div class="mt-10 prose prose-neutral dark:prose-invert max-w-none">
                {@html parsedContentHtml}
            </div>
        </div>
    </article>
</PublicLayout>
