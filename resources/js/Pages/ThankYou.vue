<script setup>
import { Head } from "@inertiajs/vue3";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useSessionStorage } from "@vueuse/core"
import { router } from '@inertiajs/vue3';
const orderHistory = useSessionStorage("orderHistory", {})
const shippingAddress = ref({});
import dayjs from "dayjs";
import utc from "dayjs/plugin/utc"; 
import timezone from "dayjs/plugin/timezone"; 
dayjs.extend(utc)
dayjs.extend(timezone);
dayjs.tz.setDefault("America/Denver");
const formatDate = (date) => {
    if (!date) return "";
    // const options = { year: "numeric", month: "short", day: "2-digit",  };
    return dayjs(date).tz("America/Denver").format("MM/DD/YYYY"); //new Date(date).toLocaleDateString("en-US", options);
};

onMounted(() => {
    const data = sessionStorage.getItem('selectedJerseys');
    if (data) {
        shippingAddress.value = JSON.parse(data).shipping_address;
    }
    setTimeout(() => {
        if (Object.keys(orderHistory.value).length === 0) {
            router.get(route('public.index'));
        }
    }, 100)
});


const grandTotalSet = (items) => {
    const total = items.reduce((total, count) => (count.total) + total, 0)
    return total;
}
onUnmounted(() => {
    sessionStorage.removeItem('orderHistory');
});
</script>


<template>
    <Head title="Order History" />
    <PublicLayout>
        <div v-if="Object.keys(orderHistory).length !== 0 && orderHistory" class="flex flex-col w-full py-8">
            <transition name="fade" mode="out-in">
                <div class="flex flex-col gap-2 pb-6">
                    <h3 class="font-bold">Thank you, {{orderHistory.address.first_name}}!</h3>
                    <span class="">Your order <span class="font-bold text-primary-500 dark:text-primary-300">#{{orderHistory.id}}</span> has been received.</span>
                </div>
            </transition>
            <div class="flex flex-col gap-1 pb-2">
                <h2 class="pb-1 text-2xl font-normal uppercase font-heading text-start">Shipping Details</h2>
                <div class="flex flex-row flex-wrap items-center gap-2 text-sm sm:flex-nowrap opacity-60">
                    <div class="flex gap-1">
                        <strong>Org:</strong>
                        <span>{{ orderHistory.organization_name }}</span>
                    </div>
                    <span class="text-xs opacity-50">|</span>
                    <div class="flex gap-1">
                        <strong>Ideal Delivery Date:</strong>
                        <span>{{formatDate(orderHistory.delivery_date)}}</span>
                    </div>
                    <span class="text-xs opacity-50">|</span>
                    <div class="flex gap-1">
                        <strong>Date of First Game:</strong>
                        <span>{{ formatDate(orderHistory.program_start_date) }}</span>
                    </div>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <div>
                    <h3 class="pb-1 font-bold">{{orderHistory.address.first_name}} {{orderHistory.address.last_name}}</h3>
                    <div class="flex flex-row items-center gap-2 text-sm opacity-60">
                        <span class="font-medium">{{ orderHistory.address.phone }}</span>
                        <span class="text-xs opacity-50">|</span>
                        <span>{{ orderHistory.address.email }}</span>
                    </div>
                </div>
                <div>
                    <span>{{ orderHistory.address.address1 }}, {{orderHistory.address.address2 ? orderHistory.address.address2 + ',' : ''}} {{orderHistory.address.city}}({{ orderHistory.address.state }}), {{orderHistory.address.zip}}</span>
                </div>
                <div class="py-3 mt-3 text-sm border-t border-gray-300 border-dashed dark:border-gray-700">If you have any questions or need assistance with your order, please reach out to Katelyn Chambers at <a href="mailto:katelyn.chambers@utahjazz.com" class="underline hover:no-underline text-primary-500 dark:text-primary-300">katelyn.chambers@utahjazz.com</a></div>
            </div>
            <div class="flex flex-col gap-4 pt-12">
                <h2 class="text-3xl font-heading">Your Order History</h2>
                <div class=" bg-gray-50 dark:bg-gray-800">
                    <table class="w-full border-separate border-spacing-0">
                        <thead>
                            <tr class="sticky top-0 uppercase z-10 bg-gray-50 dark:bg-gray-900 ">
                                <th scope="col" class="py-3 pl-4 text-left border-b border-gray-300 dark:border-gray-600">Item</th>
                                <th scope="col" class="py-3 pl-4 text-left border-b border-gray-300 dark:border-gray-600">Set Qty</th>
                                <th scope="col" class="px-4 py-3 text-right border-b border-gray-300 dark:border-gray-600">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="(sales_item, index) in orderHistory.sales_items" :key="index">
                                <tr>
                                    <td class="py-3 pl-4 text-left text-2xl font-heading">{{ sales_item.name }}</td>
                                </tr>
                                <tr v-for="(item, index) in sales_item.items" :key="index" class="even:bg-white dark:even:bg-gray-700"> 
                                    <td class="py-3 pl-4 text-left">
                                        <p>{{item.title}}</p>
                                        <b>SKU: {{item.sku}}</b>
                                    </td>
                                    <td class="py-3 pl-4 text-left">
                                        {{ item.type == '2' ? item.quantity : item.type == '1' ? item.quantity : '-' }}</td>
                                    <td class="px-4 py-3 text-right">{{ item.total }}</td>
                                </tr>
                                <tr class="bg-white dark:bg-gray-900">
                                    <td class="pt-3 font-bold pb-8 border-t border-gray-300 dark:border-gray-700 pl-4 text-left">Total</td>
                                    <td class="pt-3 font-bold pb-8 border-t border-gray-300 dark:border-gray-700 pl-4 text-left">{{ sales_item.type == '2' || sales_item.type == '1' ? sales_item.total: '' }}</td>
                                    <td class="pt-3 font-bold pb-8 border-t border-gray-300 dark:border-gray-700 px-4 text-right">{{grandTotalSet(sales_item.items)}}</td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot v-if="orderHistory.sales_items.length > 0">
                            <tr>
                                <td colspan="3" class="py-3 pl-4 text-left bg-gray-50 dark:bg-gray-800 border-t-2 border-gray-300 dark:border-gray-600">
                                    <h3 class="text-2xl font-heading">Order Information</h3>
                                </td>
                            </tr>
                            <tr v-if="orderHistory.total_jersey !== 0" class="border-t border-gray-300 dark:border-gray-700">
                                <td colspan="2" class="py-3 pl-4 text-left bg-gray-50 dark:bg-gray-800"><b>Grand Total</b> (Jerseys)</td>
                                <td class="py-3 pr-4 text-right">{{orderHistory.total_jersey}}</td>
                            </tr>
                            <tr v-if="orderHistory.total_shorts !== 0" class="border-t border-gray-300 dark:border-gray-700">
                                <td colspan="2" class="py-3 pl-4 text-left bg-gray-50 dark:bg-gray-800"><b>Grand Total</b> (Shorts)</td>
                                <td class="py-3 pr-4 text-right">{{orderHistory.total_shorts}}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<style>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}

</style>