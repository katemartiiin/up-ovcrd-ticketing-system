<script>
/* Vue */
import { ref, reactive, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
/* Composables */
import useUsers from '@/composables/users'
/* Layout */
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
/* Components */
import DataTable from '@/Components/DataTable.vue'
import Pagination from '@/Components/Pagination.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import { watch } from 'vue'
export default {
    components: {
        Link,
        Head,
        DataTable,
        Pagination,
        DropdownLink,
        AuthenticatedLayout
    },
    props: [
        'roles',
        'offices'
    ],
    setup() {
        const { fetch, activate } = useUsers()

        const showFilters = ref(false)

        const tableFilters = reactive({
            keyword: '',
            office_id: '',
            role: '',
            status: '',
            type: 0,
        })

        const headers = ref([
            { text: '#', value: 'index' },
            { text: 'Name', value: 'name' },
            { text: 'Section', value: 'officeCode' },
            { text: 'Role', value: 'roleName' },
            { text: 'Action', value: 'action' }
        ])

        const users = ref([])

        const pageOptions = reactive({
            to: 0,
            from: 0,
            page: 1,
            total: 0,
            current_page: 1,
            rowsPerPage: 10,
        })

        const search = () => {
            fetchUsers()
        }

        const filters = () => {
            showFilters.value = !showFilters.value
        }

        const fetchUsers = async () => {
            await fetch(users, tableFilters, pageOptions)
        }

        const toggleActivate = async (id) => {
            await activate(id)
            fetchUsers()
        }

        watch (
            () => tableFilters.office_id,
            (value) => {
                fetchUsers()
            }
        )

        watch (
            () => tableFilters.role,
            (value) => {
                fetchUsers()
            }
        )
        
        watch (
            () => tableFilters.status,
            (value) => {
                fetchUsers()
            }
        )

        watch (
            () => tableFilters.type,
            (value) => {
                fetchUsers()
            }
        )

        onMounted(fetchUsers)

        return {
            users,
            search,
            filters,
            headers,
            fetchUsers,
            pageOptions,
            showFilters,
            tableFilters,
            toggleActivate
        }
    }
}
</script>

<template>
    <Head title="Users" />
    
    <AuthenticatedLayout>
        <template #header>
            <h1 class="font-bold text-3xl text-up-maroon leading-tight uppercase">Users</h1>
        </template>

        <div class="py-5 px-5 md:px-10">

            <!-- Search bar -->
            <div class="w-full flex flex-wrap text-sm">
                <div class="w-full md:w-3/4 py-1">
                    <input v-model="tableFilters.keyword" type="text" class="w-full border-gray-300 rounded-md mr-2 text-sm" placeholder="Search" />
                </div>
                <div class="w-full md:w-1/4 py-1 flex grid grid-cols-2 gap-2 pl-2">
                    <button @click="search" type="button" class="border border-up-green bg-up-green-lighter hover:bg-up-green hover:text-white transition duration-300 ease-in-out text-up-green font-semibold py-2 px-5 rounded-md">Search</button>
                    <button @click="filters" type="button" class="bg-gray-300 border border-gray-400 hover:bg-gray-400 text-gray-500 hover:text-gray-200 font-semibold py-2 px-10 rounded-md transition duration-300 ease-in-out">Filters</button>
                </div>
            </div>
            <!-- End of Search bar -->

            <!-- Filters section -->
            <div v-if="showFilters" class="w-full border-2 border-gray-300 p-5 rounded-md my-5">
                <h3 class="font-semibold text-up-maroon mb-2">Filter by:</h3>
                <div class="w-full flex grid grid-cols-3 gap-2 text-xs">
                    <!-- Filter by office -->
                    <div v-if="tableFilters.type == 0" class="w-full">
                        <label for="offices" class="block mb-2 font-medium text-gray-900">Section</label>
                        <select v-model="tableFilters.office_id" id="offices" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                            <option selected value="">Select office</option>
                            <option v-for="office in offices" :key="office.id" :value="office.id">{{ office.short_code }}</option>
                        </select>
                    </div>
                    <!-- Filter by type -->
                    <div v-if="tableFilters.type == 0" class="w-full">
                        <label for="countries" class="block mb-2 font-medium text-gray-900">Role</label>
                        <select v-model="tableFilters.role" id="countries" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                            <option selected value="">Select role</option>
                            <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                        </select>
                    </div>
                    <!-- Filter by status -->
                    <div class="w-full">
                        <label for="status" class="block mb-2 font-medium text-gray-900">Status</label>
                        <select v-model="tableFilters.status" id="status" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                            <option selected value="">Select status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- End Filters Section -->

            <!-- Ticket Table -->
            <div class="w-full flex flex-wrap mt-5">
                <div class="w-full bg-gray-200">
                    <div class="w-full flex grid md:grid-cols-6 grid-cols-2">
                        <button @click="tableFilters.type = 0" type="button" class="text-white px-2 py-1 uppercase transition duration-200 ease-in-out" :class="tableFilters.type ? 'bg-gray-400 hover:bg-gray-500' : 'bg-up-green'">Admins</button>
                        <button @click="tableFilters.type = 1" type="button" class="text-white px-2 py-1 uppercase transition duration-200 ease-in-out" :class="tableFilters.type ? 'bg-up-green' : 'bg-gray-400 hover:bg-gray-500'">Clients</button>
                    </div>
                </div>
                <!-- SM -->
                <div class="w-full md:hidden block bg-gray-200 rounded-md px-5 py-3 my-5">
                    <!-- Table items -->
                    <template v-if="users.length">
                        <div v-for="(item, index) in users" :key="index" class="w-full flex flex-wrap text-sm mt-2 mb-4">
                            <template v-for="(header, key) in headers" :key="key" class="w-full flex flex-wrap">
                                <div class="w-1/2 bg-up-maroon text-white p-2 border-b border-white">{{ header['text'] }}</div>
                                <div v-if="header['value'] != 'action' && header['value'] != 'index'" class="w-1/2 bg-white text-up-maroon p-2 border-b border-up-maroon font-semibold">{{ item[header['value']] }}</div>
                                <div v-else-if="header['value'] == 'index'" class="w-1/2 bg-white text-up-maroon p-2 border-b border-up-maroon font-semibold">{{ (index + 1) }}</div>
                                <div v-else class="w-1/2 bg-white p-2">
                                    <template v-if="item.status == 0">
                                        <Link :href="route('admin.users.show', item.id)" class="mr-2">
                                            <span class="py-1 px-2 text-yellow-700 rounded-sm border border-yellow-500 bg-yellow-300 hover:bg-yellow-400 hover:text-yellow-800">
                                                <font-awesome-icon icon="fa-solid fa-eye"></font-awesome-icon>
                                            </span>
                                        </Link>
                                    </template>
                                    <template v-else>
                                        <Link :href="route('admin.users.edit', item.id)" class="mr-2">
                                            <span class="py-1 px-2 text-yellow-700 rounded-sm border border-yellow-500 bg-yellow-300 hover:bg-yellow-400 hover:text-yellow-800">
                                                <font-awesome-icon icon="fa-solid fa-pencil"></font-awesome-icon>
                                            </span>
                                        </Link>
                                    </template>
                                    <button type="button" @click="toggleActivate(item.id)">
                                        <span class="py-1 px-2 rounded-sm border" :class="item.status == 0 ? 'text-green-700 border-green-500 bg-green-300 hover:bg-green-400 hover:text-green-800' : 'text-red-700 border-red-500 bg-red-300 hover:bg-red-400 hover:text-red-800'">
                                            <font-awesome-icon :icon="item.status == 0 ? 'fa-solid fa-lock-open' : 'fa-solid fa-lock'"></font-awesome-icon>
                                        </span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template v-else>
                        <p class="text-center text-sm tracking-widest text-gray-600">No item/s found.</p>
                    </template>
                    <!-- End of Table items -->
                </div>
                <!-- MD -->
                <div class="w-full md:block hidden">
                    <!-- Table header -->
                    <div class="w-full bg-gray-200 rounded-md py-2 px-3 overflow-x-auto mb-3">
                        <div class="w-full bg-white rounded-md px-5 py-2 text-up-maroon font-bold border-2 border-gray-300">
                            <div class="w-full flex grid grid-cols-5 gap-2">
                                <span v-for="(header, index) in headers" :key="index">{{ header['text'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-md py-2 px-3 overflow-x-auto min-tbl-h mb-5">
                        <!-- Table items -->
                        <template v-if="users.length">
                            <div v-for="(item, index) in users" :key="index" class="w-full bg-white rounded-md px-5 py-3 shadow my-3">
                                <div class="w-full flex grid grid-cols-5 gap-2">
                                    <template v-for="(header, key) in headers" :key="key">
                                        <span v-if="header['value'] != 'action' && header['value'] != 'index'" >{{ item[header['value']] }}</span>
                                        <span v-else-if="header['value'] == 'index'">{{ (index + 1) }}</span>
                                        <div v-else class="w-full flex flex-wrap">
                                            <template v-if="item.status == 0">
                                                <Link :href="route('admin.users.show', item.id)" class="mr-2">
                                                    <span class="py-1 px-2 text-yellow-700 rounded-sm border border-yellow-500 bg-yellow-300 hover:bg-yellow-400 hover:text-yellow-800">
                                                        <font-awesome-icon icon="fa-solid fa-eye"></font-awesome-icon>
                                                    </span>
                                                </Link>
                                            </template>
                                            <template v-else>
                                                <Link :href="route('admin.users.edit', item.id)" class="mr-2">
                                                    <span class="py-1 px-2 text-yellow-700 rounded-sm border border-yellow-500 bg-yellow-300 hover:bg-yellow-400 hover:text-yellow-800">
                                                        <font-awesome-icon icon="fa-solid fa-pencil"></font-awesome-icon>
                                                    </span>
                                                </Link>
                                            </template>
                                            <button type="button" @click="toggleActivate(item.id)">
                                                <span class="py-1 px-2 rounded-sm border" :class="item.status == 0 ? 'text-green-700 border-green-500 bg-green-300 hover:bg-green-400 hover:text-green-800' : 'text-red-700 border-red-500 bg-red-300 hover:bg-red-400 hover:text-red-800'">
                                                    <font-awesome-icon :icon="item.status == 0 ? 'fa-solid fa-lock-open' : 'fa-solid fa-lock'"></font-awesome-icon>
                                                </span>
                                            </button>
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
                    <Pagination :options="pageOptions" @fetch="fetchUsers" />
                </div>
            </div>
            <!-- End of Ticket Table -->

        </div>
    </AuthenticatedLayout>
</template>
