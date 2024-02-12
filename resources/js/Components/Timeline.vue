<script>
export default {
    props: ['items'],
    setup() {
        return {}
    }
}
</script>
<template>
    <div class="px-3 mb-7 overflow-y-scroll h-96">
        <ol class="relative border-s border-up-maroon">
            <template v-for="(item, index) in items" :key="item.id">
                <li class="mb-10 ms-4">
                    <div class="absolute w-3 h-3 rounded-full mt-1.5 -start-1.5 border" :class="index == 0 ? 'bg-up-maroon border-up-maroon' : 'bg-gray-400 border-gray-400'"></div>
                    <time class="mb-1 text-xs font-normal leading-none text-gray-400">{{ item.createdDate }}</time>
                    <h3 class="font-semibold text-gray-900">{{ item.description }}</h3>
                </li>
                <!-- <li class="mb-10 ms-4">
                    <div class="absolute w-3 h-3 bg-up-maroon rounded-full mt-1.5 -start-1.5 border border-up-maroon"></div>
                    <time class="mb-1 text-xs font-normal leading-none text-gray-400">2023-10-10 02:57 PM</time>
                    <h3 class="font-semibold text-gray-900">Request received by staff.</h3>
                </li> -->
            </template>                
        </ol>
    </div>
</template>