<script setup>
import { Head } from "@inertiajs/vue3";
import AppButton from "@/Components/AppButton.vue";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import { router } from '@inertiajs/vue3';
import { ref, onMounted, computed, nextTick } from 'vue'
import { useSessionStorage } from "@vueuse/core"
import Alert from '@/Components/TheAlert.vue';
// import AppLoading from "@/Components/AppLoading.vue"
import axios from "axios";
import dayjs from "dayjs";
import utc from "dayjs/plugin/utc"; 
import timezone from "dayjs/plugin/timezone"; 
dayjs.extend(utc)
dayjs.extend(timezone);
dayjs.tz.setDefault("America/Denver");
const selectedJerseys = useSessionStorage("selectedJerseys", {})
const selectedItems = useSessionStorage("selectedItems", {})
const orderHistory = useSessionStorage("orderHistory", {})
const orderConfirmed = ref(false);
const shippingAddress = ref({});
const isLoading = ref(false)
const message = ref("")
const alertModal = ref(null);

const formatDate = (date) => {
    if (!date) return "";
    // const options = { year: "numeric", month: "short", day: "2-digit",  };
    return dayjs(date).tz("America/Denver").format("MM/DD/YYYY"); //new Date(date).toLocaleDateString("en-US", options);
};

onMounted(() => {
    orderHistory.value = {}
    // const data = sessionStorage.getItem('selectedJerseys');
    if (selectedJerseys.value) {
        shippingAddress.value = selectedJerseys.value.shipping_address;
    }
});



const sendBack = () => {
    window.history.back();
}

const editOrder = () =>{
    router.get(route('public.shipping-address'));
}
onMounted(async() => {
    if(sessionStorage.getItem('state') == undefined || sessionStorage.getItem('state') == null || sessionStorage.getItem('state') == 0) {
    router.get(route('public.index'));
  }else if(sessionStorage.getItem('state') == 1){
    router.get(route('public.jersey-set', { set: selectedJerseys.value.set_8.selected ? 8 : 10 }));
  } else if(sessionStorage.getItem('state') == 2){ 
    router.get(route('public.shipping-address'));
  }
    await nextTick()
    setTimeout(() => {
        if (Object.keys(shippingAddress.value).length === 0) {
            router.get(route('public.index'));
        }
    }, 100)
})
const grandTotalSet = (items) => {
    console.log(items)
    return items.reduce((total, count) => count.total + total, 0);
}
const payloadItems = computed(() => {
    const sections = [
        selectedJerseys.value?.set_8?.items,
        selectedJerseys.value?.set_10?.items,
        selectedJerseys.value?.extra_jerseys_shorts[0]?.items,
        selectedJerseys.value?.extra_jerseys_shorts[1]?.items
    ];

    return sections.flatMap(section => 
        section?.filter(item => item.qty > 0).map(item => ({
            sku: item.sku_number,
            quantity: item.qty
        })) || []
    );
});
const onSubmit = async () =>{
    isLoading.value = true
    const payload = {
        items: payloadItems.value,
        shipping_address: selectedJerseys.value.shipping_address,
        set: selectedJerseys.value.individual_jerseys.selected ? 0 : 1
    }
    try {
        await axios.post("api/orderPlace", payload).then((res) => {
            if(res.status == 200) {
                orderHistory.value = res.data.data;
                router.get(route('public.thank-you'));
                selectedJerseys.value.extra_jerseys_shorts = {};
                selectedJerseys.value.individual_jerseys = {};
                selectedJerseys.value.set_8 = {};
                selectedJerseys.value.set_10 = {};
                selectedJerseys.value.shipping_address = {};
                isLoading.value = false
                orderConfirmed.value = true;
            }
        })
    } catch (err) {
        message.value = err.response.data.message
        console.log("Error", err.response.data.message);
        alertModal.value.open();
        isLoading.value = false
    }
}
const filteredItems = computed(() => {
    const groupedItems = Object.groupBy(selectedItems.value, (obj) => obj.type)

    return Object.entries(groupedItems).map(([key, value]) => {
        const total = value.reduce((total, count) => count.qty + total, 0);
        const grand_total = value.reduce((total, count) => count.total + total, 0);
        return {
            group_name: value[0].group_name,
            type: key,
            items: value,
            total: total,
            grand_total: grand_total,
        }
    });
});
const grandTotalJersey = (items) => {
    return items.reduce((total, count) => count.type !== "4" ? count.grand_total + total: total, 0);
}
const grandTotalShort = (items) => {
    return items.reduce((total, count) => count.type === "4" ? count.grand_total + total : total, 0);
}
const isShorts = computed(() => {
    return filteredItems.value.some((item) => item.type === "4");
})
const isJerseys = computed(() => {
    return filteredItems.value.some((item) => item.type === "1" || item.type === "2" || item.type === "3");
})
</script>


<template>
    <Head title="Order Preview" />
    <PublicLayout>
        <Alert ref="alertModal" :message="message" />
        <div class="flex flex-col w-full py-8">
            <div class="flex flex-col gap-1 pb-2">
                <h2 class="pb-1 text-2xl font-normal uppercase font-heading text-start">Shipping Details</h2>
                <div class="flex flex-row flex-wrap items-center gap-2 text-sm sm:flex-nowrap opacity-60">
                    <div class="flex gap-1">
                        <strong>Org:</strong>
                        <span>{{ shippingAddress.organization_name }}</span>
                    </div>
                    <span class="text-xs opacity-50">|</span>
                    <div class="flex gap-1">
                        <strong>Ideal Delivery Date:</strong>
                        <span>{{formatDate(shippingAddress.delivery_date)}}</span>
                    </div>
                    <span class="text-xs opacity-50">|</span>
                    <div class="flex gap-1">
                        <strong>Date of First Game:</strong>
                        <span>{{ formatDate(shippingAddress.date_first_game) }}</span>
                    </div>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <div>
                    <h3 class="pb-1 font-bold">{{shippingAddress.first_name}} {{shippingAddress.last_name}}</h3>
                        <div class="flex flex-row items-center gap-2 text-sm opacity-60">
                            <span class="font-medium">{{ shippingAddress.phone }}</span>
                            <span class="text-xs opacity-50">|</span>
                            <span>{{ shippingAddress.email }}</span>
                        </div>
                </div>
                <div>
                    <span>{{ shippingAddress.address1 }}, {{shippingAddress.address2 ? shippingAddress.address2 + ',' : ''}} {{shippingAddress.city}}({{ shippingAddress.state }}), {{shippingAddress.zip}}</span>
                </div>
                <a v-if="!orderConfirmed" @click="editOrder" class="underline cursor-pointer text-primary-500 dark:text-primary-300 w-fit">Edit</a>
                <div class="py-3 mt-3 text-sm border-t border-gray-300 border-dashed dark:border-gray-700">If you have any questions or need assistance with your order, please reach out to Katelyn Chambers at <a href="mailto:katelyn.chambers@utahjazz.com" class="underline text-primary-500 dark:text-primary-300 hover:no-underline">katelyn.chambers@utahjazz.com</a></div>
            </div>
            <div class="flex flex-col gap-4 pt-8">
                <h2 class="text-3xl font-heading">Your Order Preview</h2>
                <div class="bg-gray-50 dark:bg-gray-800">
                    <table class="w-full border-separate border-spacing-0">
                        <thead>
                            <tr class="sticky top-0 uppercase z-10 bg-gray-50 dark:bg-gray-900 ">
                                <th scope="col" class="py-3 pl-4 text-left border-b border-gray-300 dark:border-gray-800">Item</th>
                                <th scope="col" class="py-3 pl-4 text-left border-b border-gray-300 dark:border-gray-800">Set Qty</th>
                                <th scope="col" class="px-4 py-3 text-right border-b border-gray-300 dark:border-gray-800">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="(sales_item, index) in filteredItems" :key="index">
                                <tr>
                                    <td class="py-3 pl-4 text-left text-2xl font-heading">{{ sales_item.group_name }}</td>
                                </tr>
                                <tr v-for="(item, index) in sales_item.items" :key="index" class="even:bg-white dark:even:bg-gray-700"> 
                                    <td class="py-3 pl-4 text-left">
                                        <p>{{item.name}}</p>
                                        <b>SKU: {{item.sku}}</b>
                                    </td>
                                    <td class="py-3 pl-4 text-left">
                                        {{ item.type == '2' ? item.qty : item.type == '1' ? item.qty : '-' }}</td>
                                    <td class="px-4 py-3 text-right">{{ item.total }}</td>
                                </tr>
                                <tr class="bg-white dark:bg-gray-900">
                                    <td class="pt-3 font-bold pb-8 border-t border-gray-300 dark:border-gray-800 pl-4 text-left">Total</td>
                                    <td class="pt-3 font-bold pb-8 border-t border-gray-300 dark:border-gray-800 pl-4 text-left">{{ sales_item.type == '2' || sales_item.type == '1' ? sales_item.total: '' }}</td>
                                    <td class="pt-3 font-bold pb-8 border-t border-gray-300 dark:border-gray-800 px-4 text-right">{{ sales_item.grand_total }}</td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot v-if="filteredItems.length > 0">
                            <tr>
                                <td colspan="3" class="py-3 pl-4 text-left bg-gray-50 dark:bg-gray-800 border-t-2 border-gray-300 dark:border-gray-600">
                                    <h3 class="text-2xl font-heading">Order Information</h3>
                                </td>
                            </tr>
                            <tr v-if="isShorts" class="border-t border-gray-300 dark:border-gray-700">
                                <td colspan="2" class="py-3 pl-4 text-left bg-gray-50 dark:bg-gray-800"><b>Grand Total</b> (Jerseys)</td>
                                <td class="py-3 pr-4 text-right">{{grandTotalJersey(filteredItems)}}</td>
                            </tr>
                            <tr v-if="isJerseys" class="border-t border-gray-300 dark:border-gray-700">
                                <td colspan="2" class="py-3 pl-4 text-left bg-gray-50 dark:bg-gray-800"><b>Grand Total</b> (Shorts)</td>
                                <td class="py-3 pr-4 text-right">{{grandTotalShort(filteredItems)}}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <transition name="fade" mode="out-in">
                <div class="grid justify-between grid-cols-1 gap-2 pt-4 sm:grid-cols-2 font-heading">
                    <AppButton variant="secondary" class="px-2 text-2xl py-7 font-heading" @click="sendBack">
                        Back
                    </AppButton>
                    <AppButton type="submit" :loading="isLoading" class="px-2 text-2xl py-7 font-heading" @click="onSubmit">
                        Place Your Order
                    </AppButton>
                </div>
            </transition>
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