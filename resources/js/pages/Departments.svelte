<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import ArrowRight from 'lucide-svelte/icons/arrow-right';
    import Building2 from 'lucide-svelte/icons/building-2';
    import Newspaper from 'lucide-svelte/icons/newspaper';
    import Users from 'lucide-svelte/icons/users';

    import AppHead from '@/components/AppHead.svelte';
    import Carousel from '@/components/Carousel.svelte';
    import PostCard from '@/components/PostCard.svelte';
    import {
        Avatar,
        AvatarFallback,
        AvatarImage,
    } from '@/components/ui/avatar';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent } from '@/components/ui/card';
    import { Separator } from '@/components/ui/separator';
    import PageLayout from '@/layouts/PageLayout.svelte';
    import type {
        Department,
        DepartmentCarousel,
        DepartmentMember,
    } from '@/types/organization';
    import type { Post } from '@/types/post';

    let {
        department,
        posts = [],
    }: {
        department: Department;
        posts: Post[];
    } = $props();

    // Map carousel data to the Carousel component's slide format
    const slides = $derived(
        (department.carousels ?? []).map((c: DepartmentCarousel) => ({
            image: c.image_url.startsWith('http')
                ? c.image_url
                : `/storage/${c.image_url}`,
            title: c.title,
            subtitle: c.description,
            buttonText: c.button_text,
            buttonUrl: c.button_url,
        })),
    );

    function getInitials(name: string): string {
        return name
            .split(' ')
            .map((n) => n[0])
            .join('')
            .toUpperCase()
            .slice(0, 2);
    }

    function resolveImageUrl(imagePath: string | null): string | null {
        if (!imagePath) {
            return null;
        }

        return imagePath.startsWith('http')
            ? imagePath
            : `/storage/${imagePath}`;
    }
</script>

<AppHead title={department.name} />

<PageLayout>
    <!-- Hero Section -->
    {#if slides.length > 0}
        <section class="relative">
            <div
                class="mx-auto max-h-[600px] aspect-video px-4 pt-6 sm:px-6 lg:px-8"
            >
                <Carousel {slides} interval={5000} />
            </div>
        </section>
    {/if}

    <!-- Department Header Info -->
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-6 md:flex-row md:items-center">
            {#if department.image_url}
                <Avatar class="h-24 w-24 rounded-2xl ring-4 ring-muted shrink-0">
                    <AvatarImage
                        src={resolveImageUrl(department.image_url)}
                        alt={department.name}
                        class="object-cover"
                    />
                    <AvatarFallback class="rounded-2xl text-2xl font-bold bg-primary/10 text-primary">
                        {getInitials(department.name)}
                    </AvatarFallback>
                </Avatar>
            {:else}
                <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-2xl bg-muted ring-4 ring-muted/50 text-muted-foreground">
                    <Building2 class="h-10 w-10 opacity-70" />
                </div>
            {/if}
            <div>
                <Badge variant="secondary" class="mb-2">
                    <Building2 class="mr-1 h-3 w-3" />
                    Department
                </Badge>
                <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                    {department.name}
                </h1>
                {#if department.description}
                    <p class="mt-2 max-w-3xl text-sm text-muted-foreground sm:text-base">
                        {department.description}
                    </p>
                {/if}
            </div>
        </div>
    </section>

    <!-- Posts Section -->
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-end justify-between">
                <div>
                    <Badge variant="secondary" class="mb-4">
                        <Newspaper class="mr-1 h-3 w-3" />
                        News & Updates
                    </Badge>
                    <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">
                        Latest from {department.name}
                    </h2>
                    <p class="mt-2 text-muted-foreground">
                        Stay updated with news and announcements from this
                        department.
                    </p>
                </div>
                {#if posts.length > 0}
                    <Button variant="ghost" class="hidden sm:flex" asChild>
                        <Link href={`/posts?department=${department.id}`}>
                            View all
                            <ArrowRight class="ml-2 h-4 w-4" />
                        </Link>
                    </Button>
                {/if}
            </div>

            {#if posts.length > 0}
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    {#each posts as post (post.id)}
                        <PostCard {post} />
                    {/each}
                </div>

                <div class="mt-6 text-center sm:hidden">
                    <Button variant="outline" asChild>
                        <Link href={`/posts?department=${department.id}`}>
                            View all posts
                            <ArrowRight class="ml-2 h-4 w-4" />
                        </Link>
                    </Button>
                </div>
            {:else}
                <Card class="py-12 text-center">
                    <CardContent>
                        <Newspaper
                            class="mx-auto mb-3 h-10 w-10 text-muted-foreground/50"
                        />
                        <p class="text-muted-foreground">
                            No posts from this department yet. Check back soon!
                        </p>
                    </CardContent>
                </Card>
            {/if}
        </div>
    </section>
        <Separator class="mx-auto max-w-7xl" />

    <!-- Department Rich Content -->
    {#if department.content}
        <section class="py-10">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div class="prose prose-slate max-w-none dark:prose-invert">
                    {@html department.content}
                </div>
            </div>
        </section>
        <Separator class="mx-auto max-w-7xl" />
    {/if}

    <Separator class="mx-auto max-w-7xl" />

    <!-- Organization / Members Section -->
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <Badge variant="secondary" class="mb-4">
                    <Users class="mr-1 h-3 w-3" />
                    Our Team
                </Badge>
                <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">
                    Meet the Faculty & Staff
                </h2>
                <p class="mt-2 text-muted-foreground">
                    The dedicated people behind {department.name}.
                </p>
            </div>

            {#if department.members.length > 0}
                <div
                    class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"
                >
                    {#each department.members as member (member.id)}
                        {@render memberCard(member)}
                    {/each}
                </div>
            {:else}
                <Card class="py-12 text-center">
                    <CardContent>
                        <Users
                            class="mx-auto mb-3 h-10 w-10 text-muted-foreground/50"
                        />
                        <p class="text-muted-foreground">
                            No team members listed yet.
                        </p>
                    </CardContent>
                </Card>
            {/if}
        </div>
    </section>
</PageLayout>

{#snippet memberCard(member: DepartmentMember)}
    <Card class="overflow-hidden transition hover:shadow-md">
        <CardContent class="p-6 text-center">
            <Avatar class="mx-auto h-24 w-24 rounded-full">
                {#if member.image_url}
                    <AvatarImage
                        src={resolveImageUrl(member.image_url)}
                        alt={member.name}
                        class="object-cover"
                    />
                {/if}
                <AvatarFallback class="text-2xl">
                    {getInitials(member.name)}
                </AvatarFallback>
            </Avatar>
            <h3 class="mt-4 font-semibold">{member.name}</h3>
            <p class="mt-1 text-sm text-muted-foreground">{member.position}</p>
        </CardContent>
    </Card>
{/snippet}
