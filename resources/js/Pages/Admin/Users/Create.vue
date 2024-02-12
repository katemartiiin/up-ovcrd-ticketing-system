<script>
/* Vue */
import { reactive } from 'vue'
import { Head } from '@inertiajs/vue3'
/* Composables */
import useUsers from '@/composables/users'
/* Layout */
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
/* Components */
import Breadcrumb from '@/Components/Breadcrumb.vue'
import ErrorText from '@/Components/ErrorText.vue'
export default {
    components: {
        Head,
        ErrorText,
        Breadcrumb,
        AuthenticatedLayout
    },
    props: ['offices', 'roles'],
    setup() {

        const { store, errors } = useUsers()

        const initialPayload = {
            first_name: '',
            last_name: '',
            email: '',
            office_id: '',
            role: ''
        }

        const payload = reactive({...initialPayload})

        const createUser = async () => {
            await store(payload, initialPayload)
        }

        return {
            errors,
            payload,
            createUser
        }
    }
}
</script>
<template>
    <Head title="Users" />
    
    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb parentTitle="Users" :parentLink="route('admin.users.index')" currentTitle="Create" />
        </template>

        <div class="py-2 px-5 md:px-10">

            <!-- Page body -->
            <div class="w-full flex flex-wrap mb-5">

                <!-- User Information -->
                <div class="w-full my-2">
                    <div class="w-full flex flex-wrap mb-3">
                        <h1 class="text-up-maroon font-bold text-2xl md:text-3xl">New User</h1>
                    </div>
                    <div class="bg-white pt-5 pb-10 px-10 md:mr-1.5 rounded-md">
                        <div class="w-full flex flex-wrap mt-5 mb-7 pb-5">
                            <div class="w-full md:w-1/2 py-3 md:py-0 md:px-2">
                                <label class="block text-up-green font-semibold mb-2">First Name <span class="text-red-500 text-sm">*</span></label>
                                <input v-model="payload.first_name" type="text" class="w-full border-gray-300 rounded-md mr-1" placeholder="First Name">
                                <ErrorText v-if="errors.first_name" :message="errors.first_name[0]" />
                            </div>
                            <div class="w-full md:w-1/2 py-3 md:py-0 md:px-2">
                                <label class="block text-up-green font-semibold mb-2">Last Name <span class="text-red-500 text-sm">*</span></label>
                                <input v-model="payload.last_name" type="text" class="w-full border-gray-300 rounded-md mr-1" placeholder="Last Name">
                                <ErrorText v-if="errors.last_name" :message="errors.last_name[0]" />
                            </div>
                        </div>
                        <div class="w-full flex flex-wrap mt-3 mb-7 pb-5 text-lg">
                            <div class="w-full md:w-1/2 py-3 md:py-0 md:px-2">
                                <label class="block text-up-green font-semibold mb-2">Email <span class="text-red-500 text-sm">*</span></label>
                                <input v-model="payload.email" type="email" class="w-full border-gray-300 rounded-md mr-1" placeholder="email@upd.edu.ph">
                                <ErrorText v-if="errors.email" :message="errors.email[0]" />
                            </div>
                            <div class="w-full md:w-1/2 py-3 md:py-0 md:px-2">
                                <label class="block text-up-green font-semibold mb-2">Section <span class="text-red-500 text-sm">*</span></label>
                                <select v-model="payload.office_id" id="offices" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                    <option disabled selected value="">Select section</option>
                                    <template v-for="office in offices" :key="office.id">
                                        <option v-if="office.id != 1" :value="office.id">{{ office.short_code }}</option>
                                    </template>
                                </select>
                                <ErrorText v-if="errors.office_id" :message="errors.office_id[0]" />
                            </div>
                        </div>
                        <div class="w-full flex flex-wrap mt-3 mb-10 pb-10">
                            <div class="w-full md:w-1/2 py-3 md:py-0 md:px-2">
                                <label class="block text-up-green font-semibold mb-2">Role <span class="text-red-500 text-sm">*</span></label>
                                <select v-model="payload.role" id="countries" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                    <option disabled selected value="">Select role</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                                </select>
                                <ErrorText v-if="errors.role" :message="errors.role[0]" />
                            </div>
                        </div>
                    </div>
                    <div class="w-full flex items-end justify-end px-2 pt-5">
                        <button @click="createUser" type="button" class="bg-up-green-light hover:bg-up-green text-white px-5 py-2 rounded-md transition duration-200 ease-in-out">Create</button>
                    </div>
                </div>
                <!-- End User Information -->

            </div>
            <!-- End Page body -->

        </div>
    </AuthenticatedLayout>

</template>