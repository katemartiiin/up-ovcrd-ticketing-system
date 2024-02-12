<script>
/* Vue */
import { ref, reactive, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
/* Composables */
import useNotifications from '@/composables/notifications'
/* Layout */
import AuthenticatedLayout from '@/Layouts/AuthenticatedVcd.vue'
/* Components */
import Dropdown from '@/Components/Dropdown.vue'
import DataTable from '@/Components/DataTable.vue'
import Pagination from '@/Components/Pagination.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
export default {
    components: {
        Link,
        Head,
        Dropdown,
        DataTable,
        Pagination,
        DropdownLink,
        AuthenticatedLayout
    },
    setup() {

        const { vcdFetch } = useNotifications()

        const headers = ref([
            { text: 'Date', value: 'createdDate' },
            { text: 'Description', value: 'description' },
        ])

        const items = ref([])

        const pageOptions = reactive({
            to: 0,
            from: 0,
            page: 1,
            total: 0,
            current_page: 1,
            rowsPerPage: 10,
        })

        const fetchNotifs = async () => {
            await vcdFetch(pageOptions, items)
        }

        onMounted(fetchNotifs)

        return {
            items,
            headers,
            pageOptions,
            fetchNotifs
        }
    }
}
</script>

<template>
    <Head title="Notifications" />
    
    <AuthenticatedLayout>
        <template #header>
            <h1 class="font-bold text-3xl text-up-maroon leading-tight uppercase">Notifications</h1>
            <p v-if="items.length" class="text-gray-700 my-2 font-semibold text-lg">{{ pageOptions.total }} Notification {{ pageOptions.total <= 1 ? '' : 's'  }}</p>
            <p v-else class="text-gray-700 my-2 text-lg">No Notifications</p>
        </template>

        <div class="py-5 px-5 md:px-10">
            <!-- Ticket Table -->
            <div class="w-full flex flex-wrap">
                <!-- SM -->
                <div class="w-full md:hidden block bg-gray-200 rounded-md px-5 py-3 mb-5">
                    <!-- Table items -->
                    <template v-if="items.length">
                        <div v-for="(item, index) in items" :key="index" class="w-full flex flex-wrap text-sm mt-2 mb-4">
                            <template v-for="(header, key) in headers" :key="key" class="w-full flex flex-wrap">
                                <div class="w-1/2 bg-up-maroon text-white p-2 border-b border-white">{{ header['text'] }}</div>
                                <div class="w-1/2 bg-white text-up-maroon p-2 border-b border-up-maroon font-semibold">{{ item[header['value']] }}</div>
                            </template>
                        </div>
                    </template>
                    <template v-else>
                        <p class="text-center text-sm tracking-widest text-gray-600">No items found.</p>
                    </template>
                    <!-- End of Table items -->
                </div>
                <!-- MD -->
                <div class="md:block hidden w-full">
                    <!-- Table header -->
                    <div class="w-full bg-gray-200 rounded-md py-2 px-3 overflow-x-auto mb-3">
                        <div class="w-full flex flex-wrap bg-white rounded-md px-5 py-2 text-up-maroon font-bold border-2 border-gray-300">
                            <div class="w-1/4">
                                Date
                            </div>
                            <div class="w-3/4">
                                Description
                            </div>
                        </div>
                    </div>
                    <!-- Table header -->
                    <div class="w-full bg-gray-200 rounded-md py-2 px-3 overflow-x-auto min-tbl-h mb-5">
                        <!-- Table items -->
                        <template v-if="items.length">
                            <div v-for="(item, index) in items" :key="index" class="w-full flex flex-wrap bg-white rounded-md px-5 py-3 shadow my-3">
                                <div class="w-1/4">
                                    {{ item['createdDate'] }}
                                </div>
                                <div class="w-3/4">
                                    {{ item['description'] }}
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="w-full bg-gray-100 rounded-md px-5 py-3 shadow my-3">
                                <p class="text-center text-sm tracking-widest text-gray-600">No items found.</p>
                            </div>
                        </template>
                        <!-- End of Table items -->
                    </div>
                </div>
                <!-- Pagination -->
                <div class="w-full">
                    <Pagination :options="pageOptions" @fetch="fetchNotifs" />
                </div>
            </div>
            <!-- End of Ticket Table -->

        </div>
    </AuthenticatedLayout>
</template>
