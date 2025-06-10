<script setup>
import { ref } from "vue";
import { Head, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import AppButton from "@/Components/AppButton.vue";
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import AppIcon from "@/Components/AppIcon.vue";
import AppChip from "@/Components/AppChip.vue";
import OrderNote from "@/AdminPages/Sales/Partials/OrderNote.vue";
import OrderLogs from "@/AdminPages/Sales/Partials/OrderLogs.vue";
import axios from "axios";

const props = defineProps({
    sale: Object,
    saleItem: Object,
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const currentItem = ref({});
const isOpenStatusModal = ref(false);
const orderLogs = ref([]);
const statusForm = useForm({
    note: "",
    status: "",
});

const approvalStatus = ref(["APPROVED", "REJECTED"]);

const createForm = useForm({
    title: "",
    quantity: "",
    sale_id: props.sale.id,
});

const editForm = useForm({
    title: props.saleItem?.title || "",
    quantity: props.saleItem?.quantity || "",
    sale_id: props.saleItem?.sale_id || "",
});

const formatDate = (date) => {
    if (!date) return "";
    const options = { year: "numeric", month: "2-digit", day: "2-digit" };
    return new Date(date).toLocaleDateString("en-US", options);
};

const toggleCreateModal = () => {
    showCreateModal.value = !showCreateModal.value;
};

const toggleEditModal = (item = null) => {
    if (item) {
        currentItem.value = item;
        editForm.title = item.title;
        editForm.quantity = item.quantity;
        editForm.sale_id = item.sale_id;
    }
    showEditModal.value = !showEditModal.value;
};

const toggleStatusModal = () => {
    isOpenStatusModal.value = !isOpenStatusModal.value;
};

const submitCreate = () => {
    createForm.post(route("sales.items.store"), {
        onSuccess: () => {
            createForm.reset();
            toggleCreateModal();
        },
    });
};

const submitEdit = () => {
    editForm.put(route("sales.items.update", [currentItem.value.id]), {
        onSuccess: () => {
            toggleEditModal();
        },
    });
};

const submitStatus = () => {
    statusForm.put(route("sales.status", [props.sale.id]), {
        onSuccess: () => {
            toggleStatusModal();
            fetchOrderLogs();
        },
    });
};

const reloadLogs = () => {
    fetchOrderLogs();
};

const fetchOrderLogs = async () => {
    await axios.get(`/sales/${props.sale.id}/logs`).then((response) => {
        orderLogs.value = response.data.data;
    });
};

fetchOrderLogs();
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex flex-row gap-4">
                        <h2
                            class="text-xl font-semibold leading-tight text-gray-800"
                        >
                            Sale Details
                        </h2>
                        <div
                            class="flex flex-row"
                            v-if="sale.approval_status !== 'NEW ORDER'"
                        >
                            <AppChip
                                v-if="sale.approval_status === 'REJECTED'"
                                color="red"
                                >REJECTED</AppChip
                            >
                            <AppChip
                                v-if="sale.approval_status === 'APPROVED'"
                                color="blue"
                                >APPROVED</AppChip
                            >
                        </div>
                    </div>

                    <div class="flex flex-row gap-8 mt-1.5">
                        <div class="flex flex-row items-center text-sm">
                            Organization:
                            <span class="pl-2 text-gray-600">
                                {{ sale.organization_name }}
                            </span>
                        </div>

                        <div class="flex flex-row items-center text-sm">
                            Program Start Date:
                            <span class="pl-2 text-gray-600">
                                {{ formatDate(sale.program_start_date) }}
                            </span>
                        </div>
                        <div class="flex flex-row items-center text-sm">
                            Delivery Date:
                            <span class="pl-2 text-gray-600">
                                {{ formatDate(sale.delivery_date) }}
                            </span>
                        </div>
                    </div>
                    <div
                        class="text-sm mt-1.5 py-1.5 px-2 w-fit border-l-2 shadow bg-white flex flex-row gap-1 items-center"
                        :class="{
                            'border-red-300':
                                sale.approval_status === 'REJECTED',
                            'border-blue-300':
                                sale.approval_status === 'APPROVED',
                        }"
                        v-if="sale.approval_status !== 'NEW ORDER'"
                    >
                        <AppIcon
                            name="mdi:note-outline"
                            color="gray"
                            class="h-4"
                        />
                        <span
                            :class="{
                                'text-red-800':
                                    sale.approval_status === 'REJECTED',
                                'text-blue-800':
                                    sale.approval_status === 'APPROVED',
                            }"
                        >
                            {{ sale.approvalNote }}
                        </span>
                    </div>
                </div>

                <!--                <AppButton icon-left="mdi:plus" @click="toggleCreateModal">-->
                <!--                    Create Sales Items-->
                <!--                </AppButton>-->
                <div class="" v-if="sale.approval_status === 'NEW ORDER'">
                    <span>
                        <AppButton @click="toggleStatusModal">
                            Update Status
                        </AppButton>
                    </span>
                </div>
            </div>
        </template>

        <div class="h-full bg-white">
            <div class="overflow-x-auto">
                <div class="flex flex-col gap-2 pb-6 sm:px-6 lg:px-8">
                    <h3 class="pb-2 font-semibold">Shipping Address</h3>
                    <div class="flex flex-row gap-8">
                        <div class="flex flex-row items-center">
                            <AppIcon
                                name="mdi:account"
                                class="w-5 h-5 mr-2"
                                color="gray"
                            />
                            <p class="text-gray-600">
                                {{ sale.address?.first_name }}
                                {{ sale.address?.last_name }}
                            </p>
                        </div>
                        <div class="flex flex-row items-center">
                            <AppIcon
                                name="mdi:email"
                                class="w-5 h-5 mr-2"
                                color="gray"
                            />
                            <p class="text-gray-600">
                                {{ sale.address?.email }}
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-row">
                        <AppIcon
                            name="mdi:map-marker-outline"
                            class="w-5 h-5 mr-2"
                            color="gray"
                        />
                        <p class="text-gray-600">
                            {{ sale.address?.address1 }},
                            {{ sale.address?.address2 }},
                            {{ sale.address?.city }}, {{ sale.address?.state }},
                            {{ sale.address?.country }},
                            {{ sale.address?.zip }}
                        </p>
                    </div>
                </div>

                <!-- Sales Items Table -->
                <div class="max-h-[620px] overflow-y-auto scrollbar-hide">
                    <table
                        class="w-full my-0 align-middle rounded-lg text-dark border-neutral-200"
                    >
                        <thead class="sticky top-0 align-bottom bg-white">
                            <tr
                                class="font-semibold text-[0.95rem] text-secondary-dark bg-slate-50"
                            >
                                <th class="p-3 pl-8 text-start min-w-[175px]">
                                    SKU
                                </th>
                                <th class="p-3 text-start min-w-[175px]">
                                    Title
                                </th>
                                <th class="p-3 text-start min-w-[175px]">
                                    Quantity
                                </th>
                                <th class="p-3 text-start min-w-[175px]">
                                    Created At
                                </th>
                                <th class="p-3 text-start min-w-[175px]">
                                    Updated At
                                </th>
                                <th class="p-3 text-start"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-if="sale.sales_items.length === 0">
                                <tr>
                                    <td
                                        colspan="6"
                                        class="py-8 text-center text-gray-500"
                                    >
                                        No sales items available.
                                    </td>
                                </tr>
                            </template>
                            <template v-else>
                                <tr
                                    :class="{
                                        'bg-white': index % 2 === 0,
                                        'bg-gray-50': index % 2 !== 0,
                                    }"
                                    v-for="(item, index) in sale.sales_items"
                                    :key="item.id"
                                >
                                    <td class="p-3 pl-8">
                                        <!-- <Link
                                            :href="
                                                route('sales.show', [item.id])
                                            "
                                            class="text-sm text-indigo-600 hover:text-indigo-800"
                                        >
                                            {{ item.id }}
                                        </Link> -->
                                        {{ item.sku }}
                                    </td>
                                    <td class="p-3">{{ item.title }}</td>
                                    <td class="p-3">{{ item.quantity }}</td>
                                    <td class="p-3">
                                        {{ formatDate(item.created_at) }}
                                    </td>
                                    <td class="p-3">
                                        {{ formatDate(item.updated_at) }}
                                    </td>
                                    <td class="p-3">
                                        <!--                                        <AppButton-->
                                        <!--                                            class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-indigo-700 bg-indigo-100 border border-transparent rounded-md hover:bg-indigo-200"-->
                                        <!--                                            @click="toggleEditModal(item)"-->
                                        <!--                                        >-->
                                        <!--                                            <svg-->
                                        <!--                                                xmlns="http://www.w3.org/2000/svg"-->
                                        <!--                                                fill="none"-->
                                        <!--                                                viewBox="0 0 24 24"-->
                                        <!--                                                stroke-width="1.5"-->
                                        <!--                                                stroke="currentColor"-->
                                        <!--                                                class="w-5 h-5"-->
                                        <!--                                            >-->
                                        <!--                                                <path-->
                                        <!--                                                    stroke-linecap="round"-->
                                        <!--                                                    stroke-linejoin="round"-->
                                        <!--                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"-->
                                        <!--                                                />-->
                                        <!--                                            </svg>-->
                                        <!--                                        </AppButton>-->
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <OrderNote :sale="sale" @reloadLogs="reloadLogs()" />
                <OrderLogs :orderLogs="orderLogs" />
            </div>
        </div>

        <!-- Create Sales Item Modal -->
        <Modal :show="showCreateModal" @close="toggleCreateModal">
            <form @submit.prevent="submitCreate" class="p-6">
                <h2 class="text-lg font-semibold text-gray-900">
                    Create Sales Item
                </h2>
                <div class="flex flex-row gap-4 pt-6">
                    <div class="w-2/3">
                        <InputLabel for="title" value="Title" />
                        <TextInput
                            id="title"
                            v-model="createForm.title"
                            type="text"
                            class="block w-full px-3 py-2 mt-1 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                        />
                        <InputError
                            :message="createForm.errors.title"
                            class="mt-2"
                        />
                    </div>
                    <div class="">
                        <InputLabel for="quantity" value="Quantity" />
                        <TextInput
                            id="quantity"
                            v-model="createForm.quantity"
                            type="number"
                            class="block w-full px-3 py-2 mt-1 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                        />
                        <InputError
                            :message="createForm.errors.quantity"
                            class="mt-2"
                        />
                    </div>
                </div>
                <div class="flex justify-end col-span-2 gap-3 mt-6">
                    <AppButton
                        type="button"
                        variant="tertiary"
                        @click="toggleCreateModal"
                    >
                        Cancel
                    </AppButton>
                    <AppButton type="submit" :disabled="createForm.processing">
                        Add Items
                    </AppButton>
                </div>
            </form>
        </Modal>

        <!-- Edit Sales Item Modal -->
        <Modal :show="showEditModal" @close="toggleEditModal">
            <form @submit.prevent="submitEdit" class="p-6">
                <h2 class="text-lg font-semibold text-gray-900">
                    Edit Sales Item
                </h2>
                <div class="flex flex-row gap-4 pt-6">
                    <div class="w-2/3">
                        <InputLabel for="title" value="Title" />
                        <TextInput
                            id="title"
                            v-model="editForm.title"
                            type="text"
                            class="block w-full px-3 py-2 mt-1 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                        />
                        <InputError
                            :message="editForm.errors.title"
                            class="mt-2"
                        />
                    </div>
                    <div class="">
                        <InputLabel for="quantity" value="Quantity" />
                        <TextInput
                            id="quantity"
                            v-model="editForm.quantity"
                            type="number"
                            class="block w-full px-3 py-2 mt-1 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                        />
                        <InputError
                            :message="editForm.errors.quantity"
                            class="mt-2"
                        />
                    </div>
                </div>
                <div class="flex items-center justify-end mt-6">
                    <AppButton
                        type="button"
                        variant="tertiary"
                        @click="toggleEditModal"
                    >
                        Cancel
                    </AppButton>
                    <AppButton
                        type="submit"
                        class="ml-3"
                        :disabled="editForm.processing"
                    >
                        Save
                    </AppButton>
                </div>
            </form>
        </Modal>
        <Modal :show="isOpenStatusModal" @close="toggleStatusModal">
            <form @submit.prevent="submitStatus" class="p-6">
                <h2 class="text-lg font-semibold text-gray-900">
                    Update Sales Status
                </h2>
                <div class="flex flex-row mt-4">
                    <div class="w-full">
                        <InputLabel
                            for="note"
                            class="block mb-1 text-sm font-medium text-gray-900 dark:text-white"
                            value="Order Status"
                        />
                        <select
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-white dark:border-gray-600 dark:placeholder-gray-400"
                            v-model="statusForm.status"
                        >
                            <option disabled value="" selected>
                                Select status
                            </option>
                            <template
                                v-for="status in approvalStatus"
                                :key="status"
                            >
                                <option :value="status">{{ status }}</option>
                            </template>
                        </select>
                        <InputError
                            :message="statusForm.errors.status"
                            class="mt-2"
                        />
                    </div>
                </div>
                <div class="flex flex-row gap-4 mt-3">
                    <div class="w-[450px]">
                        <InputLabel for="note" value="Note" class="mb-1" />
                        <div class="rounded-t-lg">
                            <label for="comment" class="sr-only"
                                >Your Order Notes</label
                            >
                            <textarea
                                id="comment"
                                v-model="statusForm.note"
                                rows="4"
                                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 dark:bg-white dark:border-gray-600 dark:placeholder-gray-400"
                                placeholder="Write a order notes..."
                                required
                            ></textarea>
                        </div>
                        <InputError
                            :message="statusForm.errors.note"
                            class="mt-2"
                        />
                    </div>
                </div>
                <div class="flex justify-end col-span-2 gap-3 mt-6">
                    <AppButton
                        type="button"
                        variant="tertiary"
                        @click="toggleStatusModal"
                    >
                        Cancel
                    </AppButton>
                    <AppButton
                        type="submit"
                        :loading="statusForm.processing"
                        :disabled="statusForm.processing"
                    >
                        Update Status
                    </AppButton>
                </div>
            </form>
        </Modal>
    </AuthenticatedLayout>
</template>
