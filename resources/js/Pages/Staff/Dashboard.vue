<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedStaff.vue'
import { Head } from '@inertiajs/vue3'
const dashboardProps = defineProps(['activities', 'notifications'])
</script>

<template>
    <Head title="Dashboard" />
    
    <AuthenticatedLayout>
        <div class="py-12 px-3">
            <div class="my-5 w-full flex grid md:grid-cols-2 grid-cols-1 gap-4 sm:px-6 lg:px-8">
                <div>
                    <h2 class="mt-3 mb-5 text-xl text-up-green font-medium">RECENT ACTIVITY</h2>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg px-2 py-5">
                        <template v-if="dashboardProps.activities.length">
                            <div v-for="activity in dashboardProps.activities" :key="activity.id">
                                <div class="px-6 py-1 my-2 text-gray-900">{{ activity.createdDate }} <span class="ml-2">{{ activity.action }}</span></div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="px-6 py-1 my-2 text-gray-900 italic">No recent activities.</div>
                        </template>
                    </div>
                </div>
                <div>
                    <h2 class="mt-3 mb-5 text-xl text-up-maroon font-medium">LATEST NOTIFICATIONS</h2>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg px-2 py-5">
                        <template v-if="dashboardProps.notifications.length">
                            <div v-for="notification in dashboardProps.notifications" :key="notification.id">
                                <div class="px-6 py-1 my-2 text-gray-900">{{ notification.dashboardDate }} <span class="ml-2">{{ notification.description }}</span></div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="px-6 py-1 my-2 text-gray-900 italic">No recent notifications.</div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
