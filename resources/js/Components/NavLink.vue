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
        ? 'inline-flex items-center px-3 py-2 text-indigo-900 bg-indigo-100 rounded-lg border-indigo-400 text-md font-bold leading-5 text-gray-900 focus:outline-none  transition duration-150 ease-in-out'
        : 'inline-flex items-center px-3 py-2  border-transparent text-md font-medium leading-5 text-gray-500 hover:text-indigo-700 hover:bg-indigo-50 hover:rounded-lg hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
