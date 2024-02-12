<script>
/* Vue */
import { Link } from '@inertiajs/vue3'
export default {
    components: {
        Link
    },
    props: [
        'parentTitle',
        'parentLink',
        'currentTitle'
    ],
    setup() {
        return {}
    }
}
</script>
<template>
    <div class="text-up-maroon leading-tight">
        <Link :href="parentLink" class="uppercase">{{ parentTitle }}</Link>
        > <span class="font-bold">{{ currentTitle }}</span>
    </div>
</template>