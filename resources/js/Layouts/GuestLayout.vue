<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-[#0d1117]">
        <div class="mb-8">
            <Link href="/" class="text-[#39d353] font-mono font-bold text-xl tracking-tight">
                &gt; remind_
            </Link>
        </div>
        <div class="w-full max-w-sm bg-[#161b22] border border-[#30363d] rounded-lg px-8 py-8">
            <slot />
        </div>
    </div>
</template>
