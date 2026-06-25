<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: { type: String, required: true },
    active: { type: Boolean },
});

const classes = computed(() =>
    props.active
        ? 'inline-flex items-center px-1 pt-1 border-b-2 border-[#39d353] text-sm font-mono font-medium leading-5 text-[#e6edf3] focus:outline-none transition duration-150 ease-in-out'
        : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-mono font-medium leading-5 text-[#8b949e] hover:text-[#e6edf3] hover:border-[#30363d] focus:outline-none transition duration-150 ease-in-out',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
