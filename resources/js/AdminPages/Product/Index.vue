<script setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import AppButton from "@/Components/AppButton.vue";
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import AppDatepicker from "@/Components/AppDatePicker.vue";

const props = defineProps({
    items: Array,
});


const formatDate = (date) => {
    if (!date) return "";
    const options = { year: "numeric", month: "2-digit", day: "2-digit" };
    return new Date(date).toLocaleDateString("en-US", options);
};
</script>
<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Products List
                </h2>
            </div>
        </template>
        <div class="bg-white h-full">
            <div class="overflow-x-auto">
                <div class="max-h-full overflow-y-auto scrollbar-hide mt-1">
                    <table
                        class="w-full my-0 align-middle text-dark border-neutral-200 rounded-lg"
                    >
                        <thead class="align-bottom sticky top-0 bg-white">
                        <tr
                            class="font-semibold text-[0.95rem] text-secondary-dark bg-slate-50"
                        >
                            <th class="p-3 pl-8 text-start min-w-[175px]">
                                SKU
                            </th>
                            <th class="p-3 pl-8  text-start min-w-[175px]">
                                Item Name
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr
                            v-for="(item, index) in items"
                            :key="item.id"
                            :class="{
                                    'bg-white': index % 2 === 0,
                                    'bg-gray-50': index % 2 !== 0,
                                }"
                        >
                            <td class="p-3 pl-8">
                                {{ item.sku }}
                            </td>

                            <td class="p-3 pl-8">
                                {{ item.name }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
