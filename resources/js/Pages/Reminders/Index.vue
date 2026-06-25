<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineProps({
    reminders: Object, // paginated
})

function stop(id) {
    if (!confirm(`Hentikan reminder #${id}?`)) return
    router.delete(route('reminders.destroy', id), { preserveScroll: true })
}

function formatDate(dt) {
    return new Date(dt).toLocaleString('id-ID', {
        timeZone: 'Asia/Jakarta',
        day: '2-digit', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit',
    }) + ' WIB'
}
</script>

<template>
    <Head title="Reminders" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Reminders</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500">#</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500">Konten</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500">Waktu (WIB)</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500">Tipe</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="r in reminders.data" :key="r.id">
                                    <td class="px-4 py-3 text-gray-400">{{ r.id }}</td>
                                    <td class="px-4 py-3 max-w-xs truncate">
                                        {{ r.content_type === 'text' ? (r.content ?? '-') : `[${r.content_type}]` }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(r.scheduled_at) }}</td>
                                    <td class="px-4 py-3">
                                        <span v-if="r.is_recurring" class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs text-blue-700">🔁 Recurring</span>
                                        <span v-else class="text-gray-400">Sekali</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span v-if="!r.is_active" class="text-gray-400">Nonaktif</span>
                                        <span v-else-if="r.triggered_at" class="text-green-600">Terkirim</span>
                                        <span v-else class="text-yellow-600">Pending</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <button
                                            v-if="r.is_active && !r.triggered_at"
                                            @click="stop(r.id)"
                                            class="rounded bg-red-100 px-3 py-1 text-xs text-red-700 hover:bg-red-200"
                                        >
                                            Stop
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="reminders.data.length === 0">
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada reminder.</td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div v-if="reminders.last_page > 1" class="mt-4 flex gap-2">
                            <Link
                                v-for="link in reminders.links"
                                :key="link.label"
                                :href="link.url ?? '#'"
                                v-html="link.label"
                                class="rounded border px-3 py-1 text-sm"
                                :class="link.active ? 'bg-gray-800 text-white' : 'text-gray-600 hover:bg-gray-50'"
                                :aria-disabled="!link.url"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
