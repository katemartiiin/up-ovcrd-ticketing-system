<script>
import Pagination from '@/Components/Pagination.vue'
export default {
    components: {
        Pagination
    },
    props: [
        'items',
        'headers',
        'tabs'
    ],
    setup() {
        return {

        }
    }
}
</script>
<template>
    <div class="w-full flex flex-wrap">
        <div v-if="tabs" class="w-full bg-gray-200">
            <div class="w-full flex grid md:grid-cols-6 grid-cols-2">
                <button type="button" class="bg-up-green text-white px-2 py-1 uppercase">Active</button>
                <button type="button" class="bg-gray-400 hover:bg-gray-500 transition duration-200 ease-in-out text-white px-2 py-1 uppercase">Archived</button>
            </div>
        </div>
        <!-- SM -->
        <div class="w-full md:hidden block bg-gray-200 rounded-md px-5 py-3" :class="tabs ? 'my-5' : 'mb-5'">
            <!-- Table items -->
            <template v-if="items.length">
                <div v-for="(item, index) in items" :key="index" class="w-full flex flex-wrap text-sm mt-2 mb-4">
                    <template v-for="(header, key) in headers" :key="key" class="w-full flex flex-wrap">
                        <div class="w-1/2 bg-up-maroon text-white p-2 border-b border-white">{{ header['text'] }}</div>
                        <div v-if="header['value'] != 'action'" class="w-1/2 bg-white text-up-maroon p-2 border-b border-up-maroon font-semibold">{{ item[header['value']] }}</div>
                        <div v-else class="w-1/2 bg-white p-2">
                            <slot name="action" />
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
        <div class="md:block hidden w-full bg-gray-200 rounded-md p-5 overflow-x-auto min-tbl-h" :class="tabs ? 'my-5' : 'mb-5'">
            <!-- Table header -->
            <div class="w-full bg-white rounded-md px-5 py-2 text-up-maroon font-bold border-2 border-gray-300">
                <div class="w-full flex grid grid-cols-6 gap-2">
                    <span v-for="(header, index) in headers" :key="index">{{ header['text'] }}</span>
                </div>
            </div>
            <!-- Table items -->
            <template v-if="items.length">
                <div class="w-full bg-white rounded-md px-5 py-3 shadow my-3">
                    <div class="w-full flex grid grid-cols-6 gap-2">
                        <template v-for="(item, index) in items" :key="index">
                            <template v-for="(header, key) in headers" :key="key">
                                <span v-if="header['value'] != 'action'" >{{ item[header['value']] }}</span>
                                <slot v-else name="action" :item="item.id" />
                            </template>
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
        <!-- Pagination -->
        <div class="w-full">
            <Pagination />
        </div>
    </div>
</template>