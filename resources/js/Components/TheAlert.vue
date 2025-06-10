<template>
  <transition name="fade">
    <div v-if="isVisible" class="fixed z-20 flex items-center justify-center -translate-x-1/2 left-1/2 top-3">
      <div class="bg-red-400 p-4 rounded-lg shadow flex flex-row justify-between text-white gap-4 w-full max-w-[360px] lg:max-w-[480px]">
        <span class="pr-10 text-sm">{{ message }}</span>
        <button @click="close" class="absolute w-5 h-5 p-1 cursor-pointer right-1 top-1"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="fill-white"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
        </button>
      </div>
    </div>
  </transition>
  </template>
  
  <script>
  import { ref } from 'vue';
  
  export default {
    props: {
      message: {
        type: String,
        default: ''
      }
    },
    setup(props) {
      const isVisible = ref(false);
  
      const open = () => {
        isVisible.value = true;
        setTimeout(() => {
        close();
      }, 3000);
      };
  
      const close = () => {
        isVisible.value = false;
      };
  
      return {
        isVisible,
        open,
        close
      };
    }
  };
  </script>
  
<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}
</style>
  