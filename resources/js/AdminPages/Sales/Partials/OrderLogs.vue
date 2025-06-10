<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    orderLogs: Array,
});

const timeAgo = (timestamp) => {
    const date = new Date(timestamp);
    const now = new Date();

    const diff = now.getTime() - date.getTime();

    const diffMinutes = Math.floor(diff / (1000 * 60));
    const diffHours = Math.floor(diff / (1000 * 60 * 60));
    const diffDays = Math.floor(diff / (1000 * 60 * 60 * 24));

    if (diffMinutes < 1) {
        return 'Just now';
    } else if (diffMinutes < 60) {
        return `${diffMinutes} minutes ago`;
    } else if (diffHours < 24) {
        return `${diffHours} hours ago`;
    } else if (diffDays === 0) {
        return 'Today';
    } else if (diffDays === 1) {
        return 'Yesterday';
    } else {
        return `${diffDays} days ago`;
    }
};

</script>

<template>
    <div class="max-h-[620px] overflow-y-auto scrollbar-hide mt-8 px-8">
        <ol class="relative border-l border-gray-200 dark:border-gray-700">
            <li v-for="log in orderLogs" :key="log.id" class="mb-8 ml-6">
                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                    <>
                </span>
                <div class="flex items-center justify-between p-4  bg-white border w-1/3 border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-600">
                    <time class=" text-sm font-normal text-gray-400 sm:order-last sm:mb-0">{{ timeAgo(log.created_at) }}</time>
                    <div class=" text-gray-600 dark:text-gray-300">
                        <div v-html="log.comment"></div>
                    </div>
                </div>
            </li>
        </ol>
    </div>
</template>
