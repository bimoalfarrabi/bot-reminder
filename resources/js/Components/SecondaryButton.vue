<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center rounded border border-[#30363d] bg-transparent px-4 py-2 text-xs font-mono font-semibold uppercase tracking-widest text-[#8b949e] transition-colors duration-150 hover:border-[#e6edf3] hover:text-[#e6edf3] focus:outline-none focus:ring-1 focus:ring-[#8b949e] focus:ring-offset-1 focus:ring-offset-[#0d1117] disabled:opacity-40"
    >
        <slot />
    </button>
</template>
