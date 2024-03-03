<script>
/* Vue */
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
/* Composables */
import useSettings from '@/composables/settings'
/* Layout */
import AdminLayout from '@/Layouts/Authenticated.vue'
/* Components */
export default {
    components: {
        Head,
        AdminLayout,
    },
    setup () {

        const { fetch, update } = useSettings()

        const settings = ref([])

        const fetchSettings = async () => {
            await fetch(settings)
        }

        const updateSettings = async () => {
            await update(settings)
            fetchSettings()
        }

        onMounted(fetchSettings)

        return {
            settings,
            updateSettings
        }
    }
}
</script>

<template>
    <Head title="Settings" />
        <AdminLayout>
            <template #header>
                <h1 class="font-bold text-3xl text-up-maroon leading-tight uppercase">Settings</h1>
            </template>
            <div class="py-5 px-5 md:px-10">
                
                <div class="max-w-7xl sm:px-6 lg:px-8 space-y-6">
                    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <section class="max-w-xl">
                            <header>
                                <h2 class="text-lg font-medium text-gray-900">System Information</h2>

                                <p class="mt-1 text-sm text-gray-600">
                                    Update your system settings here.
                                </p>
                            </header>

                            <div v-if="settings.length" class="mt-6 space-y-6">
                                <div class="w-full flex grid grid-cols-1 md:grid-cols-1 gap-3">
                                    <div>
                                        <label class="text-up-green font-semibold block mb-2">System Title <span class="text-red-500">*</span></label>
                                        <input v-model="settings[0].value" type="text" class="w-full bg-gray-50 border-gray-300 rounded-md mr-1 p-2" placeholder="Ticketing System">
                                    </div>

                                    <div>
                                        <label class="text-up-green font-semibold block mb-2">System Subtitle <span class="text-red-500">*</span></label>
                                        <textarea v-model="settings[1].value" class="w-full bg-gray-50 rounded-md border border-gray-300 text-sm" placeholder="Enter system subtitle" rows="5"></textarea>
                                    </div>

                                    <div>
                                        <label class="text-up-green font-semibold block mb-2">Ticket Due <span class="text-red-500">*</span></label>
                                        <input v-model="settings[2].value" type="number" class="w-full bg-gray-50 border-gray-300 rounded-md mr-1 p-2" placeholder="3" min="1">
                                    </div>
                                </div>

                                <div class="flex items-center gap-4">
                                    <button @click="updateSettings" type="button" class="inline-flex items-center px-4 py-2 bg-up-green border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Save
                                    </button>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </AdminLayout>

</template>
