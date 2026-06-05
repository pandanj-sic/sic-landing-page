<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import ArrowRight from 'lucide-svelte/icons/arrow-right';
    import BookOpen from 'lucide-svelte/icons/book-open';
    import Calendar from 'lucide-svelte/icons/calendar';
    import GraduationCap from 'lucide-svelte/icons/graduation-cap';
    import ImageIcon from 'lucide-svelte/icons/image';
    import Megaphone from 'lucide-svelte/icons/megaphone';
    import Newspaper from 'lucide-svelte/icons/newspaper';
    import Trophy from 'lucide-svelte/icons/trophy';
    import Users from 'lucide-svelte/icons/users';

    import AppHead from '@/components/AppHead.svelte';
    import Carousel from '@/components/Carousel.svelte';
    import PostCard from '@/components/PostCard.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent } from '@/components/ui/card';
    import { Separator } from '@/components/ui/separator';
    import PageLayout from '@/layouts/PageLayout.svelte';
    import type { Post, Event } from '@/types';

    type CarouselItem = {
        id: number;
        title: string;
        description: string;
        button_text: string | null;
        button_url: string | null;
        image_url: string;
        order: number;
    };

    type MessageOfTheDay = {
        id: number;
        person_name: string;
        person_image: string | null;
        message: string;
    };

    let {
        carousels = [],
        posts = [],
        announcements = [],
        events = [],
        messageOfTheDay = null,
    }: {
        carousels: CarouselItem[];
        posts: Post[];
        announcements: Post[];
        events: Event[];
        messageOfTheDay: MessageOfTheDay | null;
    } = $props();

    // Map carousel data to the Carousel component's slide format
    const slides = $derived(
        carousels.map((c) => ({
            image: c.image_url.startsWith('http')
                ? c.image_url
                : `/storage/${c.image_url}`,
            title: c.title,
            subtitle: c.description,
            buttonText: c.button_text,
            buttonUrl: c.button_url,
        })),
    );

    const stats = [
        { icon: GraduationCap, value: '15,000+', label: 'Graduates' },
        { icon: BookOpen, value: '120+', label: 'Programs' },
        { icon: Users, value: '500+', label: 'Faculty' },
        { icon: Trophy, value: '50+', label: 'Awards' },
    ];

    function getInitials(name: string): string {
        return name
            .split(' ')
            .map((n) => n[0])
            .join('')
            .toUpperCase()
            .slice(0, 2);
    }
</script>

<AppHead title="Welcome">
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
</AppHead>

<PageLayout>
    <!-- Hero Carousel -->
    <section
        class="mx-auto max-h-[900px] aspect-video px-4 pt-6 sm:px-6 lg:px-8"
    >
        {#if slides.length > 0}
            <Carousel {slides} interval={5000} />
        {:else}
            <div
                class="flex aspect-[21/9] items-center justify-center rounded-xl bg-muted"
            >
                <div class="text-center text-muted-foreground">
                    <ImageIcon class="mx-auto mb-2 h-12 w-12 opacity-50" />
                    <p class="text-lg font-medium">
                        Welcome to San Isidro College
                    </p>
                    <p class="text-sm">Ora et Labora</p>
                </div>
            </div>
        {/if}
    </section>

    <!-- Stats -->
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            {#each stats as stat (stat.label)}
                <Card class="text-center">
                    <CardContent class="flex flex-col items-center gap-2 pt-6">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary"
                        >
                            <stat.icon class="h-6 w-6" />
                        </div>
                        <p class="text-2xl font-bold tracking-tight">
                            {stat.value}
                        </p>
                        <p class="text-sm text-muted-foreground">
                            {stat.label}
                        </p>
                    </CardContent>
                </Card>
            {/each}
        </div>
    </section>

    <!-- Message of the Day -->
    {#if messageOfTheDay}
        <section class="py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-8 text-center"></div>
                <Card class="relative overflow-hidden">
                    <CardContent class="p-5">
                        <div class="grid grid-cols-[3fr_9fr]">
                            <!-- Image -->
                            <div class="relative overflow-hidden">
                                {#if messageOfTheDay.person_image}
                                    <img
                                        src="/storage/{messageOfTheDay.person_image}"
                                        alt={messageOfTheDay.person_name}
                                        class="h-full w-full object-cover"
                                    />
                                {:else}
                                    <div
                                        class="flex h-full w-full items-center justify-center bg-muted"
                                    >
                                        <span
                                            class="text-6xl font-bold text-muted-foreground/50"
                                        >
                                            {getInitials(
                                                messageOfTheDay.person_name,
                                            )}
                                        </span>
                                    </div>
                                {/if}
                            </div>
                            <!-- Quote -->
                            <div
                                class="flex flex-col justify-center space-y-6 p-8 md:p-12"
                            >
                                <Badge variant="secondary" class="mb-4"
                                    >Message of the Day</Badge
                                >
                                <blockquote>
                                    <p
                                        class="leading-relaxed text-foreground italic"
                                    >
                                        "{messageOfTheDay.message}"
                                    </p>
                                </blockquote>
                                <div class="border-l-4 border-primary pl-4">
                                    <p class="text-lg font-semibold">
                                        {messageOfTheDay.person_name}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </section>
    {/if}

    <!-- About -->
    <section id="about" class="bg-muted/50 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <div>
                    <Badge variant="secondary" class="mb-4"
                        >About Our College</Badge
                    >
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                        Building Futures Since 1965
                    </h2>
                    <p class="mt-4 leading-relaxed text-muted-foreground">
                        For over five decades, San Isidro College has been
                        committed to academic excellence, innovation, and
                        community service. Our institution offers a wide range
                        of undergraduate and graduate programs designed to equip
                        students with the knowledge and skills needed to thrive
                        in a rapidly changing world.
                    </p>
                    <p class="mt-4 leading-relaxed text-muted-foreground">
                        With world-class facilities, a distinguished faculty,
                        and a vibrant campus life, we provide a holistic
                        education that nurtures critical thinking, creativity,
                        and leadership.
                    </p>
                    <Button class="mt-6" variant="default">
                        Learn More
                        <ArrowRight class="ml-2 h-4 w-4" />
                    </Button>
                </div>
                <div class="relative">
                    <img
                        src="https://images.unsplash.com/photo-1607237138185-eedd9c632b0b?w=700&h=500&fit=crop"
                        alt="College campus"
                        class="rounded-xl shadow-lg"
                    />
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Posts -->
    <section id="posts" class="py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 flex items-end justify-between">
                <div>
                    <Badge variant="secondary" class="mb-4">Latest News</Badge>
                    <h2 class="text-3xl font-bold tracking-tight">
                        Campus Updates
                    </h2>
                    <p class="mt-2 text-muted-foreground">
                        Stay informed with the latest happenings around campus.
                    </p>
                </div>
                <Button variant="ghost" class="hidden sm:flex" asChild>
                    <Link href="/news">
                        View all news
                        <ArrowRight class="ml-2 h-4 w-4" />
                    </Link>
                </Button>
            </div>

            {#if posts.length > 0}
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    {#each posts as post (post.id)}
                        <PostCard {post} />
                    {/each}
                </div>
            {:else}
                <Card class="py-12 text-center">
                    <CardContent>
                        <Newspaper
                            class="mx-auto mb-3 h-10 w-10 text-muted-foreground/50"
                        />
                        <p class="text-muted-foreground">
                            No posts yet. Check back soon!
                        </p>
                    </CardContent>
                </Card>
            {/if}

            <div class="mt-6 text-center sm:hidden">
                <Button variant="outline" asChild>
                    <Link href="/news">
                        View all news
                        <ArrowRight class="ml-2 h-4 w-4" />
                    </Link>
                </Button>
            </div>
        </div>
    </section>


    <!-- Announcements -->
    <section id="announcements" class="py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 flex items-end justify-between">
                <div>
                    <Badge variant="secondary" class="mb-4">
                        <Megaphone class="mr-1 h-3 w-3" />
                        Announcements
                    </Badge>
                    <h2 class="text-3xl font-bold tracking-tight">
                        Important Notices
                    </h2>
                    <p class="mt-2 text-muted-foreground">
                        Don't miss important updates and deadlines.
                    </p>
                </div>
                <Button variant="ghost" class="hidden sm:flex" asChild>
                    <Link href="/announcements">
                        View all announcements
                        <ArrowRight class="ml-2 h-4 w-4" />
                    </Link>
                </Button>
            </div>

            {#if announcements.length > 0}
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    {#each announcements as announcement (announcement.id)}
                        <PostCard post={announcement} />
                    {/each}
                </div>
            {:else}
                <Card class="py-12 text-center">
                    <CardContent>
                        <Megaphone
                            class="mx-auto mb-3 h-10 w-10 text-muted-foreground/50"
                        />
                        <p class="text-muted-foreground">
                            No announcements at this time.
                        </p>
                    </CardContent>
                </Card>
            {/if}

            <div class="mt-6 text-center sm:hidden">
                <Button variant="outline" asChild>
                    <Link href="/announcements">
                        View all announcements
                        <ArrowRight class="ml-2 h-4 w-4" />
                    </Link>
                </Button>
            </div>
        </div>
    </section>

    <section> 
        <div id="img-highlights"> 
        <img src="/images/ARTS.png" alt="ARTS"/>
        <img src="/images/BSBA.png" alt="ARTS"/>
        <img src="/images/BAED.png" alt="ARTS"/>
        <img src="/images/ENGR.png" alt="ARTS"/>
        <img src="/images/BSIT.png" alt="ARTS"/>
        <img src="/images/MIDWIFERY.png" alt="ARTS"/>
        <img src="/images/ELEM.png" alt="ARTS"/>
        <img src="/images/JHS.png" alt="ARTS"/>
        <img src="/images/SHS.png" alt="ARTS"/>
        </div>
    </section>


    <!-- Upcoming Events -->
    {#if events.length > 0}
        <Separator class="mx-auto max-w-7xl" />

        <section id="events" class="py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-10 flex items-end justify-between">
                    <div>
                        <Badge variant="secondary" class="mb-4">
                            <Calendar class="mr-1 h-3 w-3" />
                            Events
                        </Badge>
                        <h2 class="text-3xl font-bold tracking-tight">
                            Upcoming Events
                        </h2>
                        <p class="mt-2 text-muted-foreground">
                            Mark your calendar for these upcoming activities.
                        </p>
                    </div>
                    <Button variant="ghost" class="hidden sm:flex" asChild>
                        <Link href="/events">
                            View all events
                            <ArrowRight class="ml-2 h-4 w-4" />
                        </Link>
                    </Button>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    {#each events as event (event.id)}
                        <PostCard post={event} />
                    {/each}
                </div>

                <div class="mt-6 text-center sm:hidden">
                    <Button variant="outline" asChild>
                        <Link href="/events">
                            View all events
                            <ArrowRight class="ml-2 h-4 w-4" />
                        </Link>
                    </Button>
                </div>
            </div>
        </section>
    {/if}
</PageLayout>
