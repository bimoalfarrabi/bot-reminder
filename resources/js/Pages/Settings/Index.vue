<script setup>
import { computed, reactive } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    botToken: String,
    chatId: String,
    allowedUserIds: String,
})

const form = reactive({
    bot_token:        props.botToken ?? '',
    chat_id:          props.chatId ?? '',
    allowed_user_ids: props.allowedUserIds ?? '',
})

const status = computed(() => usePage().props.flash?.status)

function submit() {
    router.put(route('settings.update'), form, { preserveScroll: true })
}
</script>

<template>
    <Head title="Settings" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-sm font-mono font-semibold uppercase tracking-wider text-[#8b949e]">~/settings</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="border border-[#30363d] rounded-lg bg-[#161b22] p-6 sm:p-8">
                    <div class="max-w-xl">
                        <h3 class="mb-1 text-sm font-mono font-semibold text-[#e6edf3]">Telegram Bot</h3>
                        <p class="mb-6 text-xs font-mono text-[#8b949e]">Token dan Chat ID dibaca dari database saat runtime.</p>

                        <div
                            v-if="status === 'settings-updated'"
                            class="mb-4 rounded border border-[#39d353] bg-[#0d1117] px-4 py-2 text-xs font-mono text-[#39d353]"
                        >
                            settings tersimpan.
                        </div>

                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <label class="block text-xs font-mono font-medium uppercase tracking-wider text-[#8b949e]">Bot Token</label>
                                <input
                                    v-model="form.bot_token"
                                    type="text"
                                    class="mt-1 w-full rounded border border-[#30363d] bg-[#0d1117] px-3 py-2 text-sm font-mono text-[#e6edf3] placeholder-[#8b949e] focus:border-[#39d353] focus:outline-none focus:ring-1 focus:ring-[#39d353]"
                                    placeholder="123456:ABC-DEF..."
                                    autocomplete="off"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-mono font-medium uppercase tracking-wider text-[#8b949e]">Chat ID</label>
                                <input
                                    v-model="form.chat_id"
                                    type="text"
                                    class="mt-1 w-full rounded border border-[#30363d] bg-[#0d1117] px-3 py-2 text-sm font-mono text-[#e6edf3] placeholder-[#8b949e] focus:border-[#39d353] focus:outline-none focus:ring-1 focus:ring-[#39d353]"
                                    placeholder="-100123456789"
                                    autocomplete="off"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-mono font-medium uppercase tracking-wider text-[#8b949e]">Allowed User IDs</label>
                                <input
                                    v-model="form.allowed_user_ids"
                                    type="text"
                                    class="mt-1 w-full rounded border border-[#30363d] bg-[#0d1117] px-3 py-2 text-sm font-mono text-[#e6edf3] placeholder-[#8b949e] focus:border-[#39d353] focus:outline-none focus:ring-1 focus:ring-[#39d353]"
                                    placeholder="123456789,987654321"
                                    autocomplete="off"
                                />
                                <p class="mt-1 text-xs font-mono text-[#8b949e]">User ID Telegram yang diizinkan, pisah koma. Kosongkan = tidak ada filter.</p>
                            </div>

                            <div class="flex items-center gap-4 pt-2">
                                <button
                                    type="submit"
                                    class="inline-flex items-center rounded border border-[#39d353] bg-transparent px-4 py-2 text-xs font-mono font-semibold uppercase tracking-widest text-[#39d353] transition-colors hover:bg-[#39d353] hover:text-[#0d1117]"
                                >
                                    simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
