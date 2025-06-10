<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center">
                <h1 class="text-xl text-gray-800">Search Result for <span class="font-bold">{{ search }}</span> </h1>
            </div>
        </template> 
        <div class="bg-white h-full">
            <div class="overflow-x-auto">
                <div class="max-h-full overflow-y-auto scrollbar-hide mt-4">
                    <table class="w-full my-0 align-middle text-dark border-neutral-200 rounded-lg">
                        <thead class="align-bottom sticky top-0 bg-white">
                            <tr class="font-semibold text-[0.95rem] text-secondary-dark bg-slate-50">
                                <th class="p-3 pl-8 text-start min-w-[175px]">ID</th>
                                <th class="p-3 text-start min-w-[175px]">Organization Name</th>
                                <th class="p-3 text-start min-w-[175px]">Order Status</th>
                                <th class="p-3 text-start min-w-[175px]">Approval Status</th>
                                <th class="p-3 text-start min-w-[175px]">Delivery Date</th>
                                <th class="p-3 text-start min-w-[175px]">Program Start Date</th>
                            </tr>
                        </thead>
                        <tbody v-if="sales.length > 0">
                            <tr v-for="(sale, index) in sales" :key="sale.id" :class="{ 'bg-white': index % 2 === 0, 'bg-gray-50': index % 2 !== 0 }">
                                <td class="p-3 pl-8">
                                    <Link :href="route('sales.show', [sale.id])" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                                        {{ sale.id }}
                                    </Link>
                                </td>
                                <td class="p-3">
                                    {{ sale.organization_name }}
                                </td>
                                <td class="p-3">
                                    <div class="relative grid select-none w-fit items-center whitespace-nowrap rounded-md bg-indigo-600 py-1.5 px-3 font-sans text-xs font-bold uppercase text-white">
                                        <span class="">{{ sale.status }}</span>
                                    </div>
                                </td>
                                <td class="p-3">
                                    <div class="relative w-fit grid select-none items-center whitespace-nowrap rounded-md bg-red-500 py-1.5 px-3 font-sans text-xs font-bold uppercase text-white">
                                        <span class="">{{ sale.approval_status }}</span>
                                    </div>
                                </td>
                                <td class="p-3">
                                    <div class="relative w-fit grid select-none items-center whitespace-nowrap rounded-md bg-red-500 py-1.5 px-3 font-sans text-xs font-bold uppercase text-white">
                                        {{ formatDate(sale.delivery_date) }}
                                    </div>
                                </td>
                                <td class="p-3">
                                    {{ formatDate(sale.program_start_date) }}
                                </td>
                            </tr>
                        </tbody>
                        <template v-else>
                            <div class="p-4 text-center text-gray-500 text-lg">
                               Sales not found
                            </div>
                        </template>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
 
</template>
<script setup>
    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
    import { Link, Head } from "@inertiajs/vue3";

    const formatDate = (date) => {
        if (!date) return "";
        const options = { year: "numeric", month: "2-digit", day: "2-digit" };
        return new Date(date).toLocaleDateString("en-US", options);
    };

    const props = defineProps({
        sales : {
            type: Array,
            default: []
        },
        search: {
            type : String,
        }
    })
</script>
