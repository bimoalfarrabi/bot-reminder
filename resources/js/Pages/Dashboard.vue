<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-sm font-mono font-semibold uppercase tracking-wider text-[#8b949e]">~/dashboard</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="border border-[#30363d] rounded-lg bg-[#161b22] p-6">
                    <p class="mb-6 text-sm font-mono text-[#8b949e]">
                        <span class="text-[#39d353]">$</span> bot running · use Telegram to set reminders
                    </p>
                    <Link
                        :href="route('reminders.index')"
                        class="inline-flex items-center rounded border border-[#39d353] px-4 py-2 text-xs font-mono font-semibold uppercase tracking-widest text-[#39d353] transition-colors hover:bg-[#39d353] hover:text-[#0d1117]"
                    >
                        ls reminders/
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
