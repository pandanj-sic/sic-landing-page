<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import {
        ChevronDownIcon,
        GraduationCap,
        Mail,
        MapPin,
        MoonIcon,
        Phone,
        SunIcon,
        Menu,
    } from 'lucide-svelte';

    import { ModeWatcher, toggleMode } from 'mode-watcher';
    import { Button } from '@/components/ui/button';
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuTrigger,
    } from '@/components/ui/dropdown-menu';
    import { Separator } from '@/components/ui/separator';
    import {
        Sheet,
        SheetContent,
        SheetHeader,
        SheetTitle,
    } from '@/components/ui/sheet';
    let { children } = $props();

    let mobileMenuOpen = $state(false);

    let departments = $derived(
        (page.props.departments as Array<{
            id: number;
            name: string;
            slug: string;
        }>) ?? [],
    );
</script>

<ModeWatcher />

<div class="min-h-screen bg-background text-foreground">
    <header
        class="sticky top-0 z-50 border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60"
    >
        <div
            class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8"
        >
            <a class="flex items-center gap-3" href="/">
                <div
                    class="flex h-9 w-9 items-center justify-center rounded-lg"
                >
                    <img src="/sic.png" alt="icon" />
                </div>
                <div class="flex flex-col justify-start text-start">
                    <span class="text-lg font-bold tracking-tight"
                        >San Isidro College</span
                    >
                    <span class="text-sm tracking-tight">Ora et Labora</span>
                </div>
            </a>

            <nav class="hidden items-center gap-6 md:flex">
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        {#snippet children(props)}
                            <button
                                class="inline-flex items-center gap-1 text-sm font-medium text-muted-foreground transition hover:text-foreground"
                                onclick={props.onclick}
                                aria-expanded={props['aria-expanded']}
                                data-state={props['data-state']}
                            >
                                About
                                <ChevronDownIcon class="h-4 w-4" />
                            </button>
                        {/snippet}
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="start" sideOffset={8}>
                        <DropdownMenuItem asChild>
                            {#snippet children(props)}
                                <a
                                    href="/history"
                                    class={props.class}
                                    onclick={props.onClick}
                                >
                                    History
                                </a>
                            {/snippet}
                        </DropdownMenuItem>
                        <DropdownMenuItem asChild>
                            {#snippet children(props)}
                                <a
                                    href="/mission-and-vision"
                                    class={props.class}
                                    onclick={props.onClick}
                                >
                                    Mission and Vision
                                </a>
                            {/snippet}
                        </DropdownMenuItem>
                        <DropdownMenuItem asChild>
                            {#snippet children(props)}
                                <a
                                    href="/organization"
                                    class={props.class}
                                    onclick={props.onClick}
                                >
                                    Organization
                                </a>
                            {/snippet}
                        </DropdownMenuItem>
                        <DropdownMenuItem asChild>
                            {#snippet children(props)}
                                <a
                                    href="/scholarships"
                                    class={props.class}
                                    onclick={props.onClick}
                                >
                                    Scholarships
                                </a>
                            {/snippet}
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>

                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        {#snippet children(props)}
                            <button
                                class="inline-flex items-center gap-1 text-sm font-medium text-muted-foreground transition hover:text-foreground"
                                onclick={props.onclick}
                                aria-expanded={props['aria-expanded']}
                                data-state={props['data-state']}
                            >
                                Departments
                                <ChevronDownIcon class="h-4 w-4" />
                            </button>
                        {/snippet}
                    </DropdownMenuTrigger>
                    <DropdownMenuContent
                        align="start"
                        sideOffset={8}
                        class="w-64"
                    >
                        {#each departments as dept}
                            <DropdownMenuItem asChild>
                                {#snippet children(props)}
                                    <a
                                        href={`/departments/${dept.slug}`}
                                        class={props.class}
                                        onclick={props.onClick}
                                    >
                                        {dept.name}
                                    </a>
                                {/snippet}
                            </DropdownMenuItem>
                        {/each}
                    </DropdownMenuContent>
                </DropdownMenu>

                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        {#snippet children(props)}
                            <button
                                class="inline-flex items-center gap-1 text-sm font-medium text-muted-foreground transition hover:text-foreground"
                                onclick={props.onclick}
                                aria-expanded={props['aria-expanded']}
                                data-state={props['data-state']}
                            >
                                Services
                                <ChevronDownIcon class="h-4 w-4" />
                            </button>
                        {/snippet}
                    </DropdownMenuTrigger>
                    <DropdownMenuContent
                        align="start"
                        sideOffset={8}
                        class="w-48"
                    >
                        <DropdownMenuItem asChild>
                            {#snippet children(props)}
                                <a
                                    href="/library"
                                    class={props.class}
                                    onclick={props.onClick}>Library</a
                                >
                            {/snippet}
                        </DropdownMenuItem>
                        <DropdownMenuItem asChild>
                            {#snippet children(props)}
                                <a
                                    href="https://siccollegeregistrar.com/"
                                    class={props.class}
                                    onclick={props.onClick}>Registrar</a
                                >
                            {/snippet}
                        </DropdownMenuItem>
                        <DropdownMenuItem asChild>
                            {#snippet children(props)}
                                <a
                                    href="/admission"
                                    class={props.class}
                                    onclick={props.onClick}>Admission</a
                                >
                            {/snippet}
                        </DropdownMenuItem>
                        <DropdownMenuItem asChild>
                            {#snippet children(props)}
                                <a
                                    href="/downloadables"
                                    class={props.class}
                                    onclick={props.onClick}>Downloadables</a
                                >
                            {/snippet}
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>

                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        {#snippet children(props)}
                            <button
                                class="inline-flex items-center gap-1 text-sm font-medium text-muted-foreground transition hover:text-foreground"
                                onclick={props.onclick}
                                aria-expanded={props['aria-expanded']}
                                data-state={props['data-state']}
                            >
                                News & Updates
                                <ChevronDownIcon class="h-4 w-4" />
                            </button>
                        {/snippet}
                    </DropdownMenuTrigger>
                    <DropdownMenuContent
                        align="start"
                        sideOffset={8}
                        class="w-48"
                    >
                        <DropdownMenuItem asChild>
                            {#snippet children(props)}
                                <a
                                    href="/posts"
                                    class={props.class}
                                    onclick={props.onClick}>All Posts</a
                                >
                            {/snippet}
                        </DropdownMenuItem>
                        <DropdownMenuItem asChild>
                            {#snippet children(props)}
                                <a
                                    href="/posts?type=article"
                                    class={props.class}
                                    onclick={props.onClick}>News</a
                                >
                            {/snippet}
                        </DropdownMenuItem>
                        <DropdownMenuItem asChild>
                            {#snippet children(props)}
                                <a
                                    href="/posts?type=announcement"
                                    class={props.class}
                                    onclick={props.onClick}>Announcements</a
                                >
                            {/snippet}
                        </DropdownMenuItem>
                        <DropdownMenuItem asChild>
                            {#snippet children(props)}
                                <a
                                    href="/posts?type=event"
                                    class={props.class}
                                    onclick={props.onClick}>Events</a
                                >
                            {/snippet}
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
                <a
                    href="/contact"
                    class="text-sm font-medium text-muted-foreground transition hover:text-foreground"
                    >Contact</a
                >
            </nav>

            <div class="flex items-center gap-2">
                <Button
                    onclick={() => {
                        toggleMode();
                    }}
                    variant="outline"
                    size="icon"
                >
                    <SunIcon
                        class="h-[1.2rem] w-[1.2rem] scale-100 rotate-0 !transition-all dark:scale-0 dark:-rotate-90"
                    />
                    <MoonIcon
                        class="absolute h-[1.2rem] w-[1.2rem] scale-0 rotate-90 !transition-all dark:scale-100 dark:rotate-0"
                    />
                    <span class="sr-only">Toggle theme</span>
                </Button>

                <!-- Mobile Menu Trigger -->
                <div class="md:hidden">
                    <Button
                        variant="ghost"
                        size="icon"
                        class="h-9 w-9"
                        onclick={() => (mobileMenuOpen = true)}
                    >
                        <Menu class="h-5 w-5" />
                        <span class="sr-only">Toggle menu</span>
                    </Button>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Menu Sheet (outside header to avoid stacking context issues) -->
    <Sheet bind:open={mobileMenuOpen}>
        <SheetContent side="right" class="w-[300px] p-6 overflow-y-auto">
            <SheetHeader class="flex justify-start text-left mb-6">
                <SheetTitle class="flex items-center gap-2">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary"
                    >
                        <img src="/sic.png" alt="icon" class="h-6 w-6" />
                    </div>
                    <span class="font-bold text-base">San Isidro College</span>
                </SheetTitle>
            </SheetHeader>
            <nav class="flex flex-col space-y-4">
                <div class="space-y-3">
                    <h4 class="font-medium text-sm text-foreground">About</h4>
                    <a
                        href="/history"
                        class="block text-sm text-muted-foreground transition hover:text-foreground"
                        >History</a
                    >
                    <a
                        href="/mission-and-vision"
                        class="block text-sm text-muted-foreground transition hover:text-foreground"
                        >Mission and Vision</a
                    >
                    <a
                        href="/organization"
                        class="block text-sm text-muted-foreground transition hover:text-foreground"
                        >Organization</a
                    >
                    <a
                        href="/scholarships"
                        class="block text-sm text-muted-foreground transition hover:text-foreground"
                        >Scholarships</a
                    >
                </div>
                <Separator />
                <div class="space-y-3">
                    <h4 class="font-medium text-sm text-foreground">
                        Departments
                    </h4>
                    {#each departments as dept}
                        <a
                            href={`/departments/${dept.slug}`}
                            class="block text-sm text-muted-foreground transition hover:text-foreground"
                            >{dept.name}</a
                        >
                    {/each}
                </div>
                <Separator />
                <div class="space-y-3">
                    <h4 class="font-medium text-sm text-foreground">
                        Services
                    </h4>
                    <a
                        href="/library"
                        class="block text-sm text-muted-foreground transition hover:text-foreground"
                        >Library</a
                    >
                    <a
                        href="https://siccollegeregistrar.com/"
                        class="block text-sm text-muted-foreground transition hover:text-foreground"
                        >Registrar</a
                    >
                    <a
                        href="/admission"
                        class="block text-sm text-muted-foreground transition hover:text-foreground"
                        >Admission</a
                    >
                    <a
                        href="/downloadables"
                        class="block text-sm text-muted-foreground transition hover:text-foreground"
                        >Downloadables</a
                    >
                </div>
                <Separator />
                <div class="space-y-3">
                    <h4 class="font-medium text-sm text-foreground">
                        News & Updates
                    </h4>
                    <a
                        href="/posts"
                        class="block text-sm text-muted-foreground transition hover:text-foreground"
                        onclick={() => (mobileMenuOpen = false)}>All Posts</a
                    >
                    <a
                        href="/posts?type=article"
                        class="block text-sm text-muted-foreground transition hover:text-foreground"
                        onclick={() => (mobileMenuOpen = false)}>News</a
                    >
                    <a
                        href="/posts?type=announcement"
                        class="block text-sm text-muted-foreground transition hover:text-foreground"
                        onclick={() => (mobileMenuOpen = false)}
                        >Announcements</a
                    >
                    <a
                        href="/posts?type=event"
                        class="block text-sm text-muted-foreground transition hover:text-foreground"
                        onclick={() => (mobileMenuOpen = false)}>Events</a
                    >
                </div>
                <a
                    href="/contact"
                    class="text-sm font-medium transition hover:text-foreground"
                    onclick={() => (mobileMenuOpen = false)}
                    >Contact</a
                >
            </nav>
        </SheetContent>
    </Sheet>

    {@render children()}
    <!-- Contact / Footer -->
    <footer id="contact" class="border-t bg-muted/50 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Brand -->
                <div class="sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary text-primary-foreground"
                        >
                            <GraduationCap class="h-5 w-5" />
                        </div>
                        <span class="text-lg font-bold">San Isidro College</span
                        >
                    </div>
                    <p
                        class="mt-4 text-sm leading-relaxed text-muted-foreground"
                    >
                        Empowering students through quality education,
                        innovative research, and community engagement. Ora et
                        Labora.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="mb-4 font-semibold">Quick Links</h3>
                    <ul class="space-y-2 text-sm text-muted-foreground">
                        <li>
                            <a
                                href="/#about"
                                class="transition hover:text-foreground"
                                >About Us</a
                            >
                        </li>
                        <li>
                            <a
                                href="/history"
                                class="transition hover:text-foreground"
                                >History</a
                            >
                        </li>
                        <li>
                            <a
                                href="/posts?type=article"
                                class="transition hover:text-foreground">News</a
                            >
                        </li>
                        <li>
                            <a
                                href="/posts?type=announcement"
                                class="transition hover:text-foreground"
                                >Announcements</a
                            >
                        </li>
                        <li>
                            <a
                                href="/admissions"
                                class="transition hover:text-foreground"
                                >Admissions</a
                            >
                        </li>
                        <li>
                            <a
                                href="/programs"
                                class="transition hover:text-foreground"
                                >Academic Programs</a
                            >
                        </li>
                    </ul>
                </div>

                <!-- Resources -->
                <div>
                    <h3 class="mb-4 font-semibold">Resources</h3>
                    <ul class="space-y-2 text-sm text-muted-foreground">
                        <li>
                            <a
                                href="/portal"
                                class="transition hover:text-foreground"
                                >Student Portal</a
                            >
                        </li>
                        <li>
                            <a
                                href="/library"
                                class="transition hover:text-foreground"
                                >Library</a
                            >
                        </li>
                        <li>
                            <a
                                href="/elearning"
                                class="transition hover:text-foreground"
                                >E-Learning</a
                            >
                        </li>
                        <li>
                            <a
                                href="/careers"
                                class="transition hover:text-foreground"
                                >Career Services</a
                            >
                        </li>
                        <li>
                            <a
                                href="/campus-map"
                                class="transition hover:text-foreground"
                                >Campus Map</a
                            >
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="mb-4 font-semibold">Contact Us</h3>
                    <ul class="space-y-3 text-sm text-muted-foreground">
                        <li class="flex items-start gap-2">
                            <MapPin class="mt-0.5 h-4 w-4 shrink-0" />
                            <span
                                >Impalambonog, Malaybalay City, 8700, Bukidnon,
                                Philippines</span
                            >
                        </li>
                        <li class="flex items-center gap-2">
                            <Phone class="h-4 w-4 shrink-0" />
                            <span> (088) 822-2240 </span>
                        </li>
                        <li class="flex items-center gap-2">
                            <Mail class="h-4 w-4 shrink-0" />
                            <span>info@siccollege.edu</span>
                        </li>
                    </ul>
                </div>
            </div>

            <Separator class="my-8" />

            <div
                class="flex flex-col items-center justify-between gap-4 text-sm text-muted-foreground sm:flex-row"
            >
                <p>
                    &copy; {new Date().getFullYear()} San Isidro College. All rights
                    reserved.
                </p>
                <div class="flex gap-4">
                    <a href="/privacy" class="transition hover:text-foreground"
                        >Privacy Policy</a
                    >
                    <a href="/terms" class="transition hover:text-foreground"
                        >Terms of Service</a
                    >
                </div>
            </div>
        </div>
    </footer>
</div>
