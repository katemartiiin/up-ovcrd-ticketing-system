<script>
/* Vue */
import { ref, reactive, watch, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
/* Composables */
import useOffices from '@/composables/offices'
/* Layout */
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
/* Components */
import DataTable from '@/Components/DataTable.vue'
import Pagination from '@/Components/Pagination.vue'
export default {
    components: {
        Link,
        Head,
        DataTable,
        Pagination,
        AuthenticatedLayout
    },
    setup() {

        const { fetch, activate } = useOffices()

        const searchParams = reactive({
            keyword: '',
            status: 1,
        })

        const headers = ref([
            { text: '#', value: 'id' },
            { text: 'Section', value: 'name' },
            { text: 'Users', value: 'userCount' },
            { text: 'Action', value: 'action' }
        ])

        const offices = ref([])

        const initialOptions = {
            to: 0,
            from: 0,
            page: 1,
            total: 0,
            current_page: 1,
            rowsPerPage: 10,
        }

        const pageOptions = reactive({...initialOptions})

        const fetchOffices = async () => {
            await fetch(searchParams, pageOptions, offices)
        }

        const search = () => {
            fetchOffice()
        }

        const toggleActivate = async (id) => {
            await activate(id)
            fetchOffices()
        }

        watch (
            () => searchParams.status,
            (value) => {
                Object.assign(pageOptions, initialOptions)
                fetchOffices()
            }
        )

        onMounted(fetchOffices)

        return {
            search,
            headers,
            offices,
            pageOptions,
            searchParams,
            fetchOffices,
            toggleActivate
        }
    }
}
</script>

<template>
    <Head title="Sections" />
    
    <AuthenticatedLayout>
        <template #header>
            <h1 class="font-bold text-3xl text-up-maroon leading-tight uppercase">Sections</h1>
        </template>

        <div class="py-5 px-5 md:px-10">

            <!-- Search bar -->
            <div class="w-full flex flex-wrap text-sm">
                <div class="w-full md:w-3/4 py-1">
                    <input v-model="searchParams.keyword" type="text" class="w-full border-gray-300 rounded-md mr-2 text-sm" placeholder="Search" />
                </div>
                <div class="w-full md:w-1/4 py-1 flex grid grid-cols-2 gap-2 md:pl-2">
                    <button @click="search" type="button" class="border border-up-green bg-up-green-lighter hover:bg-up-green hover:text-white transition duration-300 ease-in-out text-up-green font-semibold py-2 px-5 rounded-md">Search</button>
                    <Link :href="route('admin.offices.create')" class="bg-blue-600 border border-blue-700 hover:bg-blue-700 text-white font-semibold py-2 px-10 rounded-md transition duration-300 ease-in-out text-center">Add</Link>
                </div>
            </div>
            <!-- End of Search bar -->

            <!-- Office Table -->
            <div class="w-full flex flex-wrap mt-5">
                <div class="w-full bg-gray-200">
                    <div class="w-full flex grid md:grid-cols-6 grid-cols-2">
                        <button @click="searchParams.status = 1" type="button" class="text-white px-2 py-1 uppercase transition duration-200 ease-in-out" :class="!searchParams.status ? 'bg-gray-400 hover:bg-gray-500' : 'bg-up-green'">Active</button>
                        <button @click="searchParams.status = 0" type="button" class="text-white px-2 py-1 uppercase transition duration-200 ease-in-out" :class="!searchParams.status ? 'bg-up-green' : 'bg-gray-400 hover:bg-gray-500'">Inactive</button>
                    </div>
                </div>
                <!-- SM -->
                <div class="w-full md:hidden block bg-gray-200 rounded-md px-5 py-3 my-5">
                    <!-- Table items -->
                    <template v-if="offices.length">
                        <div v-for="(item, index) in offices" :key="index" class="w-full flex flex-wrap text-sm mt-2 mb-4">
                            <template v-for="(header, key) in headers" :key="key" class="w-full flex flex-wrap">
                                <div class="w-1/2 bg-up-maroon text-white p-2 border-b border-white">{{ header['text'] }}</div>
                                <div v-if="header['value'] != 'action'" class="w-1/2 bg-white text-up-maroon p-2 border-b border-up-maroon font-semibold">{{ item[header['value']] }}</div>
                                <div v-else-if="header['value'] == 'id'" class="w-1/2 bg-white text-up-maroon p-2 border-b border-up-maroon font-semibold">{{ (index + 1) }}</div>
                                <div v-else class="w-1/2 bg-white p-2">
                                    <Link v-if="item.status == 1" :href="route('admin.offices.edit', item.id)" class="mr-2">
                                        <span class="py-1 px-2 text-yellow-700 rounded-sm border border-yellow-500 bg-yellow-300 hover:bg-yellow-400 hover:text-yellow-800">
                                            <font-awesome-icon icon="fa-solid fa-pencil"></font-awesome-icon>
                                        </span>
                                    </Link>
                                    <button type="button" @click="toggleActivate(item.id)">
                                        <span class="py-1 px-2 rounded-sm border" :class="item.status == 1 ? 'border-red-500 text-red-700 bg-red-300 hover:bg-red-400 hover:text-red-800' : 'border-green-500 text-green-700 bg-green-300 hover:bg-green-400 hover:text-green-800'">
                                            <font-awesome-icon :icon="item.status == 1 ? 'fa-solid fa-lock' : 'fa-solid fa-lock-open'"></font-awesome-icon>
                                        </span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template v-else>
                        <p class="text-center text-sm tracking-widest text-gray-600">No items found.</p>
                    </template>
                    <!-- End of Table items -->
                </div>
                <!-- MD -->
                <div class="w-full md:block hidden">
                    <!-- Table header -->
                    <div class="w-full bg-gray-200 rounded-md py-2 pl-3 pr-7 overflow-x-auto my-3">
                        <div class="w-full bg-white rounded-md px-5 py-2 text-up-maroon font-bold border-2 border-gray-300">
                            <div class="w-full flex grid grid-cols-4 gap-2">
                                <span v-for="(header, index) in headers" :key="index">{{ header['text'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-md py-2 px-3 overflow-x-auto min-tbl-h mb-5">
                        <!-- Table items -->
                        <template v-if="offices.length">
                            <div v-for="(item, index) in offices" :key="index" class="w-full bg-white rounded-md px-5 py-3 shadow my-3">
                                <div class="w-full flex grid grid-cols-4 gap-2">
                                    <template v-for="(header, key) in headers" :key="key">
                                        <span v-if="header['value'] != 'action' && header['value'] != 'id'" >{{ item[header['value']] }}</span>
                                        <span v-else-if="header['value'] == 'id'" >{{ index + 1 }}</span>
                                        <div v-else class="w-full flex flex-wrap">
                                            <Link v-if="item.status == 1" :href="route('admin.offices.edit', item.id)" class="mr-2">
                                                <span class="py-1 px-2 text-yellow-700 rounded-sm border border-yellow-500 bg-yellow-300 hover:bg-yellow-400 hover:text-yellow-800">
                                                    <font-awesome-icon icon="fa-solid fa-pencil"></font-awesome-icon>
                                                </span>
                                            </Link>
                                            <button type="button" @click="toggleActivate(item.id)">
                                                <span class="py-1 px-2 rounded-sm border" :class="item.status == 1 ? 'border-red-500 text-red-700 bg-red-300 hover:bg-red-400 hover:text-red-800' : 'border-green-500 text-green-700 bg-green-300 hover:bg-green-400 hover:text-green-800'">
                                                    <font-awesome-icon :icon="item.status == 1 ? 'fa-solid fa-lock' : 'fa-solid fa-lock-open'"></font-awesome-icon>
                                                </span>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="w-full bg-gray-100 rounded-md px-5 py-3 shadow my-3">
                                <p class="text-center text-sm tracking-widest text-gray-600">No section/s found.</p>
                            </div>
                        </template>
                        <!-- End of Table items -->
                    </div>
                </div>
                <!-- Pagination -->
                <div class="w-full">
                    <Pagination :options="pageOptions" @fetch="fetchOffices" />
                </div>
            </div>
            <!-- End of Office Table -->

        </div>
    </AuthenticatedLayout>
</template>
