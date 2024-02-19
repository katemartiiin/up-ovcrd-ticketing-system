<script>
/* Vue */
import { Head } from '@inertiajs/vue3'
/* Layout */
import AdminLayout from '@/Layouts/Authenticated.vue'
import VcdLayout from '@/Layouts/AuthenticatedVcd.vue'
import ClientLayout from '@/Layouts/AuthenticatedClient.vue'
import StaffLayout from '@/Layouts/AuthenticatedStaff.vue'
/* Components */
import ProfilePartials from './ProfilePartials.vue'
export default {
    components: {
        Head,
        VcdLayout,
        StaffLayout,
        AdminLayout,
        ClientLayout,
        ProfilePartials
    },
    props: ['user', 'status'],
    setup () {


        return {
            //
        }
    }
}
</script>

<template>
    <Head title="Profile" />

    <template v-if="user.roleName == 'Admin'">
        <AdminLayout>
            <template #header>
                <h1 class="font-bold text-3xl text-up-maroon leading-tight uppercase">Profile</h1>
            </template>
            <ProfilePartials :user="user" />
        </AdminLayout>
    </template>

    <template v-else-if="user.roleName == 'Staff'">
        <StaffLayout>
            <template #header>
                <h1 class="font-bold text-3xl text-up-maroon leading-tight uppercase">Profile</h1>
            </template>
            <ProfilePartials :user="user" />
        </StaffLayout>
    </template>

    <template v-else-if="user.roleName == 'Director' || user.roleName == 'Vice-Chancellor'">
        <VcdLayout>
            <template #header>
                <h1 class="font-bold text-3xl text-up-maroon leading-tight uppercase">Profile</h1>
            </template>
            <ProfilePartials :user="user" />
        </VcdLayout>
    </template>

    <template v-else>
        <ClientLayout>
            <template #header>
                <h1 class="font-bold text-3xl text-up-maroon leading-tight uppercase">Profile</h1>
            </template>
            <ProfilePartials :user="user" />
        </ClientLayout>
    </template>

</template>
