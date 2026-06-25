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
            <h2 class="text-sm font-mono font-semibold uppercase tracking-wider text-[#8b949e]">~/reminders</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="border border-[#30363d] rounded-lg bg-[#161b22] overflow-hidden">
                    <table class="min-w-full text-xs font-mono">
                        <thead>
                            <tr class="border-b border-[#30363d] bg-[#21262d]">
                                <th class="px-4 py-3 text-left font-medium text-[#8b949e] uppercase tracking-wider">id</th>
                                <th class="px-4 py-3 text-left font-medium text-[#8b949e] uppercase tracking-wider">content</th>
                                <th class="px-4 py-3 text-left font-medium text-[#8b949e] uppercase tracking-wider">scheduled_at</th>
                                <th class="px-4 py-3 text-left font-medium text-[#8b949e] uppercase tracking-wider">type</th>
                                <th class="px-4 py-3 text-left font-medium text-[#8b949e] uppercase tracking-wider">status</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#21262d]">
                            <tr v-for="r in reminders.data" :key="r.id" class="hover:bg-[#21262d] transition-colors">
                                <td class="px-4 py-3 text-[#8b949e]">{{ r.id }}</td>
                                <td class="px-4 py-3 max-w-xs truncate text-[#e6edf3]">
                                    {{ r.content_type === 'text' ? (r.content ?? '-') : `[${r.content_type}]` }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-[#e6edf3]">{{ formatDate(r.scheduled_at) }}</td>
                                <td class="px-4 py-3 text-[#8b949e]">{{ r.content_type }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center gap-1 text-xs font-mono"
                                        :class="r.status === 'pending' ? 'text-[#39d353]' : r.status === 'sent' ? 'text-[#8b949e]' : 'text-[#f85149]'"
                                    >
                                        <span style="font-size:0.6rem;line-height:1">{{ r.status === 'pending' ? '●' : '○' }}</span>
                                        {{ r.status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <button
                                        v-if="r.status === 'pending'"
                                        @click="stop(r.id)"
                                        class="text-[#f85149] hover:text-[#e6edf3] text-xs font-mono uppercase tracking-wider transition-colors"
                                    >
                                        rm
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!reminders.data.length">
                                <td colspan="6" class="px-4 py-10 text-center text-[#8b949e]">no reminders found</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="reminders.last_page > 1" class="flex gap-2 px-4 py-3 border-t border-[#30363d]">
                        <Link
                            v-for="link in reminders.links"
                            :key="link.label"
                            :href="link.url ?? '#'"
                            v-html="link.label"
                            class="rounded border px-3 py-1 text-xs font-mono"
                            :class="link.active
                                ? 'border-[#39d353] text-[#39d353]'
                                : 'border-[#30363d] text-[#8b949e] hover:border-[#e6edf3] hover:text-[#e6edf3]'"
                            :aria-disabled="!link.url"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
