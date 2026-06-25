<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="w-full rounded border border-[#30363d] bg-[#0d1117] px-3 py-2 text-sm font-mono text-[#e6edf3] placeholder-[#8b949e] focus:border-[#39d353] focus:outline-none focus:ring-1 focus:ring-[#39d353]"
        v-model="model"
        ref="input"
    />
</template>
