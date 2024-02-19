<script>
/* Vue */
import { computed } from 'vue'
export default {
    props: ['options'],
    emits: ['fetch'],
    setup (props, { emit }) {
        const pageOptions = computed(() => props.options)

        const prevPage = () => {
            if (pageOptions.value.current_page > 1) {
                pageOptions.value.page--;
                emit('fetch')
            }
        }

        const nextPage = () => {
            if (pageOptions.value.to != pageOptions.value.total) {
                pageOptions.value.page++;
                emit('fetch')
            }
        }

        return {
            prevPage,
            nextPage,
            pageOptions
        }
    }
}
</script>
<template>
    <div class="w-full flex flex-wrap">
        <!-- Page count -->
        <div class="w-full flex items-end justify-end mx-2">
            <p class="text-gray-500 text-sm mr-5">{{ pageOptions.from }}-{{ pageOptions.to }} of {{ pageOptions.total }}</p>
            <p class="text-gray-500 text-sm">
                <button @click="prevPage" type="button" class="mx-2" :class="pageOptions.current_page <= 1 ? 'text-gray-400' : 'text-gray-600'" :disabled="pageOptions.current_page == 1"><font-awesome-icon icon="fa-solid fa-angle-left" /></button>
                <button @click="nextPage" type="button" class="mx-2" :class="pageOptions.to == pageOptions.total ? 'text-gray-400' : 'text-gray-600'" :disabled="pageOptions.to == pageOptions.total"><font-awesome-icon icon="fa-solid fa-angle-right" /></button>
            </p>
        </div>
    </div>
</template>