<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-full px-4 py-2 text-start text-xs font-mono leading-5 text-[#8b949e] transition duration-150 ease-in-out hover:bg-[#21262d] hover:text-[#e6edf3] focus:bg-[#21262d] focus:outline-none"
    >
        <slot />
    </Link>
</template>
