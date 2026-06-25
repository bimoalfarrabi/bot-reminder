<script setup>
import { reactive } from 'vue'
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

const status = usePage().props.status

function submit() {
    router.put(route('settings.update'), form, { preserveScroll: true })
}
</script>

<template>
    <Head title="Settings" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Settings</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <div class="max-w-xl">
                        <h3 class="mb-1 text-lg font-medium text-gray-900">Telegram Bot</h3>
                        <p class="mb-6 text-sm text-gray-500">Token dan Chat ID dibaca dari database saat runtime.</p>

                        <div
                            v-if="status === 'settings-updated'"
                            class="mb-4 rounded bg-green-50 px-4 py-2 text-sm text-green-700"
                        >
                            Settings tersimpan.
                        </div>

                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Bot Token</label>
                                <input
                                    v-model="form.bot_token"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm"
                                    placeholder="123456:ABC-DEF..."
                                    autocomplete="off"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Chat ID</label>
                                <input
                                    v-model="form.chat_id"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm"
                                    placeholder="-100123456789"
                                    autocomplete="off"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Allowed User IDs</label>
                                <input
                                    v-model="form.allowed_user_ids"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm"
                                    placeholder="123456789,987654321"
                                    autocomplete="off"
                                />
                                <p class="mt-1 text-xs text-gray-400">User ID Telegram yang diizinkan, pisah koma. Kosongkan = tidak ada filter.</p>
                            </div>

                            <div class="flex items-center gap-4">
                                <button
                                    type="submit"
                                    class="rounded bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-700"
                                >
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
