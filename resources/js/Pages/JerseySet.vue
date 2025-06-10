<script setup>
import { Head } from "@inertiajs/vue3";
import AppButton from "@/Components/AppButton.vue";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import { router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue'
import AppCounter from "@/Components/AppCounter.vue"
import { useSessionStorage } from "@vueuse/core"
import Alert from '@/Components/TheAlert.vue';
const props = defineProps({
    route: null
})
const alertModal = ref(null);
const message = ref("Please select at least one set.");
const isMultiple = computed(() => (props.route.query.multiple ?? false))
const jerseys_set_8 = ref({
    set_8: {
        totalSet: 0,
        // selected: false,
        title: '8 Jersey Set',
        slug: "8-jersey-set",
        items: [
            {
                name: 'PK',
                qty: 0,
                sku_number: "90613960",
                sizes: [
                    { name: "YS", qty: 7 },
                    { name: "YM", qty: 1 },
                    { name: "YL", qty: 0 },
                    { name: "AS", qty: 0 },
                    { name: "AM", qty: 0 },
                    { name: "AL", qty: 0 },
                    { name: "AXL", qty: 0 },
                ]
            },
            {
                name: 'K',
                qty: 0,
                sku_number: "90613961",
                sizes: [
                    { name: "YS", qty: 5 },
                    { name: "YM", qty: 3 },
                    { name: "YL", qty: 0 },
                    { name: "AS", qty: 0 },
                    { name: "AM", qty: 0 },
                    { name: "AL", qty: 0 },
                    { name: "AXL", qty: 0 },
                ]
            },
            {
                name: '1&2',
                qty: 0,
                sku_number: "90613962",
                sizes: [
                    { name: "YS", qty: 3 },
                    { name: "YM", qty: 3 },
                    { name: "YL", qty: 2 },
                    { name: "AS", qty: 0 },
                    { name: "AM", qty: 0 },
                    { name: "AL", qty: 0 },
                    { name: "AXL", qty: 0 },
                ]
            },
            {
                name: '3&4',
                qty: 0,
                sku_number: "90613963",
                sizes: [
                    { name: "YS", qty: 0 },
                    { name: "YM", qty: 3 },
                    { name: "YL", qty: 3 },
                    { name: "AS", qty: 2 },
                    { name: "AM", qty: 0 },
                    { name: "AL", qty: 0 },
                    { name: "AXL", qty: 0 },
                ]
            },
            {
                name: '5&6',
                qty: 0,
                sku_number: "90613964",
                sizes: [
                    { name: "YS", qty: 0 },
                    { name: "YM", qty: 0 },
                    { name: "YL", qty: 2 },
                    { name: "AS", qty: 5 },
                    { name: "AM", qty: 1 },
                    { name: "AL", qty: 0 },
                    { name: "AXL", qty: 0 },
                ]
            },
            {
                name: '7&8',
                qty: 0,
                sku_number: "90613965",
                sizes: [
                    { name: "YS", qty: 0 },
                    { name: "YM", qty: 0 },
                    { name: "YL", qty: 0 },
                    { name: "AS", qty: 1 },
                    { name: "AM", qty: 4 },
                    { name: "AL", qty: 2 },
                    { name: "AXL", qty: 1 },
                ]
            },
            {
                name: '9&10',
                qty: 0,
                sku_number: "90613966",
                sizes: [
                    { name: "YS", qty: 0 },
                    { name: "YM", qty: 0 },
                    { name: "YL", qty: 0 },
                    { name: "AS", qty: 0 },
                    { name: "AM", qty: 2 },
                    { name: "AL", qty: 4 },
                    { name: "AXL", qty: 2 },
                ]
            },
            {
                name: '11&12',
                qty: 0,
                sku_number: "90613967",
                sizes: [
                    { name: "YS", qty: 0 },
                    { name: "YM", qty: 0 },
                    { name: "YL", qty: 0 },
                    { name: "AS", qty: 0 },
                    { name: "AM", qty: 1 },
                    { name: "AL", qty: 4 },
                    { name: "AXL", qty: 3 },
                ]
            },
        ],
    },
})
const jerseys_set_10 = ref({
    set_10: {
        // selected: false,
        title: '10 Jersey Set',
        slug: "10-jersey-set",
        totalSet: 0,
        items: [
            {
                name: 'PK',
                qty: 0,
                sku_number: "90613968",
                sizes: [
                    { name: "YS", qty: 8 },
                    { name: "YM", qty: 2 },
                    { name: "YL", qty: 0 },
                    { name: "AS", qty: 0 },
                    { name: "AM", qty: 0 },
                    { name: "AL", qty: 0 },
                    { name: "AXL", qty: 0 },
                ]
            },
            {
                name: 'K',
                qty: 0,
                sku_number: "90613969",
                sizes: [
                    { name: "YS", qty: 6 },
                    { name: "YM", qty: 4 },
                    { name: "YL", qty: 0 },
                    { name: "AS", qty: 0 },
                    { name: "AM", qty: 0 },
                    { name: "AL", qty: 0 },
                    { name: "AXL", qty: 0 },
                ]
            },
            {
                name: '1&2',
                qty: 0,
                sku_number: "90613970",
                sizes: [
                    { name: "YS", qty: 4 },
                    { name: "YM", qty: 4 },
                    { name: "YL", qty: 2 },
                    { name: "AS", qty: 0 },
                    { name: "AM", qty: 0 },
                    { name: "AL", qty: 0 },
                    { name: "AXL", qty: 0 },
                ]
            },
            {
                name: '3&4',
                qty: 0,
                sku_number: "90613971",
                sizes: [
                    { name: "YS", qty: 0 },
                    { name: "YM", qty: 4 },
                    { name: "YL", qty: 4 },
                    { name: "AS", qty: 2 },
                    { name: "AM", qty: 0 },
                    { name: "AL", qty: 0 },
                    { name: "AXL", qty: 0 },
                ]
            },
            {
                name: '5&6',
                qty: 0,
                sku_number: "90613972",
                sizes: [
                    { name: "YS", qty: 0 },
                    { name: "YM", qty: 0 },
                    { name: "YL", qty: 3 },
                    { name: "AS", qty: 6 },
                    { name: "AM", qty: 1 },
                    { name: "AL", qty: 0 },
                    { name: "AXL", qty: 0 },
                ]
            },
            {
                name: '7&8',
                qty: 0,
                sku_number: "90613973",
                sizes: [
                    { name: "YS", qty: 0 },
                    { name: "YM", qty: 0 },
                    { name: "YL", qty: 0 },
                    { name: "AS", qty: 1 },
                    { name: "AM", qty: 5 },
                    { name: "AL", qty: 3 },
                    { name: "AXL", qty: 1 },
                ]
            },
            {
                name: '9&10',
                qty: 0,
                sku_number: "90613974",
                sizes: [
                    { name: "YS", qty: 0 },
                    { name: "YM", qty: 0 },
                    { name: "YL", qty: 0 },
                    { name: "AS", qty: 0 },
                    { name: "AM", qty: 3 },
                    { name: "AL", qty: 5 },
                    { name: "AXL", qty: 2 },
                ]
            },
            {
                name: '11&12',
                qty: 0,
                sku_number: "90613975",
                sizes: [
                    { name: "YS", qty: 0 },
                    { name: "YM", qty: 0 },
                    { name: "YL", qty: 0 },
                    { name: "AS", qty: 0 },
                    { name: "AM", qty: 2 },
                    { name: "AL", qty: 5 },
                    { name: "AXL", qty: 3 },
                ]
            },
        ],
    },
})
const extra_jerseys_shorts = ref([
    {
        title: "Shorts",
        description: "Shorts Order Form (Minimum order quantity is 5)",
        items: [
            { name: "YS", qty: 0, sku_number: "90614063" },
            { name: "YM", qty: 0, sku_number: "90614064" },
            { name: "YL", qty: 0, sku_number: "90614065" },
            { name: "SM", qty: 0, sku_number: "90614066" },
            { name: "MED", qty: 0, sku_number: "90614067" },
            { name: "LG", qty: 0, sku_number: "90614068" },
            { name: "XL", qty: 0, sku_number: "90614069" },
            { name: "2XL", qty: 0, sku_number: "90614070" },
            { name: "3XL", qty: 0, sku_number: "90614071" },
        ],
    },
    {
        title: "Extra Jerseys",
        subtitle: "Extra jersey numbers will be different than the jersey numbers in the standard sets.",
        description: "Jerseys Order Form (Minimum order quantity is 5)",
        items: [
            { name: "YS", qty: 0, sku_number: "90613988" },
            { name: "YM", qty: 0, sku_number: "90613989" },
            { name: "YL", qty: 0, sku_number: "90613990" },
            { name: "SM", qty: 0, sku_number: "90613991" },
            { name: "MED", qty: 0, sku_number: "90613992" },
            { name: "LG", qty: 0, sku_number: "90613993" },
            { name: "XL", qty: 0, sku_number: "90613994" },
            { name: "2XL", qty: 0, sku_number: "90613995" },
            { name: "3XL", qty: 0, sku_number: "90613996" },
        ],
    }
])
const headers = ref([
    'Age',
    'Set Qty',
    'YS',
    'YM',
    'YL',
    'AS',
    'AM',
    'AL',
    'AXL',
]);

// const individual_jerseys = ref({
//     title: 'Individual Jerseys',
//     shorts: [
//         { name: "YS", qty: 0, sku_number: "90614063" },
//         { name: "YM", qty: 0, sku_number: "90614064" },
//         { name: "YL", qty: 0, sku_number: "90614065" },
//         { name: "SM", qty: 0, sku_number: "90614066" },
//         { name: "MED", qty: 0, sku_number: "90614067" },
//         { name: "LG", qty: 0, sku_number: "90614068" },
//         { name: "XL", qty: 0, sku_number: "90614069" },
//         { name: "2XL", qty: 0, sku_number: "90614070" },
//         { name: "3XL", qty: 0, sku_number: "90614071" },
//     ],
//     extra_jerseys: [
//         { name: "YS", qty: 0 },
//         { name: "YM", qty: 0 },
//         { name: "YL", qty: 0 },
//         { name: "SM", qty: 0 },
//         { name: "MED", qty: 0 },
//         { name: "LG", qty: 0 },
//         { name: "XL", qty: 0 },
//         { name: "2XL", qty: 0 },
//         { name: "3XL", qty: 0 },
//     ]
// })
const sendBack = () => {
    router.get(route('public.index'));
}
const extra_jersey_limit = computed(() => {
    return selectedJerseys.value.extra_jerseys_shorts[1].items.reduce((total, item) => total + item.qty, 0)
})
const extra_jersey_max_limit = computed(() => {
    return selectedJerseys.value.extra_jerseys_shorts[1].items.some((item) => item.qty > 12)
})
const individual_jersey_limit = computed(() => {
  return Object.values(selectedJerseys.value.extra_jerseys_shorts).reduce((total, obj) => {

    const itemTotal = obj.items.reduce((itemSum, item) => {
      return itemSum + item.qty;
    }, 0);

    return total + itemTotal;
  }, 0);
});
const onSubmit = async () => {
    sessionStorage.setItem('state', 2);

    let isValid_1 = false;
    let isValid_2 = false;
    let isValidIndividual = false;
    if (selectedJerseys.value.set_8?.items || selectedJerseys.value.set_10?.items || selectedJerseys.value.extra_jerseys_shorts) {
        if (selectedJerseys.value.set_8?.items) {
            isValid_1 = selectedJerseys.value.set_8.items.some((item) => item.qty > 0);
        }
        if (selectedJerseys.value.set_10?.items) {
            isValid_2 = selectedJerseys.value.set_10.items.some((item) => item.qty > 0);
        }
        if (selectedJerseys.value.extra_jerseys_shorts && selectedJerseys.value.individual_jerseys.selected) {
            isValidIndividual = Object.values(selectedJerseys.value.extra_jerseys_shorts).some((obj) => {
                return obj.items.some((item) => item.qty > 0)
            })
        }
        if (extra_jersey_max_limit.value) {
            message.value = `You can not order more than 12 single extra jerseys PER SIZE.`;
            alertModal.value.open();
        } else if (extra_jersey_limit.value > 50) {
            message.value = '😥 You can not order more than 50 extra jerseys.';
            alertModal.value.open();
        } else if (isChecked.value) {
            if (!isMultiple.value && (!isValid_1 && !isValid_2)) {
                alertModal.value.open();
            } else {
                await router.get(route('public.jersey-set', { set: isSetType.value == 8 ? '10' : '8', multiple: true }));
            }
        } else {
            if (isValidIndividual || ((!isMultiple.value && (isValid_1 || isValid_2)) || (isMultiple.value && (isValid_1 && isValid_2)))) {
                if (selectedJerseys.value.individual_jerseys.selected && individual_jersey_limit.value < 5){
                    message.value = '😥 Minimum order qty is 5';
                    alertModal.value.open();
                } else {
                    router.get(route('public.shipping-address'));
                }
            } else {
                message.value = selectedJerseys.value.individual_jerseys.selected? 'Please select qty.': 'Please select at least one set.';
                alertModal.value.open();
            }
        }
    }
}

const getTotalSet = (items, qty) => {
    const total = items.reduce((total, count) => count.qty + total, 0)

    if(isSetType.value == 8){
        jerseys_set_8.value.set_8.totalSet = total
    }

    if(isSetType.value == 10){
        jerseys_set_10.value.set_10.totalSet = total
    }

    return total;
}

const getTotalByAge = (items, qty) => {
    return items.reduce((total, count) => (count.qty * qty) + total, 0);
}
const getTotalBySize = (items) => {
    const sizeTotals = {};

    items.forEach((item) => {
        if (item.qty > 0) {
            item.sizes.forEach((size) => {
                if (sizeTotals[size.name]) {
                    sizeTotals[size.name] += size.qty * item.qty;
                } else {
                    sizeTotals[size.name] = size.qty * item.qty;
                }
            });
        } else {
            item.sizes.forEach((size) => {
                sizeTotals[size.name] = (sizeTotals[size.name] ?? 0);
            });
        }
    });

    return sizeTotals;
};

const selectedJerseys = useSessionStorage("selectedJerseys", {})
const isChecked = ref(false)

const isSetType = computed(() => props.route.query.set)
const getGrandTotal = (items) => {
    const sizeTotals = getTotalBySize(items);
    let grandTotal = 0;

    for (const size in sizeTotals) {
        grandTotal += sizeTotals[size];
    }

    return grandTotal;
};
onMounted(() => {
  if(sessionStorage.getItem('state') == undefined || sessionStorage.getItem('state') == null || sessionStorage.getItem('state') == 0) {
    router.get(route('public.index'));
  }
    selectedJerseys.value.extra_jerseys_shorts = {...extra_jerseys_shorts.value, ...selectedJerseys.value.extra_jerseys_shorts } //= {...selectedJerseys.value.extra_jerseys_shorts, ...extra_jerseys_shorts.value}
    if (isSetType.value == 8) {
        selectedJerseys.value.set_8 = {...selectedJerseys.value.set_8, ...jerseys_set_8.value.set_8, ...jerseys_set_8.value.set_8.totalSet}
    }
    if (isSetType.value == 10) {
        selectedJerseys.value.set_10 = {...selectedJerseys.value.set_10, ...jerseys_set_10.value.set_10, ...jerseys_set_10.value.set_10.totalSet}
    }
    if (isSetType.value == 0) {
        selectedJerseys.value.extra_jerseys_shorts = {...selectedJerseys.value.extra_jerseys_shorts, ...extra_jerseys_shorts.value}
    }
})
const selectedExtraJerseys = computed(() => selectedJerseys.value.extra_jerseys_shorts)
</script>
<template>

    <Head title="Jersey Set" />
    <PublicLayout>
        <Alert ref="alertModal" :message="message" />
        <div class="flex flex-col gap-12" :class="[isSetType !=0 ? 'py-8 xl:p-8' : 'pb-12 xl:px-8' ]">
            <div class="flex flex-col">
                <h2 v-if="isSetType != 0" class="pb-2 text-2xl font-normal uppercase font-heading text-start">{{ isSetType }} Jerseys Set Form
                </h2>
                <div class="overflow-x-auto">
                    <table v-if="isSetType == 8" class="table w-full border-collapse">
                        <thead>
                            <tr class="text-sm uppercase border-b border-gray-300 dark:border-gray-700">
                                <th v-for="(header, index) in headers" :key="index" :scope="'col'"
                                    class="min-w-16"
                                    :class="['p-2 text-left', { 'bg-gray-50 dark:bg-gray-800': header === 'Set Qty' || header === 'Age', 'border-r border-gray-300 dark:border-gray-600': header === 'Set Qty' }]">
                                    {{ header }}</th>
                                <th class="text-right" scope="'col'">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(product, index) in jerseys_set_8.set_8.items" :key="index" class="even:bg-gray-100 dark:even:bg-gray-700 odd:bg-white dark:odd:bg-gray-800"
                                :class="{ 'even:bg-gray-100 dark:bg-gray-800': index % 2 === 0 }">
                                <td class="w-[60px] p-2" :class="[index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-700' : 'bg-gray-100 dark:bg-gray-800']">{{
                        product.name }}</td>
                                <td class="border-r border-gray-300 dark:border-gray-600 w-[112px] p-2"
                                    :class="['border-r border-gray-300 dark:border-gray-600 w-[112px] p-2', index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-700' : 'bg-gray-100 dark:bg-gray-800']">
                                    <AppCounter v-model="product.qty" />
                                </td>
                                <td v-for="size in product.sizes" :key="size" class="p-2">
                                    <template v-if="size.qty !== 0">
                                        <span v-if="product.qty == 0 && size.qty" class="opacity-40">{{ size.qty
                                            }}</span>
                                        <span v-else>{{ size.qty * product.qty }}</span>
                                    </template>
                                </td>
                                <td class="p-2 text-right">{{ getTotalByAge(product.sizes, product.qty) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="font-bold border-t border-gray-300 dark:border-gray-600">
                                <td class="p-2 bg-gray-50 dark:bg-gray-900">Total</td>
                                <td class="p-2 text-center border-r border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-900">{{
                        getTotalSet(jerseys_set_8.set_8.items) }} 
                                <input type="hidden" v-model="jerseys_set_8.set_8.totalSet"/>
                                </td>
                                <td v-for="size in jerseys_set_8.set_8.items[0].sizes" :key="size" class="p-2">
                                    {{ getTotalBySize(jerseys_set_8.set_8.items)[size.name] }}
                                </td>
                                <td class="p-2 text-right">
                                    {{ getGrandTotal(jerseys_set_8.set_8.items) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="overflow-x-auto">
                    <table v-if="isSetType == 10" class="table w-full border-collapse">
                        <thead>
                            <tr class="text-sm uppercase border-b border-gray-300 dark:border-gray-700">
                                <th v-for="(header, index) in headers" :key="index" :scope="'col'"
                                    class="min-w-16"
                                    :class="['p-2 text-left', { 'bg-gray-50 dark:bg-gray-800': header === 'Set Qty' || header === 'Age', 'border-r border-gray-300 dark:border-gray-600': header === 'Set Qty' }]">
                                    {{ header }}</th>
                                <th class="text-right" scope="'col'">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(product, index) in jerseys_set_10.set_10.items" :key="index" class="even:bg-gray-100 dark:even:bg-gray-700 odd:bg-white dark:odd:bg-gray-800"
                                :class="{ 'even:bg-gray-100 dark:bg-gray-800': index % 2 === 0 }">
                                <td class="w-[60px] p-2" :class="[index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-700' : 'bg-gray-100 dark:bg-gray-800']">{{
                        product.name }}</td>
                                <td class="border-r border-gray-300 dark:border-gray-600 w-[112px] p-2"
                                    :class="['border-r border-gray-300 dark:border-gray-600 w-[112px] p-2', index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-700' : 'bg-gray-100 dark:bg-gray-800']">
                                    <AppCounter v-model="product.qty" />
                                </td>
                                <td v-for="size in product.sizes" :key="size" class="p-2">
                                    <template v-if="size.qty !== 0">
                                        <span v-if="product.qty == 0 && size.qty" class="opacity-40">{{ size.qty
                                            }}</span>
                                        <span v-else>{{ size.qty * product.qty }}</span>
                                    </template>
                                </td>
                                <td class="p-2 text-right">{{ getTotalByAge(product.sizes, product.qty) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="font-bold border-t border-gray-300 dark:border-gray-600">
                                <td class="p-2 bg-gray-50 dark:bg-gray-900">Total</td>
                                <td class="p-2 text-center border-r border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-900">{{
                        getTotalSet(jerseys_set_10.set_10.items) }} 
                                <input type="hidden" v-model="jerseys_set_10.set_10.totalSet"/>
                                </td>
                                <td v-for="size in jerseys_set_10.set_10.items[0].sizes" :key="size" class="p-2">
                                    {{ getTotalBySize(jerseys_set_10.set_10.items)[size.name] }}
                                </td>
                                <td class="p-2 text-right">
                                    {{ getGrandTotal(jerseys_set_10.set_10.items) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="flex flex-col items-center gap-4">
                <div class="flex flex-row">
                    <div class="aspect-square">
                        <img src="/images/jazz-short.png" alt="Jazz basketball short" class="h-full w-[320px]" loading="lazy">
                    </div>
                    <div class="aspect-square">
                        <img src="/images/jazz-short-black.png" alt="Jazz basketball short in black color" class="h-full w-[320px]" loading="lazy">
                    </div>
                </div>
                <h2 class="text-2xl font-normal leading-6 text-center font-heading">Reversible Shorts</h2>
            </div>
            <div class="grid gap-8">
                <div v-if="selectedJerseys.individual_jerseys !== undefined && selectedJerseys.individual_jerseys.selected && Object.keys(selectedJerseys.individual_jerseys).length !== 0" class="p-5 text-lg bg-white border-l-4 border-primary-500 dark:bg-gray-800 text-orange">If making an add-on order, 5 items must be ordered as a minimum. This can be a combination of shorts and jerseys.</div>
                <div v-for="extra_jersey_short in selectedExtraJerseys" :key="extra_jersey_short.title" class="flex flex-col gap-4">
                    <h2 class="text-3xl font-normal font-heading">{{ extra_jersey_short.title }}</h2>
                    <p v-if="isSetType != 0 && extra_jersey_short.subtitle" class="block text-sm leading-4">{{ extra_jersey_short.subtitle }}</p>
                    <div class="flex flex-col gap-2 bg-white dark:bg-gray-900">
                        <!-- <p v-if="selectedJerseys.individual_jerseys !== undefined && selectedJerseys.individual_jerseys.selected && Object.keys(selectedJerseys.individual_jerseys).length !== 0" class="pt-2 text-sm leading-4 text-orange">{{ extra_jersey_short.description}}</p> -->
                        <div class="flex flex-row flex-wrap">
                            <div v-for="item in extra_jersey_short.items" :key="item.name"
                                class="flex flex-col items-center">
                                <span class="px-2 py-3 text-sm font-bold">{{ item.name }}</span>
                                <div class="p-2 border-t border-gray-300 dark:border-gray-700">
                                    <AppCounter v-model="item.qty" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="isSetType != 0 && !isMultiple" class="flex flex-col gap-4">
                <div 
                    :class="['flex flex-col gap-2 p-4 border-2 rounded-lg w-full sm:max-w-[348px] sm:min-w-[348px] cursor-pointer', { 'border-gray-300 dark:border-gray-700': !isChecked, 'border-primary-500 dark:border-primary-300': isChecked }]">
                    <label for="setJersey" class="flex flex-col gap-2 cursor-pointer">
                        <div class="flex flex-row justify-between">
                            <div class="flex gap-5 my-2">
                                <span class="font-heading text-[18px] leading-5">Set of {{isSetType == 8 ? '10' : '8'}}
                                    Jerseys</span>
                            </div>
                            <input type="checkbox" id="setJersey" v-model="isChecked"
                                class="w-5 h-5 border-gray-300 rounded cursor-pointer checked:bg-primary-500 accent-primary-500 checked:hover:bg-primary-500 focus-visible:outline-0 focus:ring-transparent focus:ring-offset-0 checked:focus:outline-0 checked:focus:bg-primary-500 checked:ring-offset-0" />
                        </div>
                        <span class="text-sm">Add this set of {{isSetType == 8 ? '10' : '8'}} jerseys to
                            the order
                            too.</span>
                    </label>
                </div>
            </div>
            <div class="py-3 mt-3 text-sm border-t border-gray-300 border-dashed dark:border-gray-700">If you have any questions or need assistance with your order, please reach out to Katelyn Chambers at <a href="mailto:katelyn.chambers@utahjazz.com" class="underline text-primary-500 dark:text-primary-300 hover:no-underline">katelyn.chambers@utahjazz.com</a></div>
            <div class="grid justify-between grid-cols-2 gap-2 font-heading">
                <AppButton variant="secondary" class="h-[60px] text-2xl font-heading" @click="sendBack">
                    Back
                </AppButton>
                <AppButton type="submit" class="h-[60px] text-2xl font-heading" @click="onSubmit()">
                    Continue
                </AppButton>
            </div>
        </div>
    </PublicLayout>
</template>