<script setup>
import { usePage } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import AppIcon from "@/Components/AppIcon.vue";

const flash = computed(() => usePage().props.flash);
const messageStack = ref([]);
const clearFlash = () => {
    flash.value.error = null;
    flash.value.success = null;
};

watch(
    () => flash.value.error,
    () => {
        messageStack.value.push(flash.value);
        setTimeout(() => {
            clearFlash();
            messageStack.value.shift();
        }, 2000);
    }
);

watch(
    () => flash.value.success,
    () => {
        messageStack.value.push(flash.value);
        setTimeout(() => {
            clearFlash();
            messageStack.value.shift();
        }, 2000);
    }
);

const removeMessageByKey = (key) => {
    messageStack.value.splice(key, 1);
};
</script>

<template>
    <div
        class="fixed top-8 left-1/2 transform -translate-x-1/2 z-50 flex flex-col items-center gap-2"
        style="width: calc(100% - 16px);" 
    >
        <li
            v-for="(message, index) in messageStack"
            :key="index"
            class="list-none"
        >
            <div
                v-if="message.success !== null || message.error !== null"
                class="min-w-[360px] max-w-[400px] mb-2 rounded-lg min-h-[70px] relative shadow-lg bg-white p-4 gap-4 overflow-hidden border border-white"
            >
                <div class="relative flex gap-4" style="z-index: 200">
                    <div
                        class="flex items-center justify-center w-10 h-10 rounded-full bg-green-400"
                        v-if="message.success"
                    >
                        <AppIcon name="mdi:emoticon" class="text-white" />
                    </div>

                    <div
                        class="flex items-center justify-center w-10 h-10 bg-red-400 rounded-full"
                        v-if="message.error"
                    >
                        <AppIcon name="mdi:emoticon-sad" class="text-white" />
                    </div>

                    <div v-if="message.success" class="flex-1">
                        <h5 class="text-lg font-semibold text-gray-700">
                            Success !
                        </h5>
                        <p class="text-gray-600 bodySm">{{ message.success }}</p>
                    </div>

                    <div v-if="message.error" class="flex-1">
                        <h5 class="text-red-900 text-lg font-semibold">Error !</h5>
                        <p class="text-gray-600 bodySm">{{ message.error }}</p>
                    </div>
                </div>

                <button
                    type="button"
                    class="absolute z-10 p-1 text-gray-500 group top-2 right-2 hover:text-primary"
                    @click="removeMessageByKey(index)"
                >
                    <AppIcon name="fa-xmark" class="w-4 h-4" />
                </button>

                <div
                    class="absolute top-0 left-0 z-0 block w-full h-full blur-3xl"
                    :class="{
                        'bg-gradient-to-r from-emerald-200 to-[#e3f9f2]':
                            message.success,
                        'bg-red-200': message.error,
                    }"
                />
            </div>
        </li>
    </div>
</template>
