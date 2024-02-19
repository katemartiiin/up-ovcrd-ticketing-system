<script>
/* Vue */
import { ref, reactive, watch, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
/* Composables */
import useResearch from '@/composables/research'
/* Layout */
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
/* Components */
import UpModal from '@/Components/UpModal.vue'
import Dropdown from '@/Components/Dropdown.vue'
import ErrorText from '@/Components/ErrorText.vue'
import Pagination from '@/Components/Pagination.vue'
export default {
    components: {
        Link,
        Head,
        UpModal,
        Dropdown,
        ErrorText,
        Pagination,
        AuthenticatedLayout
    },
    props: ['offices'],
    setup() {
        const { fetch, store, errors, update, fetchStaffs, fetchAvailable } = useResearch()

        const headers = ref([
            { text: '#', value: 'index' },
            { text: 'Code', value: 'research_code' },
            { text: 'Section', value: 'officeCode' },
            { text: '# of Staff', value: 'staffCount' },
            { text: 'Action', value: 'action' }
        ])

        const params = reactive({
            office_id: '',
            keyword: ''
        })

        const staff = ref([])
        const available = ref([])
        const researchIds = ref([])
        const showStaffModal = ref(false)
        const showCreateModal = ref(false)
        const selectedResearch = ref('')

        const initialPayload = {
            code: '',
            office_id: ''
        }

        const payload = reactive({...initialPayload})

        const initialOptions = {
            to: 0,
            from: 0,
            page: 1,
            total: 0,
            current_page: 1,
            rowsPerPage: 10,
        }

        const pageOptions = reactive({...initialOptions})

        const initialStaffs = {
            research_id: '',
            add_id: '',
            remove_id: '',
            function: ''
        }

        const payloadStaffs = reactive({...initialStaffs})

        const fetchResearch = async () => {
            await fetch(researchIds, params, pageOptions)
        }

        const getStaff = async () => {
            await fetchStaffs(selectedResearch, staff)
        }

        const getAvailable = async () => {
            await fetchAvailable(selectedResearch, available)
        }

        const toggleStaff = (researchId) => {
            showCreateModal.value = false

            if (!showStaffModal.value) {
                selectedResearch.value = researchId
                getStaff()
                getAvailable()
            }

            showStaffModal.value = !showStaffModal.value
        }

        const toggleCreate = () => {
            showCreateModal.value = !showCreateModal.value
        }

        const createResearch = async () => {
            await store(payload, initialPayload)
            fetchResearch()
        }

        const updateStaff = async (fx, id) => {
            payloadStaffs.function = fx
            payloadStaffs.research_id = selectedResearch.value.id

            if (fx == 'remove') {
                payloadStaffs.remove_id = id
            }
            
            await update(payloadStaffs, initialStaffs)
            getStaff()
            getAvailable()
        }

        onMounted(fetchResearch)

        return {
            staff,
            params,
            errors,
            payload,
            headers,
            available,
            updateStaff,
            toggleStaff,
            researchIds,
            pageOptions,
            toggleCreate,
            payloadStaffs,
            fetchResearch,
            createResearch,
            showStaffModal,
            showCreateModal,
            selectedResearch
        }
    }
}
</script>

<template>
    <Head title="Research IDs" />
    
    <AuthenticatedLayout>
        <template #header>
            <h1 class="font-bold text-3xl text-up-maroon leading-tight uppercase">Research IDs</h1>
            <p class="text-gray-700 my-2 font-semibold text-lg">{{ pageOptions.total }} Active</p>
        </template>

        <div class="py-5 px-5 md:px-10">

             <!-- Research Id -->
             <UpModal :show="showCreateModal" @close="toggleCreate()">
                <div class="w-full flex flex-wrap">
                    <div class="w-full bg-up-maroon text-white py-5 px-7">
                        <h2 class="text-xl">New Research ID</h2>
                    </div>
                    <div class="w-full py-5 px-7">
                        <div class="w-full flex grid grid-cols-1 md:grid-cols-2 gap-2 mt-2 mb-7">
                            <div>
                                <input v-model="payload.code" type="text" class="w-full p-2 rounded-md bg-gray-50 border border-gray-200 text-sm" placeholder="Research Code" />
                                <ErrorText v-if="errors.code" :message="errors.code[0]" />
                            </div>
                            <div>
                                <select v-model="payload.office_id" id="countries" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                <option selected value="">Select section</option>
                                    <option v-for="office in offices" :key="office.id" :value="office.id">{{ office.short_code }}</option>
                                </select>
                                <ErrorText v-if="errors.office_id" :message="errors.office_id[0]" />
                            </div>
                        </div>
                        <div class="w-full flex items-end justify-end my-3">
                            <button @click="createResearch" type="button" class="bg-up-green hover:bg-up-green-light text-white px-3 py-2 rounded-md transition duration-200 ease-in-out">Create</button>
                        </div>
                    </div>
                </div>
            </UpModal>
            <!-- End Research Id -->

            <!-- Research Staff-->
            <UpModal :show="showStaffModal" @close="toggleStaff('')">
                <div class="w-full flex flex-wrap">
                    <div class="w-full bg-up-maroon text-white py-5 px-7">
                        <h2 class="text-xl">{{ selectedResearch.research_code }}</h2>
                    </div>
                    <div class="w-full py-5 px-7">
                        <div class="w-full flex flex-wrap mt-2 mb-5">
                            <label class="font-semibold block my-auto text-sm">Add:</label>
                            <select v-model="payloadStaffs.add_id" id="countries" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 mx-2 p-2 w-1/2">
                                <option selected value="">Select staff</option>
                                <option v-for="staff in available" :key="staff.id" :value="staff.id">{{ staff.name }}</option>
                            </select>
                            <button @click="updateStaff('add', 0)" type="button" class="bg-up-green text-white py-1 px-2 rounded-md text-center">+</button>
                        </div>
                        <div class="w-full mt-2 mb-3 text-sm">
                            <p>Current Staff/s:</p>
                        </div>
                        <div class="w-full bg-gray-100 h-60 rounded-md shadow-md flex flex-wrap mt-2 mb-5 py-5 pl-3 overflow-y-scroll text-sm">
                            <template v-if="staff.length">
                                <div v-for="st in staff" :key="st.id" class="w-full flex grid grid-cols-2 gap-1 my-1 h-8">
                                    <span>{{ st.staff.name }}</span>
                                    <span>
                                        <button @click="updateStaff('remove', st.id)" type="button" class="bg-up-maroon text-white py-1 px-3 text-xs">-</button>
                                    </span>
                                </div>
                            </template>
                            <template>
                                <p class="text-sm my-2 italic text-gray-600">No staffs added yet. </p>
                            </template>
                        </div>
                        <div class="w-full flex items-end justify-end my-2">
                            <button @click="toggleStaff('')" type="button" class="bg-gray-500 hover:bg-gray-400 text-white px-3 py-2 rounded-md mr-2 transition duration-200 ease-in-out">Close</button>
                        </div>
                    </div>
                </div>
            </UpModal>
            <!-- End Research Staff -->

            <!-- Search bar -->
            <div class="w-full flex flex-wrap text-sm">
                <div class="w-full md:w-2/3 py-1 md:pr-2">
                    <input v-model="params.keyword" type="text" class="w-full border-gray-300 rounded-md mr-2 text-sm" placeholder="Search" />
                </div>
                <div class="w-full md:w-1/3 py-1 flex grid grid-cols-2 md:grid-cols-3 gap-2">
                    <select v-model="params.office_id" id="countries" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                        <option selected value="">Select section</option>
                        <option v-for="office in offices" :key="office.id" :value="office.id">{{ office.short_code }}</option>
                    </select>
                    <button @click="fetchResearch" type="button" class="border border-up-green bg-up-green-lighter hover:bg-up-green hover:text-white transition duration-300 ease-in-out text-up-green font-semibold py-2 px-5 rounded-md">
                        Search</button>
                    <button @click="toggleCreate" type="button" class="col-span-2 md:col-span-1 bg-blue-200 border border-blue-500 hover:bg-blue-600 text-blue-600 hover:text-white font-semibold py-2 px-10 rounded-md transition duration-300 ease-in-out">Create</button>
                </div>
            </div>
            <!-- End of Search bar -->

            <!-- Research IDs Table -->
            <div class="mt-5">
                <!-- SM -->
                <div class="w-full md:hidden block bg-gray-200 rounded-md px-5 py-3 mb-5">
                    <!-- Table items -->
                    <template v-if="researchIds.length">
                        <div v-for="(item, index) in researchIds" :key="index" class="w-full flex flex-wrap text-sm mt-2 mb-4">
                            <template v-for="(header, key) in headers" :key="key" class="w-full flex flex-wrap">
                                <div class="w-1/2 bg-up-maroon text-white p-2 border-b border-white">{{ header['text'] }}</div>
                                <div v-if="header['value'] != 'action' && header['value'] != 'index'" class="w-1/2 bg-white text-up-maroon p-2 border-b border-up-maroon font-semibold">{{ item[header['value']] }}</div>
                                <div v-else-if="header['value'] == 'index'" class="w-1/2 bg-white text-up-maroon p-2 border-b border-up-maroon font-semibold">{{ (index + 1) }}</div>
                                <div v-else class="w-1/2 bg-white p-2">
                                    <button @click="toggleStaff(item)" type="button" class="bg-up-green text-white py-1 px-2 text-xs rounded-md">Manage Staff</button>
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
                <div class="md:block hidden">
                    <!-- Table header -->
                    <div class="w-full bg-gray-200 rounded-md py-2 pl-3 pr-7 overflow-x-auto mb-3">
                        <div class="w-full bg-white rounded-md px-5 py-2 text-up-maroon font-bold border-2 border-gray-300">
                            <div class="w-full flex grid grid-cols-6 gap-2">
                                <span v-for="(header, index) in headers" :key="index">{{ header['text'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-md py-2 px-3 overflow-x-auto min-tbl-h mb-5">
                        <template v-if="researchIds.length">
                            <div v-for="(item, index) in researchIds" :key="index" class="w-full bg-white rounded-md px-5 py-3 shadow my-3">
                                <div class="w-full flex grid grid-cols-6 gap-2">
                                    <template v-for="(header, key) in headers" :key="key">
                                        <span v-if="header['value'] != 'action' && header['value'] != 'index'" >{{ item[header['value']] }}</span>
                                        <span v-else-if="header['value'] == 'index'" >{{ (index + 1) }}</span>
                                        <div v-else class="w-full flex flex-wrap">
                                            <button @click="toggleStaff(item)" type="button" class="bg-up-green text-white py-1 px-2 text-xs rounded-md">Manage Staff</button>
                                        </div>
                                    </template>
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
                    <Pagination :options="pageOptions" @fetch="fetchResearch" />
                </div>
            </div>
            <!-- End of Ticket Table -->

        </div>
    </AuthenticatedLayout>
</template>
