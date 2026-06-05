import { createInertiaApp } from '@inertiajs/svelte';
import AppLayout from '@/layouts/AppLayout.svelte';
import AuthLayout from '@/layouts/AuthLayout.svelte';
import SettingsLayout from '@/layouts/settings/Layout.svelte';
import { initializeTheme } from '@/lib/theme.svelte';

const appName = 'San Isidro College';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        return;
        // switch (true) {
        //   case name === 'Welcome':
        //   case name === 'History':
        //   case name === 'MissionAndVision':
        //   case name === 'PostLists':
        //   case name === 'Posts':
        //   case name === 'Downloadables':
        //     return null;
        //   case name.startsWith('auth/'):
        //     return AuthLayout;
        //   case name.startsWith('settings/'):
        //     return [AppLayout, SettingsLayout];
        //   default:
        //     return AppLayout;
        // }
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
// initializeTheme();
