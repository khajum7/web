<template>
  <div class="relative inline-flex">
    <button type="button" class="absolute text-gray-900 top-2 left-2 dark:text-gray-300" @click="decrement">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="opacity-40">
        <path
          d="M11.75 8.75H4.25V7.25H11.75M8 0.5C7.01509 0.5 6.03982 0.693993 5.12987 1.0709C4.21993 1.44781 3.39314 2.00026 2.6967 2.6967C1.29018 4.10322 0.5 6.01088 0.5 8C0.5 9.98912 1.29018 11.8968 2.6967 13.3033C3.39314 13.9997 4.21993 14.5522 5.12987 14.9291C6.03982 15.306 7.01509 15.5 8 15.5C9.98912 15.5 11.8968 14.7098 13.3033 13.3033C14.7098 11.8968 15.5 9.98912 15.5 8C15.5 7.01509 15.306 6.03982 14.9291 5.12987C14.5522 4.21993 13.9997 3.39314 13.3033 2.6967C12.6069 2.00026 11.7801 1.44781 10.8701 1.0709C9.96019 0.693993 8.98491 0.5 8 0.5Z"
          fill="currentColor" />
      </svg>
    </button>
     <input type="number" v-model="count" v-bind="$attrs" class="w-20 h-8 px-6 text-center bg-white border border-gray-300 rounded-full dark:bg-gray-800 dark:border-gray-700" @input="onChange" />
    <button type="button" class="absolute text-gray-900 top-2 right-2 dark:text-gray-300" @click="increment">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="opacity-40">
        <path
          d="M11.75 8.75H8.75V11.75H7.25V8.75H4.25V7.25H7.25V4.25H8.75V7.25H11.75M8 0.5C7.01509 0.5 6.03982 0.693993 5.12987 1.0709C4.21993 1.44781 3.39314 2.00026 2.6967 2.6967C1.29018 4.10322 0.5 6.01088 0.5 8C0.5 9.98912 1.29018 11.8968 2.6967 13.3033C3.39314 13.9997 4.21993 14.5522 5.12987 14.9291C6.03982 15.306 7.01509 15.5 8 15.5C9.98912 15.5 11.8968 14.7098 13.3033 13.3033C14.7098 11.8968 15.5 9.98912 15.5 8C15.5 7.01509 15.306 6.03982 14.9291 5.12987C14.5522 4.21993 13.9997 3.39314 13.3033 2.6967C12.6069 2.00026 11.7801 1.44781 10.8701 1.0709C9.96019 0.693993 8.98491 0.5 8 0.5Z"
          fill="currentColor" />
      </svg>

    </button>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
const emit = defineEmits(['update:modelValue', 'change']);
const props = defineProps({
  modelValue: null,
});
// State
const count = ref(props.modelValue);

// Methods
const decrement = () => {
  if (count.value > 0) {
    count.value--;
    emit("update:modelValue", count.value);
  }
};

const increment = () => {
  count.value++;
  emit("update:modelValue", count.value);
};
const onChange = () => {
  emit("update:modelValue", count.value);
}
watch(
  () => props.modelValue,
  (newVal) => {
    count.value = newVal;
    emit("update:modelValue", newVal);
    emit("change", newVal);
  }
);
</script>