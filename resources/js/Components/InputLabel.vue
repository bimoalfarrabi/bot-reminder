<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="block text-xs font-mono font-medium uppercase tracking-wider text-[#8b949e]">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
