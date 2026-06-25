<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="min-h-screen bg-[#0d1117]">
        <nav class="border-b border-[#30363d] bg-[#0d1117]">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-14 justify-between">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex shrink-0 items-center">
                            <Link :href="route('dashboard')" class="text-[#39d353] font-mono font-bold text-sm tracking-tight">
                                &gt; remind_
                            </Link>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                dashboard
                            </NavLink>
                            <NavLink :href="route('reminders.index')" :active="route().current('reminders.*')">
                                reminders
                            </NavLink>
                            <NavLink :href="route('settings.edit')" :active="route().current('settings.*')">
                                settings
                            </NavLink>
                        </div>
                    </div>

                    <div class="hidden sm:ms-6 sm:flex sm:items-center">
                        <div class="relative ms-3">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button class="inline-flex items-center rounded border border-transparent px-3 py-2 text-xs font-mono text-[#8b949e] transition duration-150 hover:text-[#e6edf3] hover:border-[#30363d] focus:outline-none">
                                        <span>{{ $page.props.auth.user.name }}</span>
                                        <svg class="ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <div class="bg-[#161b22] border border-[#30363d] rounded">
                                        <DropdownLink :href="route('profile.edit')" class="block px-4 py-2 text-xs font-mono text-[#8b949e] hover:text-[#e6edf3] hover:bg-[#21262d]">
                                            profile
                                        </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button" class="block w-full text-left px-4 py-2 text-xs font-mono text-[#8b949e] hover:text-[#e6edf3] hover:bg-[#21262d]">
                                            logout
                                        </DropdownLink>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="-me-2 flex items-center sm:hidden">
                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="inline-flex items-center justify-center rounded p-2 text-[#8b949e] transition hover:text-[#e6edf3] hover:bg-[#21262d] focus:outline-none"
                        >
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation -->
            <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden border-t border-[#30363d]">
                <div class="space-y-1 pb-3 pt-2">
                    <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')" class="block px-4 py-2 text-sm font-mono text-[#8b949e] hover:text-[#e6edf3] hover:bg-[#21262d]">
                        dashboard
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('reminders.index')" :active="route().current('reminders.*')" class="block px-4 py-2 text-sm font-mono text-[#8b949e] hover:text-[#e6edf3] hover:bg-[#21262d]">
                        reminders
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('settings.edit')" :active="route().current('settings.*')" class="block px-4 py-2 text-sm font-mono text-[#8b949e] hover:text-[#e6edf3] hover:bg-[#21262d]">
                        settings
                    </ResponsiveNavLink>
                </div>

                <div class="border-t border-[#30363d] pb-1 pt-4">
                    <div class="px-4">
                        <div class="text-xs font-mono font-medium text-[#e6edf3]">{{ $page.props.auth.user.name }}</div>
                        <div class="text-xs font-mono text-[#8b949e]">{{ $page.props.auth.user.email }}</div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <ResponsiveNavLink :href="route('profile.edit')" class="block px-4 py-2 text-sm font-mono text-[#8b949e] hover:text-[#e6edf3] hover:bg-[#21262d]">
                            profile
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('logout')" method="post" as="button" class="block px-4 py-2 text-sm font-mono text-[#8b949e] hover:text-[#e6edf3] hover:bg-[#21262d]">
                            logout
                        </ResponsiveNavLink>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        <header class="border-b border-[#30363d] bg-[#0d1117]" v-if="$slots.header">
            <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
    </div>
</template>
