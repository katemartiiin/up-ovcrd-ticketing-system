<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedClient.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
    <Head title="Dashboard" />
    
    <AuthenticatedLayout>
        <div class="py-12 px-3">
            <div class="my-5 w-full flex grid md:grid-cols-2 grid-cols-1 gap-4 sm:px-6 lg:px-8">
                <div>
                    <h2 class="mt-3 mb-5 text-xl text-up-green font-medium">RECENT ACTIVITY</h2>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg px-2 py-5">
                        <div class="px-6 py-1 my-2 text-gray-900">12 OCT 23 09:00 <span class="ml-2">Juan has closed ticket <span class="underline">#20230101</span>.</span></div>
                        <div class="px-6 py-1 my-2 text-gray-900">12 OCT 23 09:00 <span class="ml-2">Juan has closed ticket <span class="underline">#20230101</span>.</span></div>
                        <div class="px-6 py-1 my-2 text-gray-900">12 OCT 23 09:00 <span class="ml-2">Juan has closed ticket <span class="underline">#20230101</span>.</span></div>
                    </div>
                </div>
                <div>
                    <h2 class="mt-3 mb-5 text-xl text-up-maroon font-medium">LATEST NOTIFICATIONS</h2>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg px-2 py-5">
                        <div class="px-6 py-1 my-2 text-gray-900">12 OCT 23 09:00 <span class="ml-2">Juan has closed ticket <span class="underline">#20230101</span>.</span></div>
                        <div class="px-6 py-1 my-2 text-gray-900">12 OCT 23 09:00 <span class="ml-2">Juan has closed ticket <span class="underline">#20230101</span>.</span></div>
                        <div class="px-6 py-1 my-2 text-gray-900">12 OCT 23 09:00 <span class="ml-2">Juan has closed ticket <span class="underline">#20230101</span>.</span></div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
