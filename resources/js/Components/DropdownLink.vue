<script setup>
import { Link } from "@inertiajs/vue3";
import AppIcon from "./AppIcon.vue";

defineProps({
    href: {
        type: String,
        required: true,
    },
    iconLeft: {
        type: String,
        default: "",
    },
    iconRight: {
        type: String,
        default: "",
    },
});
</script>

<template>
    <Link
        :href="href"
        class="flex items-center w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
    >
        <template v-if="iconLeft">
            <AppIcon :name="iconLeft" class="mr-3" />
        </template>
        <slot />
        <template v-if="iconRight">
            <AppIcon :name="iconRight" class="ml-2" />
        </template>
    </Link>
</template>
