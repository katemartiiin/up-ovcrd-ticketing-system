<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'w-full uppercase inline-flex items-center px-10 py-7 font-medium bg-up-maroon-darker leading-5 text-white focus:outline-none transition duration-150 ease-in-out'
        : 'w-full uppercase inline-flex items-center px-10 py-7 font-medium leading-5 text-white hover:bg-up-maroon-lighter focus:outline-none transition duration-150 ease-in-out'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
