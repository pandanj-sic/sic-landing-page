<script lang="ts">
    import Building2 from 'lucide-svelte/icons/building-2';
    import ChevronRight from 'lucide-svelte/icons/chevron-right';
    import Users from 'lucide-svelte/icons/users';

    import AppHead from '@/components/AppHead.svelte';
    import {
        Avatar,
        AvatarFallback,
        AvatarImage,
    } from '@/components/ui/avatar';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import { Separator } from '@/components/ui/separator';
    import {
        Sheet,
        SheetContent,
        SheetHeader,
        SheetTitle,
    } from '@/components/ui/sheet';
    import PageLayout from '@/layouts/PageLayout.svelte';
    import type { Department, DepartmentMember } from '@/types/organization';

    let { departments = [] }: { departments: Department[] } = $props();

    type MemberWithDept = DepartmentMember & { departmentName?: string };

    let selectedDepartmentId = $state<number | 'all'>('all');
    let mobileMenuOpen = $state(false);

    const totalMembersCount = $derived(
        departments.reduce((sum, d) => sum + (d.members?.length ?? 0), 0)
    );

    const displayedMembers = $derived<MemberWithDept[]>(
        selectedDepartmentId === 'all'
            ? departments.flatMap((d) => (d.members ?? []).map((m) => ({ ...m, departmentName: d.name })))
            : (departments.find((d) => d.id === selectedDepartmentId)?.members ?? [])
    );

    const selectedDepartment = $derived(
        selectedDepartmentId === 'all'
            ? {
                  name: 'All Members',
                  description: 'Complete directory of all faculty and staff across all departments.',
                  membersCount: totalMembersCount
              }
            : (departments.find((d) => d.id === selectedDepartmentId) ?? null)
    );

    function selectDepartment(id: number | 'all') {
        selectedDepartmentId = id;
        mobileMenuOpen = false;
    }

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

<AppHead title="Organization" />

<PageLayout>
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="mb-8">
            <Badge variant="secondary" class="mb-4">
                <Building2 class="mr-1 h-3 w-3" />
                Organization
            </Badge>
            <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                Our Organization
            </h1>
            <p class="mt-2 text-muted-foreground">
                Meet the dedicated faculty and staff across our departments.
            </p>
        </div>

        {#if departments.length > 0}
            <div class="flex flex-col gap-6 lg:flex-row">
                <!-- Mobile Department Selector -->
                <div class="lg:hidden">
                    <Button
                        variant="outline"
                        class="w-full justify-between"
                        onclick={() => {
                            mobileMenuOpen = true;
                        }}
                    >
                        <span class="flex items-center gap-2">
                            <Building2 class="h-4 w-4" />
                            {selectedDepartment?.name ?? 'Select Department'}
                        </span>
                        <ChevronRight class="h-4 w-4 rotate-90" />
                    </Button>
                </div>

                <!-- Mobile Department Sheet -->
                <Sheet bind:open={mobileMenuOpen}>
                    <SheetContent
                        side="left"
                        class="w-[300px] overflow-y-auto p-0"
                    >
                        <SheetHeader class="border-b p-4">
                            <SheetTitle class="flex items-center gap-2">
                                <Building2 class="h-5 w-5" />
                                Departments
                            </SheetTitle>
                        </SheetHeader>
                        <nav class="p-2 space-y-1">
                            <button
                                type="button"
                                class="{selectedDepartmentId === 'all'
                                    ? 'bg-primary/10 text-primary'
                                    : 'text-muted-foreground'} flex w-full items-center justify-between rounded-lg px-3 py-3 text-left text-sm font-medium transition hover:bg-muted"
                                onclick={() => selectDepartment('all')}
                            >
                                <span>All Members</span>
                                <div class="flex items-center gap-2">
                                    <Badge variant="secondary" class="text-xs">
                                        {totalMembersCount}
                                    </Badge>
                                    {#if selectedDepartmentId === 'all'}
                                        <ChevronRight class="h-4 w-4" />
                                    {/if}
                                </div>
                            </button>

                            {#each departments as department (department.id)}
                                <button
                                    type="button"
                                    class="{department.id === selectedDepartmentId
                                        ? 'bg-primary/10 text-primary'
                                        : 'text-muted-foreground'} flex w-full items-center justify-between rounded-lg px-3 py-3 text-left text-sm font-medium transition hover:bg-muted"
                                    onclick={() => selectDepartment(department.id)}
                                >
                                    <span>{department.name}</span>
                                    <div class="flex items-center gap-2">
                                        <Badge variant="secondary" class="text-xs">
                                            {department.members.length}
                                        </Badge>
                                        {#if department.id === selectedDepartmentId}
                                            <ChevronRight class="h-4 w-4" />
                                        {/if}
                                    </div>
                                </button>
                            {/each}
                        </nav>
                    </SheetContent>
                </Sheet>

                <!-- Desktop Sidebar -->
                <aside class="hidden w-72 shrink-0 lg:block">
                    <Card class="sticky top-24">
                        <CardHeader class="pb-3">
                            <CardTitle class="flex items-center gap-2 text-base">
                                <Building2 class="h-4 w-4" />
                                Departments
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="p-2">
                            <nav class="space-y-1">
                                <button
                                    type="button"
                                    class="{selectedDepartmentId === 'all'
                                        ? 'bg-primary/10 text-primary'
                                        : 'text-muted-foreground'} flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-left text-sm font-medium transition hover:bg-muted"
                                    onclick={() => selectDepartment('all')}
                                >
                                    <span class="truncate font-semibold">All Members</span>
                                    <div class="flex shrink-0 items-center gap-2">
                                        <Badge variant="secondary" class="text-xs">
                                            {totalMembersCount}
                                        </Badge>
                                        {#if selectedDepartmentId === 'all'}
                                            <ChevronRight class="h-4 w-4" />
                                        {/if}
                                    </div>
                                </button>

                                {#each departments as department (department.id)}
                                    <button
                                        type="button"
                                        class="{department.id === selectedDepartmentId
                                            ? 'bg-primary/10 text-primary'
                                            : 'text-muted-foreground'} flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-left text-sm font-medium transition hover:bg-muted"
                                        onclick={() => selectDepartment(department.id)}
                                    >
                                        <span class="truncate">{department.name}</span>
                                        <div class="flex shrink-0 items-center gap-2">
                                            <Badge variant="secondary" class="text-xs">
                                                {department.members.length}
                                            </Badge>
                                            {#if department.id === selectedDepartmentId}
                                                <ChevronRight class="h-4 w-4" />
                                            {/if}
                                        </div>
                                    </button>
                                {/each}
                            </nav>
                        </CardContent>
                    </Card>
                </aside>

                <!-- Main Content -->
                <main class="min-w-0 flex-1">
                    {#if selectedDepartment}
                        <Card>
                            <CardHeader>
                                <div class="flex items-start justify-between">
                                    <div>
                                        <CardTitle class="text-xl">
                                            {selectedDepartment.name}
                                        </CardTitle>
                                        {#if selectedDepartment.description}
                                            <p class="mt-1 text-sm text-muted-foreground">
                                                {selectedDepartment.description}
                                            </p>
                                        {/if}
                                    </div>
                                    <Badge variant="outline" class="shrink-0">
                                        <Users class="mr-1 h-3 w-3" />
                                        {displayedMembers.length}
                                        member{displayedMembers.length !== 1 ? 's' : ''}
                                    </Badge>
                                </div>
                            </CardHeader>
                            <Separator />
                            <CardContent class="p-6">
                                {#if displayedMembers.length > 0}
                                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                                        {#each displayedMembers as member (member.id + '-' + (member.departmentName ?? ''))}
                                            {@render memberCard(member)}
                                        {/each}
                                    </div>
                                {:else}
                                    <div class="py-12 text-center">
                                        <Users class="mx-auto mb-3 h-10 w-10 text-muted-foreground/40" />
                                        <p class="font-medium">No members yet</p>
                                        <p class="mt-1 text-sm text-muted-foreground">
                                            There are no members listed.
                                        </p>
                                    </div>
                                {/if}
                            </CardContent>
                        </Card>
                    {:else}
                        <Card class="py-16 text-center">
                            <CardContent>
                                <Building2 class="mx-auto mb-4 h-12 w-12 text-muted-foreground/40" />
                                <h3 class="text-lg font-semibold">Select a Department</h3>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    Choose a department from the sidebar to view its members.
                                </p>
                            </CardContent>
                        </Card>
                    {/if}
                </main>
            </div>
        {:else}
            <Card class="py-16 text-center">
                <CardContent>
                    <Building2 class="mx-auto mb-4 h-12 w-12 text-muted-foreground/40" />
                    <h3 class="text-lg font-semibold">No departments yet</h3>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Check back soon for information about our organization structure.
                    </p>
                </CardContent>
            </Card>
        {/if}
    </section>
</PageLayout>

{#snippet memberCard(member: DepartmentMember & { departmentName?: string })}
    <Card class="overflow-hidden transition hover:shadow-md">
        <CardContent class="p-4">
            <div class="flex items-start gap-4">
                <Avatar class="h-16 w-16 shrink-0 rounded-lg">
                    {#if member.image_url}
                        <AvatarImage
                            src={resolveImageUrl(member.image_url)}
                            alt={member.name}
                            class="object-cover"
                        />
                    {/if}
                    <AvatarFallback class="rounded-lg text-lg">
                        {getInitials(member.name)}
                    </AvatarFallback>
                </Avatar>
                <div class="min-w-0 flex-1">
                    <h3 class="truncate font-semibold">{member.name}</h3>
                    <p class="mt-0.5 truncate text-sm text-muted-foreground">
                        {member.position}
                    </p>
                    {#if member.departmentName}
                        <Badge variant="outline" class="mt-2 text-[10px] py-0 px-1.5 font-normal uppercase tracking-wider text-muted-foreground">
                            {member.departmentName}
                        </Badge>
                    {/if}
                </div>
            </div>
        </CardContent>
    </Card>
{/snippet}
