<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import LayoutGrid from 'lucide-svelte/icons/layout-grid';
    import type { Snippet } from 'svelte';
    import AppLogo from '@/components/AppLogo.svelte';
    import NavFooter from '@/components/NavFooter.svelte';
    import NavMain from '@/components/NavMain.svelte';
    import NavUser from '@/components/NavUser.svelte';
    import {
        Sidebar,
        SidebarContent,
        SidebarFooter,
        SidebarHeader,
        SidebarMenu,
        SidebarMenuButton,
        SidebarMenuItem,
    } from '@/components/ui/sidebar';
    import { toUrl } from '@/lib/utils';
    import { dashboard } from '@/routes';
    import type { NavItem } from '@/types';

    import { page } from '@inertiajs/svelte';
    import FileText from 'lucide-svelte/icons/file-text';
    import Download from 'lucide-svelte/icons/download';
    import Image from 'lucide-svelte/icons/image';
    import School from 'lucide-svelte/icons/school';
    import UsersIcon from 'lucide-svelte/icons/users';
    import Link2 from 'lucide-svelte/icons/link-2';
    import Home from 'lucide-svelte/icons/home';
    import SettingsIcon from 'lucide-svelte/icons/settings';
    import SparklesIcon from 'lucide-svelte/icons/sparkles';

    let {
        children,
    }: {
        children?: Snippet;
    } = $props();

    const auth = $derived(page.props.auth as any);
    const user = $derived(auth?.user);

    const mainNavItems = $derived([
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutGrid,
        },
        {
            title: 'Posts',
            href: '/admin/posts',
            icon: FileText,
        },
        {
            title: 'Downloads',
            href: '/admin/downloads',
            icon: Download,
        },
        {
            title: 'Carousels',
            href: '/admin/carousels',
            icon: Image,
        },
        {
            title: 'Departments',
            href: '/admin/departments',
            icon: School,
        },
        ...(user?.role === 'superadmin' ? [
            {
                title: 'Users',
                href: '/admin/users',
                icon: UsersIcon,
            },
            {
                title: 'Nav Links',
                href: '/admin/navigation-links',
                icon: Link2,
            },
            {
                title: 'Landing Settings',
                href: '/admin/settings/landing',
                icon: SettingsIcon,
            },
            {
                title: 'Hero Sections',
                href: '/admin/hero-sections',
                icon: SparklesIcon,
            }
        ] : []),
        {
            title: 'View Site',
            href: '/',
            icon: Home,
        }
    ]);

    const footerNavItems: NavItem[] = [];
</script>

<Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
        <SidebarMenu>
            <SidebarMenuItem>
                <SidebarMenuButton size="lg" asChild>
                    {#snippet children(props)}
                        <Link
                            {...props}
                            href={toUrl(dashboard())}
                            class={props.class}
                        >
                            <AppLogo />
                        </Link>
                    {/snippet}
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
        <NavMain items={mainNavItems} />
    </SidebarContent>

    <SidebarFooter>
        <NavFooter items={footerNavItems} />
        <NavUser />
    </SidebarFooter>
</Sidebar>
{@render children?.()}
