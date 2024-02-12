<script>
/* Vue */
import { ref, watch, reactive, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
/* Composables */
import useTickets from '@/composables/tickets'
/* Layout */
import AuthenticatedLayout from '@/Layouts/AuthenticatedVcd.vue'
/* Components */
import UpModal from '@/Components/UpModal.vue'
import Pagination from '@/Components/Pagination.vue'
export default {
    components: {
        Link,
        Head,
        UpModal,
        Pagination,
        AuthenticatedLayout
    },
    props: ['office', 'offices', 'staffs'],
    setup() {
        const { vcdFetch, vcdAssign } = useTickets()

        const headers = ref([
            { text: 'Ticket #', value: 'control_number' },
            { text: 'Section', value: 'officeCode' },
            { text: 'Type', value: 'processName' },
            { text: 'Status', value: 'statusLabel' },
            { text: 'Date', value: 'createdDate'},
            { text: 'Action', value: 'action'}
        ])

        const searchParams = reactive({
            keyword: '',
            process_id: '',
            trashed: false
        })

        const tickets = ref([])
        const processes = ref([])

        const initialOptions = {
            to: 0,
            from: 0,
            page: 1,
            total: 0,
            current_page: 1,
            rowsPerPage: 10,
        }

        const pageOptions = reactive({...initialOptions})

        const initialPayload = {
            ticket_id: '',
            staff_id: ''
        }

        const payload = reactive({...initialPayload})

        const fetchTickets = async () => {
            await vcdFetch(searchParams, pageOptions, tickets)
        }

        const search = () => {
            fetchTickets()
        }
        
        const assign = async () => {
            await vcdAssign()
        }

        const toggleTrashed = (isTrashed) => {
            Object.assign(pageOptions, initialOptions)
            searchParams.trashed = isTrashed
            fetchTickets()
        }

        watch(
            () => searchParams.process_id,
            (value) => {
                fetchTickets()
            }
        )

        onMounted(fetchTickets)

        return {
            search,
            payload,
            tickets,
            headers,
            processes,
            pageOptions,
            fetchTickets,
            searchParams,
            toggleTrashed
        }
    }
}
</script>

<template>
    <Head title="Tickets" />
    
    <AuthenticatedLayout>
        <template #header>
            <h1 class="font-bold text-3xl text-up-maroon leading-tight uppercase">Tickets</h1>
            <p class="text-gray-700 my-2 font-semibold text-lg">{{ pageOptions.total }} {{ searchParams.trashed ? 'Archived' : 'Active'}}</p>
        </template>

        <div class="py-5 px-5 md:px-10">
            <!-- Search bar -->
            <div class="w-full flex flex-wrap text-sm">
                <div class="w-full md:w-3/4 py-1">
                    <input v-model="searchParams.keyword" type="text" class="w-full border-gray-300 rounded-md mr-2 text-sm" placeholder="Ticket #" />
                </div>
                <div class="w-full md:w-1/4 py-1 flex grid grid-cols-2 gap-2 md:pl-2">
                    <button @click="search" type="button" class="border border-up-green bg-up-green-lighter hover:bg-up-green hover:text-white transition duration-300 ease-in-out text-up-green font-semibold py-2 px-5 rounded-md">Search</button>
                    <select v-model="searchParams.process_id" id="countries" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                        <option selected value="">Select type</option>
                        <option v-for="process in office.processes" :key="process.id" :value="process.id">{{ process.name }}</option>
                    </select>
                </div>
            </div>
            <!-- End of Search bar -->

            <!-- Ticket Table -->
            <div class="mt-5">
                <!-- Tabs -->
                <div class="w-full flex bg-gray-200 rounded-md mb-5">
                    <div class="w-full bg-gray-200 rounded-md">
                        <div class="w-full flex grid grid-cols-2 md:grid-cols-6 gap-0 text-center">
                            <button @click="toggleTrashed(false)" type="button" class="text-white py-1 rounded-md z-10" :class="searchParams.trashed ? 'bg-gray-500 hover:bg-gray-400' : 'bg-up-maroon'">ACTIVE</button>
                            <button @click="toggleTrashed(true)" type="button" class="text-white py-1 rounded-r-md mx--1" :class="searchParams.trashed ? 'bg-up-maroon' : 'bg-gray-500 hover:bg-gray-400'">ARCHIVED</button>
                        </div>
                    </div>
                </div>
                <!-- SM -->
                <div class="w-full md:hidden block bg-gray-200 rounded-md px-5 py-3 mb-5">
                    <!-- Table items -->
                    <template v-if="tickets.length">
                        <div v-for="(item, index) in tickets" :key="index" class="w-full flex flex-wrap text-sm mt-2 mb-4">
                            <template v-for="(header, key) in headers" :key="key" class="w-full flex flex-wrap">
                                <div class="w-1/2 bg-up-maroon text-white p-2 border-b border-white">{{ header['text'] }}</div>
                                <div v-if="header['value'] != 'action'" class="w-1/2 bg-white text-up-maroon p-2 border-b border-up-maroon font-semibold">{{ item[header['value']] }}</div>
                                <div v-else class="w-1/2 bg-white text-up-maroon p-2 border-b border-up-maroon font-semibold">Assign to</div>
                            </template>
                        </div>
                    </template>
                    <template v-else>
                        <p class="text-center text-sm tracking-widest text-gray-600">No item/s found.</p>
                    </template>
                    <!-- End of Table items -->
                </div>
                <!-- MD -->
                <div class="md:block hidden">
                    <!-- Table header -->
                    <div class="w-full bg-gray-200 rounded-md py-2 px-3 overflow-x-auto mb-3">
                        <div class="w-full bg-white rounded-md px-5 py-2 text-up-maroon font-bold border-2 border-gray-300">
                            <div class="w-full flex grid grid-cols-6 gap-2">
                                <span v-for="(header, index) in headers" :key="index">{{ header['text'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-md py-2 px-3 overflow-x-auto min-tbl-h mb-5">
                        <!-- Table items -->
                        <template v-if="tickets.length">
                            <div v-for="(item, index) in tickets" :key="index">
                                <div class="w-full bg-white rounded-md px-5 py-3 shadow my-3" :class="item.ticketDue >= 3 ? 'border-2 border-up-maroon' : ''">
                                    <div class="w-full flex grid grid-cols-6 gap-2">
                                        <template v-for="(header, key) in headers" :key="key">
                                            <span v-if="header['value'] != 'action'">{{ item[header['value']] }}</span>
                                            <span v-else>Assign to</span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="w-full bg-gray-100 rounded-md px-5 py-3 shadow my-3">
                                <p class="text-center text-sm tracking-widest text-gray-600">No items found.</p>
                            </div>
                        </template>
                    </div>
                    <!-- End of Table items -->
                </div>
                <!-- Pagination -->
                <div class="w-full">
                    <Pagination :options="pageOptions" @fetch="fetchTickets" />
                </div>
            </div>
            <!-- End of Ticket Table -->

        </div>
    </AuthenticatedLayout>
</template>
