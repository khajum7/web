<template>
    <icon :icon="name" :class="classes" />
  </template>
  
  <script setup lang="ts">
  import { computed, useAttrs } from 'vue'
  import { twMerge } from 'tailwind-merge'
  import { Icon } from '@iconify/vue'
  
  defineProps({
    name: {
      type: String,
      default: 'mdi-light:home'
    }
  })
  
  const attrs = useAttrs()
  
  const classes = computed<string>(() => {
    return twMerge('h-6 w-6 ', attrs.class as string)
  })
  </script>
  