<script>
/* Vue */
import { ref, watch, reactive, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
/* Composables */
import useReports from '@/composables/reports'
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
    props: ['office_id', 'offices', 'processes', 'research_ids'],
    setup(props) {

        const { fetch, generate } = useReports()

        const headers = ref([
            { text: 'Date', value: 'createdDate' },
            { text: 'Report', value: 'title' },
            { text: 'Download', value: 'fileUrl' }
        ])

        const items = ref([])
        const processes = ref(props.processes)
        const selectedOffice = ref('')

        const filters = reactive({
            office_id: props.office_id,
            process_id: '',
            research_id: '',
            dates: '',
            status: ''
        })

        const pageOptions = reactive({
            to: 0,
            from: 0,
            page: 1,
            total: 0,
            current_page: 1,
            rowsPerPage: 10,
        })

        const fetchReports = async () => {
            await fetch(items, pageOptions)
        }

        const generateReport = async () =>{
            await generate(filters)
        }

        watch(
            () => selectedOffice.value,
            (value) => {
                if (value != '') {
                    processes.value = value.processes
                    filters.office_id = value.id
                } else {
                    filters.process_id = ''
                }
            }
        )

        onMounted(fetchReports)

        return {
            items,
            filters,
            headers,
            processes,
            pageOptions,
            fetchReports,
            generateReport,
            selectedOffice,
        }
    }
}
</script>

<template>
    <Head title="Reports" />
    
    <AuthenticatedLayout>
        <template #header>
            <h1 class="font-bold text-3xl text-up-maroon leading-tight uppercase">Reports</h1>
        </template>

        <div class="py-5 px-5 md:px-10">
            <!-- Search bar -->
            <div class="w-full flex flex-wrap mb-4">
                <div class="w-full flex flex-wrap md:w-5/6 md:pr-1 py-2">
                    <div class="w-full flex grid grid-cols-2 md:grid-cols-12 gap-2">
                        <template v-if="office_id == ''">
                            <div class="col-span-2">
                                <select v-model="selectedOffice" id="countries" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                    <option selected value="">Select section</option>
                                    <option v-for="office in offices" :key="office.id" :value="office">{{ office.name }}</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <select v-model="filters.process_id" id="countries" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                    <option selected value="">Select process</option>
                                    <option v-for="process in processes" :key="process.id" :value="process.id">{{ process.name }}</option>
                                </select>
                            </div>
                        </template>
                        <template v-else>
                            <div class="col-span-2">
                                <select v-model="filters.process_id" id="countries" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                    <option selected value="">Select process</option>
                                    <option v-for="process in processes" :key="process.id" :value="process.id">{{ process.name }}</option>
                                </select>
                            </div>
                        </template>
                        <div class="col-span-2">
                            <select v-model="filters.research_id" id="countries" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                <option selected value="">Select Research Id</option>
                                <option v-for="research_id in research_ids" :key="research_id.id" :value="research_id.id">{{ research_id.research_code }}</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <select v-model="filters.status" id="countries" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                <option selected value="">Select Status</option>
                                <option value="0">Active</option>
                                <option value="1">Completed</option>
                                <option value="2">Resolved</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <VueDatePicker v-model="filters.dates" placeholder="Select range" :max-date="new Date()" :enable-time-picker="false" range></VueDatePicker>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <button @click="generateReport" type="button" class="border border-up-maroon bg-up-maroon-lighter hover:bg-up-maroon rounded-md w-full text-white transition duration-300 ease-in-out font-semibold py-1.5 px-3">Generate</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Search bar -->
            <!-- Ticket Table -->
            <div class="w-full flex flex-wrap">
                <!-- SM -->
                <div class="w-full md:hidden block bg-gray-200 rounded-md px-5 py-3 mb-5">
                    <!-- Table items -->
                    <template v-if="items.length">
                        <div v-for="(item, index) in items" :key="index" class="w-full flex flex-wrap text-sm mt-2 mb-4">
                            <template v-for="(header, key) in headers" :key="key" class="w-full flex flex-wrap">
                                <div class="w-1/2 bg-up-maroon text-white p-2 border-b border-white">{{ header['text'] }}</div>
                                <div class="w-1/2 bg-white text-up-maroon p-2 border-b border-up-maroon font-semibold">
                                    <a v-if="header['value'] == 'fileUrl'" :href="item[header['value']]" class="text-up-green" download>
                                        <font-awesome-icon icon="fa-solid fa-download" />
                                    </a>
                                    <span v-else>{{ item[header['value']] }}</span>
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
                    <div class="w-full bg-gray-200 rounded-md py-2 px-3 overflow-x-auto mb-3">
                        <div class="w-full bg-white rounded-md px-5 py-2 text-up-maroon font-bold border-2 border-gray-300">
                            <div class="w-full flex flex-wrap">
                            <div class="w-1/6">
                                Date
                            </div>
                            <div class="w-4/6">
                                Name
                            </div>
                            <div class="w-1/6">
                                Download
                            </div>
                        </div>
                        </div>
                    </div>
                    <!-- Table header -->
                    <div class="w-full bg-gray-200 rounded-md py-2 px-3 overflow-x-auto min-tbl-h mb-5">
                        <!-- Table items -->
                        <template v-if="items.length">
                            <div v-for="(item, index) in items" :key="index" class="w-full flex flex-wrap bg-white rounded-md px-5 py-2.5 shadow my-3">
                                <div class="w-1/6">
                                    {{ item['createdDate'] }}
                                </div>
                                <div class="w-4/6">
                                    {{ item['title'] }}
                                </div>
                                <div class="w-1/6">
                                    <a :href="item['fileUrl']" class="text-up-green" download>
                                        <font-awesome-icon icon="fa-solid fa-download" />
                                    </a>
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
                    <Pagination :options="pageOptions" @fetch="fetchReports" />
                </div>
            </div>
            <!-- End of Ticket Table -->

        </div>
    </AuthenticatedLayout>
</template>
