<script>
/* Vue */
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
/* Composables */
import useUsers from '@/composables/users'
/* Layout */
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
/* Components */
import Breadcrumb from '@/Components/Breadcrumb.vue'
import Timeline from '@/Components/Timeline.vue'
export default {
    components: {
        Head,
        Timeline,
        Breadcrumb,
        AuthenticatedLayout
    },
    props: ['offices', 'roles', 'item'],
    setup(props) {

        const { update } = useUsers()

        const user = computed(() => props.item)

        const updateUser = async () => {
            await update(user.value)
        }

        return {
            user,
            updateUser
        }
    }
}
</script>
<template>
    <Head title="Users" />
    
    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb parentTitle="Users" :parentLink="route('admin.users.index')" :currentTitle="'Edit #' + user.id" />
        </template>

        <div class="py-2 px-5 md:px-10">

            <!-- Page body -->
            <div class="w-full flex flex-wrap mb-5">

                <!-- User Information -->
                <div class="w-full my-2">
                    <div class="w-full flex flex-wrap mb-3">
                        <h1 class="text-up-maroon font-bold text-2xl md:text-3xl">Update User #{{ user.id }}</h1>
                    </div>
                    <div class="bg-white pt-5 pb-10 px-10 md:mr-1.5 rounded-md">
                        <div class="w-full flex flex-wrap mt-5 mb-7 pb-5">
                            <div class="w-full md:w-1/2 py-3 md:py-0 md:px-2">
                                <label class="block text-up-green font-semibold mb-2">First Name <span class="text-red-500 text-sm">*</span></label>
                                <input v-model="user.first_name" type="text" class="w-full border-gray-300 rounded-md mr-1" placeholder="First Name">
                            </div>
                            <div class="w-full md:w-1/2 py-3 md:py-0 md:px-2">
                                <label class="block text-up-green font-semibold mb-2">Last Name <span class="text-red-500 text-sm">*</span></label>
                                <input v-model="user.last_name" type="text" class="w-full border-gray-300 rounded-md mr-1" placeholder="Last Name">
                            </div>
                        </div>
                        <div class="w-full flex flex-wrap mt-3 mb-7 pb-5 text-lg">
                            <div class="w-full md:w-1/2 py-3 md:py-0 md:px-2">
                                <label class="block text-up-green font-semibold mb-2">Email <span class="text-red-500 text-sm">*</span></label>
                                <input v-model="user.email" type="email" class="w-full border-gray-300 rounded-md mr-1" placeholder="email@upd.edu.ph">
                            </div>
                            <div class="w-full md:w-1/2 py-3 md:py-0 md:px-2">
                                <label class="block text-up-green font-semibold mb-2">Section <span class="text-red-500 text-sm">*</span></label>
                                <select v-model="user.office_id" id="offices" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                    <option disabled selected value="">Select section</option>
                                    <option v-for="office in offices" :key="office.id" :value="office.id">{{ office.short_code }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="w-full flex flex-wrap mt-3 mb-10 pb-10">
                            <div class="w-full md:w-1/2 py-3 md:py-0 md:px-2">
                                <label class="block text-up-green font-semibold mb-2">Role <span class="text-red-500 text-sm">*</span></label>
                                <select v-model="user.role" id="countries" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                    <option disabled selected value="">Select role</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="w-full flex items-end justify-end px-2 pt-5">
                        <button @click="updateUser" type="button" class="bg-up-green-light hover:bg-up-green text-white px-5 py-2 rounded-md transition duration-200 ease-in-out">Save</button>
                    </div>
                </div>
                <!-- End User Information -->

            </div>
            <!-- End Page body -->

        </div>
    </AuthenticatedLayout>

</template>