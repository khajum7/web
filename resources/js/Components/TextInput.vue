<script setup>
import { onMounted, ref, computed, useAttrs } from 'vue';
import { twMerge } from 'tailwind-merge'
const model = defineModel({
    type: null,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
const attrs = useAttrs();
const classes = computed(() => twMerge('bg-white border-gray-300 w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600', attrs.class));
</script>

<template>
    <input
        :class="classes"
        v-model="model"
        ref="input"
    />
</template>
