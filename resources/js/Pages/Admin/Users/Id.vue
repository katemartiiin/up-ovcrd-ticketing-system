<script>
/* Vue */
import { Head } from '@inertiajs/vue3'
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
    props: [
        'item'
    ],
    setup() {
        return {

        }
    }
}
</script>
<template>
    <Head title="Users" />
    
    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb parentTitle="Users" :parentLink="route('admin.users.index')" :currentTitle="'View #' + item.id" />
        </template>

        <div class="py-2 px-5 md:px-10">

            <!-- Page body -->
            <div class="w-full flex flex-wrap mb-5">

                <!-- User Information -->
                <div class="w-full my-2">
                    <!-- <div class="w-full flex flex-wrap mb-3">
                        <input type="text" class="w-full border-gray-300 rounded-md mr-1" placeholder="Office Name">
                    </div> -->
                    <div class="bg-white pt-5 pb-10 px-10 md:mr-1.5 rounded-md">
                        <div class="w-full flex flex-wrap mt-5 mb-7 pb-5">
                            <div class="w-full md:w-1/2 py-3 md:py-0 md:px-2">
                                <label class="block text-up-green font-semibold mb-2">First Name</label>
                                <p class="text-lg font-semibold">{{ item.first_name  }}</p>
                            </div>
                            <div class="w-full md:w-1/2 py-3 md:py-0 md:px-2">
                                <label class="block text-up-green font-semibold mb-2">Last Name</label>
                                <p class="text-lg font-semibold">{{ item.last_name  }}</p>
                            </div>
                        </div>
                        <div class="w-full flex flex-wrap mt-3 mb-7 pb-5 text-lg">
                            <div class="w-full md:w-1/2 py-3 md:py-0 md:px-2">
                                <label class="block text-up-green font-semibold mb-2">Email</label>
                                <p class="text-lg font-semibold">{{ item.email  }}</p>
                            </div>
                            <div class="w-full md:w-1/2 py-3 md:py-0 md:px-2">
                                <label class="block text-up-green font-semibold mb-2">Section</label>
                                <p class="text-lg font-semibold">{{ item.office.name }}</p>
                            </div>
                        </div>
                        <div class="w-full md:w-1/2 mt-3 mb-10 pb-10 md:px-2">
                            <label class="block text-up-green font-semibold mb-2">Role</label>
                            <p class="text-lg font-semibold">Admin</p>
                        </div>
                    </div>
                </div>
                <!-- End User Information -->

            </div>
            <!-- End Page body -->

        </div>
    </AuthenticatedLayout>

</template>