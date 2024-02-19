<script>
/* Vue */
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
/* Composables */
import useOffices from '@/composables/offices'
/* Layout */
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
/* Components */
import Timeline from '@/Components/Timeline.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'
export default {
    components: {
        Head,
        Timeline,
        Breadcrumb,
        AuthenticatedLayout
    },
    props: [
        'item',
        'users'
    ],
    setup(props) {

        const { update } = useOffices()

        const office = computed(() => props.item)

        const removedUsers = ref([])
        const removedProcess = ref([])
        const addedProcess = ref([])

        const selectedUser = ref('')
        const selectedProcess = ref('')

        const addUser = () => {
            office.value.users.push(selectedUser.value)
            selectedUser.value = ''
        }

        const removeUser = (user, index) => {
            office.value.users.splice(index, 1)
            removedUsers.value.push(user)
        }

        const addProcess = () => {
            addedProcess.value.push(selectedProcess.value)
            selectedProcess.value = ''
        }

        const removeProcess = (process, index) => {
            office.value.processes.splice(index, 1)
            removedProcess.value.push(process)
        }

        const removeNewProcess = (index) => {
            addedProcess.value.splice(index, 1)
        }

        const updateOffice = async () => {
            office.value.removed_users = removedUsers.value
            office.value.removed_process = removedProcess.value
            office.value.added_process = addedProcess.value
            await update(office, addedProcess, removedProcess, removedUsers)
        }

        return {
            office,
            addUser,
            addProcess,
            removeUser,
            removedUsers,
            selectedUser,
            addedProcess,
            updateOffice,
            removeProcess,
            removedProcess,
            selectedProcess,
            removeNewProcess,
        }
    }
}
</script>
<template>
    <Head title="Sections" />
    
    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb parentTitle="Sections" :parentLink="route('admin.offices.index')" :currentTitle="'Edit #' + office.id" />
        </template>

        <div class="py-2 px-5 md:px-10">

            <!-- Page body -->
            <div class="w-full flex flex-wrap mb-5">

                <!-- Ticket Information -->
                <div class="w-full md:w-3/4 my-2">
                    <div class="w-full flex flex-wrap mb-3">
                        <input v-model="office.name" type="text" class="w-full border-gray-300 rounded-md mr-1" placeholder="Section Name">
                    </div>
                    <div class="bg-white pt-5 pb-10 px-10 md:mr-1.5 rounded-md">
                        <div class="w-full flex flex-wrap mt-5 mb-7 pb-5">
                            <div class="w-full md:w-1/2 py-3 md:py-0 md:pr-2">
                                <label class="block text-up-green font-semibold mb-2">Short Code <span class="text-red-500 text-sm">*</span></label>
                                <input v-model="office.short_code" type="text" class="w-full border-gray-300 rounded-md mr-1 p-2" placeholder="Short Code">
                            </div>
                            <div class="w-full md:w-1/2 py-3 md:py-0">
                                <label class="block text-up-green font-semibold mb-2">POC</label>
                                <select v-model="office.head_id" id="countries" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option disabled selected value="">Select user</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="w-full mt-3 mb-10 pb-10 text-lg">
                            <label class="block text-up-green font-semibold mb-2">Description <small class="text-xs">(Optional)</small></label>
                            <textarea v-model="office.description" class="w-full bg-gray-50 rounded-md border border-gray-300 text-sm" placeholder="Enter description" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="w-full flex items-end justify-end px-2 pt-5">
                        <button @click="updateOffice" type="button" class="bg-up-green-light hover:bg-up-green text-white px-5 py-2 rounded-md transition duration-200 ease-in-out">Save</button>
                    </div>
                </div>
                <!-- End Ticket Information -->

                <!-- Users and Processes -->
                <div class="w-full md:w-1/4 mt-2">
                    <!-- Users -->
                    <div class="bg-white p-5 md:ml-1.5 rounded-md mb-5">
                        <div class="w-full mt-3">
                            <label class="block text-up-green text-xl font-bold mb-2">Users</label>                         
                            <div class="w-full flex flex-wrap my-3">
                                <div class="w-5/6">
                                    <select v-model="selectedUser" id="countries" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                        <option disabled selected value="">Select user</option>
                                        <option v-for="user in users" :key="user.id" :value="user">{{ user.name }}</option>
                                    </select>
                                </div>
                                <div class="w-1/6 pl-2">
                                    <button @click="addUser" type="button" class="w-full bg-up-green-light hover:bg-up-green text-white px-2 py-1.5 font-semibold rounded-md transition duration-200 ease-in-out">+</button>
                                </div>
                            </div>
                            <div class="w-full flex flex-wrap mt-5 mb-3 bg-gray-100 rounded-md px-5 py-3 h-64 overflow-y-scroll">
                                <template v-if="office.users.length">
                                    <div v-for="(user, index) in office.users" :key="user.id" class="w-full flex grid grid-cols-2 gap-2 text-sm my-3">
                                        <div>
                                            <p>{{ user.name }}</p>
                                        </div>
                                        <div class="text-right">
                                            <button @click="removeUser(user, index)" type="button" class="text-red-600"><font-awesome-icon icon="fa-solid fa-minus" /></button>
                                        </div>
                                    </div>
                                </template>
                                <template v-else>
                                    <p class="text-gray-700 italic text-sm mt-3">No user/s added yet.</p>
                                </template>
                            </div>
                        </div>
                    </div>
                    <!-- Processes -->
                    <div class="bg-white p-5 md:ml-1.5 rounded-md">
                        <div class="w-full mt-3">
                            <label class="block text-up-green text-xl font-bold mb-2">Processes</label>                         
                            <div class="w-full flex flex-wrap my-3">
                                <div class="w-5/6">
                                    <input v-model="selectedProcess" type="text" class="w-full border-gray-300 rounded-md mr-2" placeholder="Process">
                                </div>
                                <div class="w-1/6 pl-2">
                                    <button @click="addProcess" type="button" class="w-full bg-up-green-light hover:bg-up-green text-white px-2 py-1.5 font-semibold rounded-md transition duration-200 ease-in-out">+</button>
                                </div>
                            </div>
                            <div class="w-full flex flex-wrap mt-5 mb-3 bg-gray-100 rounded-md px-5 py-3 max-h-64 overflow-y-scroll">
                                <template v-if="office.processes.length">
                                    <div v-for="(process, index) in office.processes" :key="process.id" class="w-full flex grid grid-cols-2 gap-2 text-sm my-3">
                                        <div>
                                            <p v-if="process.name">{{ process.name }}</p>
                                            <p v-else>{{ process }}</p>
                                        </div>
                                        <div class="text-right">
                                            <button @click="removeProcess(process, index)" type="button" class="text-red-600"><font-awesome-icon icon="fa-solid fa-minus" /></button>
                                        </div>
                                    </div>
                                    <div v-for="(process, index) in addedProcess" :key="index" class="w-full flex grid grid-cols-2 gap-2 text-sm my-3">
                                        <div>
                                            <p>{{ process }}</p>
                                        </div>
                                        <div class="text-right">
                                            <button @click="removeNewProcess(index)" type="button" class="text-red-600"><font-awesome-icon icon="fa-solid fa-minus" /></button>
                                        </div>
                                    </div>
                                </template>
                                <template v-else>
                                    <p class="text-gray-700 italic text-sm mt-3">No processes added yet.</p>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Users and Processes -->

            </div>
            <!-- End Page body -->

        </div>
    </AuthenticatedLayout>

</template>