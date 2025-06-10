<script setup>
import { ref, watch, onMounted } from 'vue'
import { Head } from "@inertiajs/vue3";
import AppButton from "@/Components/AppButton.vue";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import { router } from '@inertiajs/vue3';
import {useSessionStorage} from "@vueuse/core"
import Alert from '@/Components/TheAlert.vue';
const alertModal = ref(null);
const items = ref([
    { name: '8 Jersey Set', selected: 8 },
    { name: '10 Jersey Set', selected: 10 },
    { name: 'Individual Jerseys', selected: 0 }
])
const selectedJerseys = useSessionStorage("selectedJerseys", {})
selectedJerseys.value.set_8 = {}
selectedJerseys.value.set_10 = {}
selectedJerseys.value.extra_jerseys_shorts = {}
selectedJerseys.value.individual_jerseys = {}
const selectedJersey = ref(null)
const onSubmit = () => {
    
    if(selectedJersey.value == null) {
        alertModal.value.open();
        return;
    };
    selectedJerseys.value.set_8.selected = selectedJersey.value === 8 ? true : false;
    selectedJerseys.value.set_10.selected = selectedJersey.value === 10 ? true : false;
    selectedJerseys.value.individual_jerseys.selected = selectedJersey.value === 0 ? true : false;
    useSessionStorage("state", 1)
    router.get(route('public.jersey-set', { set: selectedJersey.value }));
}

</script>
<template>

    <Head title="Junior Jazz" />
    <PublicLayout>
        <Alert ref="alertModal" message="Please select at least one set." />
        <div class="flex flex-col gap-5 py-8 xl:py-0">
            <div class="flex flex-col items-center gap-4">
                <div class="flex flex-row">
                    <div class="aspect-square">
                        <img src="/images/jazz-front.png" alt="Junior Jazz basketball jersey" class="h-full w-[362px]" loading="lazy">
                    </div>
                    <div class="aspect-square">
                        <img src="/images/jazz-front-black.png" alt="Junior Jazz basketball jersey" class="h-full w-[362px]" loading="lazy">
                    </div>
                </div>
                <h2 class="text-2xl font-normal text-center font-heading">Reversible Jerseys</h2>
            </div>
            <form @submit.prevent="onSubmit">
                <div class="flex flex-col gap-6">
                    <h2 class="text-3xl text-center font-heading md:text-left">Select Number of Jerseys per Set</h2>
                    <div class="grid w-full gap-6 md:grid-cols-3 font-heading">
                        <div v-for="(jersey, index) in items" :key="index">
                            <input :id="'jersey-' + index" v-model="selectedJersey" type="radio" name="set_type"  :value="jersey.selected" 
                                class="hidden peer">
                            <label :for="'jersey-' + index"
                                class="text-[24px] text-center uppercase inline-flex items-center justify-center w-full p-6 bg-white border-2 border-gray-300 rounded-xl cursor-pointer dark:border-gray-700 peer-checked:border-primary-500 dark:peer-checked:border-primary-300 peer-checked:bg-white dark:peer-checked:text-white dark:peer-checked:bg-gray-700 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 peer-checked:drop-shadow-md">
                                <div class="block">
                                    {{ jersey.name }}
                                </div>
                            </label>
                        </div>
                    </div>
                    <AppButton type="submit" class="h-[60px] text-2xl font-heading">
                        Continue</AppButton>
                </div>
            </form>
        </div>
    </PublicLayout>
</template>