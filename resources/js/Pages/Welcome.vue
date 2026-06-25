<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: { type: Boolean },
});
</script>

<template>
    <Head title="remind_" />
    <div class="flex min-h-screen flex-col items-center justify-center bg-[#0d1117]">
        <div class="text-center">
            <p class="mb-2 font-mono text-xs uppercase tracking-widest text-[#8b949e]">personal telegram bot</p>
            <h1 class="mb-8 font-mono text-4xl font-bold text-[#39d353]">&gt; remind_</h1>
            <div v-if="canLogin">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="inline-flex items-center rounded border border-[#39d353] px-6 py-2 font-mono text-sm font-semibold uppercase tracking-widest text-[#39d353] transition-colors hover:bg-[#39d353] hover:text-[#0d1117]"
                >
                    go to dashboard
                </Link>
                <Link
                    v-else
                    :href="route('login')"
                    class="inline-flex items-center rounded border border-[#39d353] px-6 py-2 font-mono text-sm font-semibold uppercase tracking-widest text-[#39d353] transition-colors hover:bg-[#39d353] hover:text-[#0d1117]"
                >
                    login
                </Link>
            </div>
        </div>
    </div>
</template>
