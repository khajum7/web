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
    sales: Array,
});

const showCreateModal = ref(false);
// const showEditModal = ref(false);
const selectedSale = ref(null);

const createForm = useForm({
    delivery_date: "",
    program_start_date: "",
    organization_name: "",
    first_name: "",
    last_name: "",
    email: "",
    phone: "",
    address1: "",
    address2: "",
    city: "",
    state: "",
    zip: "",
    country: "",
});

const editForm = useForm({
    delivery_date: "",
    program_start_date: "",
    organization_name: "",
    first_name: "",
    last_name: "",
    email: "",
    phone: "",
    address1: "",
    address2: "",
    city: "",
    state: "",
    zip: "",
    country: "",
});

const selectApprovalStatus = ref([
    { approved: "All", value: "" },
    { approved: "New Order", value: "0" },
    { approved: "Approved", value: "1" },
    { approved: "Rejected", value: "2" },
]);

const dateFilterForm = useForm({
    approval_status: "",
});

const toggleCreateModal = () => {
    showCreateModal.value = !showCreateModal.value;
    if (!showCreateModal.value) {
        createForm.reset();
    }
};

// const toggleEditModal = (sale = null) => {
//     if (sale) {
//         selectedSale.value = sale;

//         editForm.delivery_date = sale.delivery_date || "";
//         editForm.program_start_date = sale.program_start_date || "";
//         editForm.organization_name = sale.organization_name || "";

//         const address = sale.address || {};
//         editForm.first_name = address.first_name || "";
//         editForm.last_name = address.last_name || "";
//         editForm.email = address.email || "";
//         editForm.phone = address.phone || "";
//         editForm.address1 = address.address1 || "";
//         editForm.address2 = address.address2 || "";
//         editForm.city = address.city || "";
//         editForm.state = address.state || "";
//         editForm.zip = address.zip || "";
//         editForm.country = address.country || "";
//     } else {
//         selectedSale.value = null;
//         editForm.reset();
//     }
//     showEditModal.value = !showEditModal.value;
// };

const submitCreate = () => {
    createForm.post(route("sales.store"), {
        onSuccess: () => {
            createForm.reset();
            toggleCreateModal();
        },
    });
};

// const submitEdit = () => {
//     if (selectedSale.value) {
//         editForm.put(route("sales.update", [selectedSale.value.id]), {
//             onSuccess: () => {
//                 editForm.reset();
//                 toggleEditModal();
//             },
//         });
//     }
// };

const submitDateFilter = () => {
    const params = {
        start_from: dateFilterForm.start_from,
        end_to: dateFilterForm.end_to,
    };

    if (dateFilterForm.approval_status) {
        params.approval_status = dateFilterForm.approval_status;
    }

    dateFilterForm.get(route("sales.index", params), {
        preserveState: true,
        replace: true,
    });
};

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
                    Sales List
                </h2>
                <!--                <AppButton icon-left="mdi:plus" @click="toggleCreateModal"-->
                <!--                    >Create Sales</AppButton-->
                <!--                >-->
            </div>
            <div class="flex justify-start">
                <div class="mt-4">
                    <form @submit.prevent="submitDateFilter">
                        <div class="grid grid-cols-4 gap-3 items-end">
                            <div>
                                <AppDatepicker
                                    name="start_from"
                                    label="From"
                                    v-model="dateFilterForm.start_from"
                                    :range="false"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="dateFilterForm.errors.start_from"
                                />
                            </div>
                            <div>
                                <AppDatepicker
                                    name="end_to"
                                    label="To"
                                    v-model="dateFilterForm.end_to"
                                    :range="false"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="dateFilterForm.errors.end_to"
                                />
                            </div>
                            <div>
                                <InputLabel
                                    for="approval_status"
                                    value="Select Approved Status"
                                    class="text-sm text-gray-500 mb-1"
                                />
                                <select
                                    name="approval_status"
                                    v-model="dateFilterForm.approval_status"
                                    id="approval_status"
                                    class="w-full rounded-md border h-[38px] border-gray-300 text-sm"
                                >
                                    <option
                                        value=""
                                        class="text-gray-300"
                                        disabled
                                        selected
                                    >
                                        Select Status
                                    </option>
                                    <option
                                        v-for="selectApprovalStatu in selectApprovalStatus"
                                        :value="selectApprovalStatu.value"
                                    >
                                        {{ selectApprovalStatu.approved }}
                                    </option>
                                </select>
                            </div>
                            <div class="mt-1">
                                <AppButton type="submit" icon-left="mdi:search"
                                    >Search</AppButton
                                >
                            </div>
                        </div>
                    </form>
                </div>
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
                                    ID
                                </th>
                                <th class="p-3 text-start min-w-[175px]">
                                    Organization Name
                                </th>
                                <th class="p-3 text-start min-w-[175px]">
                                    Order Status
                                </th>
                                <th class="p-3 text-start min-w-[175px]">
                                    Approval Status
                                </th>
                                <th class="p-3 text-start min-w-[175px]">
                                    Delivery Date
                                </th>
                                <th class="p-3 text-start min-w-[175px]">
                                    Program Start Date
                                </th>
                                <!-- <th class="p-3 text-start min-w-[100px]">
                                    Action
                                </th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(sale, index) in sales"
                                :key="sale.id"
                                :class="{
                                    'bg-white': index % 2 === 0,
                                    'bg-gray-50': index % 2 !== 0,
                                }"
                            >
                                <td class="p-3 pl-8">
                                    <Link
                                        :href="route('sales.show', [sale.id])"
                                        class="text-sm font-semibold text-indigo-600 hover:text-indigo-800"
                                    >
                                        {{ sale.id }}
                                    </Link>
                                </td>
                                <td class="p-3">
                                    {{ sale.organization_name }}
                                </td>
                                <td class="p-3">
                                    <div
                                        class="relative grid select-none w-fit items-center whitespace-nowrap rounded-md bg-indigo-300 text-indigo-800 py-1.5 px-3 font-sans text-xs font-bold uppercase"
                                    >
                                        {{ sale.status }}
                                    </div>
                                </td>
                                <td class="p-3">
                                    <div
                                        :class="{
                                            'relative w-fit grid select-none items-center whitespace-nowrap rounded-md py-1.5 px-3 font-sans text-xs font-bold uppercase text-white': true,
                                            'bg-blue-500 text-blue-800  ':
                                                sale.approval_status ===
                                                'APPROVED',
                                            'bg-red-500 text-red-800 ':
                                                sale.approval_status ===
                                                'REJECTED',
                                            'bg-green-500 text-green-800  ':
                                                sale.approval_status ===
                                                'NEW ORDER',
                                        }"
                                    >
                                        {{ sale.approval_status }}
                                    </div>
                                </td>

                                <td class="p-3">
                                    {{ formatDate(sale.delivery_date) }}
                                </td>
                                <td class="p-3">
                                    {{ formatDate(sale.program_start_date) }}
                                </td>
                                <td>
                                    <!-- <div class="grid grid-cols-3 gap-0.5 pl-3">
                                        <a
                                            href="javascript:void(0);"
                                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none"
                                            @click.prevent="
                                                toggleEditModal(sale)
                                            "
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                class="size-6"
                                                color="indigo"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"
                                                />
                                            </svg>
                                        </a>
                                    </div> -->
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <Modal :show="showCreateModal" @close="toggleCreateModal">
            <form @submit.prevent="submitCreate" class="p-6">
                <h2 class="text-xl font-semibold text-gray-900">
                    Create Sales
                </h2>
                <div class="pt-6">
                    <div class="grid grid-cols-2 gap-3 pb-4">
                        <div>
                            <InputLabel
                                for="delivery_date"
                                value="Delivery Date"
                            />
                            <AppDatepicker
                                name="delivery_date"
                                v-model="createForm.delivery_date"
                                :range="false"
                            />
                            <InputError
                                class="mt-2"
                                :message="createForm.errors.delivery_date"
                            />
                        </div>
                        <div>
                            <InputLabel
                                for="program_start_date"
                                value="Program Start Date"
                            />
                            <AppDatepicker
                                name="program_start_date"
                                v-model="createForm.program_start_date"
                                :range="false"
                            />
                            <InputError
                                class="mt-2"
                                :message="createForm.errors.program_start_date"
                            />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 pb-4">
                        <div>
                            <InputLabel
                                for="organization_name"
                                value="Organization Name"
                            />
                            <TextInput
                                id="organization_name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="createForm.organization_name"
                                required
                                autocomplete="new-password"
                            />
                            <InputError
                                class="mt-2"
                                :message="createForm.errors.organization_name"
                            />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pb-4">
                        <div>
                            <InputLabel for="first_name" value="First Name" />
                            <TextInput
                                id="first_name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="createForm.first_name"
                                required
                                autocomplete="new-password"
                            />
                            <InputError
                                class="mt-2"
                                :message="createForm.errors.first_name"
                            />
                        </div>
                        <div>
                            <InputLabel for="last_name" value="Last Name" />
                            <TextInput
                                id="last_name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="createForm.last_name"
                                required
                                autocomplete="new-password"
                            />
                            <InputError
                                class="mt-2"
                                :message="createForm.errors.last_name"
                            />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pb-4">
                        <div>
                            <InputLabel for="email" value="Email" />
                            <TextInput
                                id="email"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="createForm.email"
                                required
                                autocomplete="new-password"
                            />
                            <InputError
                                class="mt-2"
                                :message="createForm.errors.email"
                            />
                        </div>
                        <div>
                            <InputLabel for="phone" value="Phone" />
                            <TextInput
                                id="phone"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="createForm.phone"
                                required
                                autocomplete="new-password"
                            />
                            <InputError
                                class="mt-2"
                                :message="createForm.errors.phone"
                            />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pb-4">
                        <div>
                            <InputLabel for="address1" value="Address 1" />
                            <TextInput
                                id="address1"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="createForm.address1"
                                required
                                autocomplete="new-password"
                            />
                            <InputError
                                class="mt-2"
                                :message="createForm.errors.address1"
                            />
                        </div>
                        <div>
                            <InputLabel for="address2" value="Address 2" />
                            <TextInput
                                id="address2"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="createForm.address2"
                                required
                                autocomplete="new-password"
                            />
                            <InputError
                                class="mt-2"
                                :message="createForm.errors.address2"
                            />
                        </div>
                    </div>
                    <div class="grid grid-cols-4 gap-3 pb-4">
                        <div>
                            <InputLabel for="city" value="City" />
                            <TextInput
                                id="city"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="createForm.city"
                                required
                                autocomplete="new-password"
                            />
                            <InputError
                                class="mt-2"
                                :message="createForm.errors.city"
                            />
                        </div>
                        <div>
                            <InputLabel for="state" value="State" />
                            <TextInput
                                id="state"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="createForm.state"
                                required
                                autocomplete="new-password"
                            />
                            <InputError
                                class="mt-2"
                                :message="createForm.errors.state"
                            />
                        </div>
                        <div>
                            <InputLabel for="zip" value="Zip Code" />
                            <TextInput
                                id="zip"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="createForm.zip"
                                required
                                autocomplete="new-password"
                            />
                            <InputError
                                class="mt-2"
                                :message="createForm.errors.zip"
                            />
                        </div>
                        <div>
                            <InputLabel for="country" value="Country" />
                            <TextInput
                                id="country"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="createForm.country"
                                required
                                autocomplete="new-password"
                            />
                            <InputError
                                class="mt-2"
                                :message="createForm.errors.country"
                            />
                        </div>
                    </div>
                </div>
                <div class="flex justify-end pt-6">
                    <AppButton
                        type="button"
                        class="ml-3"
                        variant="tertiary"
                        @click="toggleCreateModal"
                    >
                        Cancel
                    </AppButton>
                    <AppButton
                        type="submit"
                        :loading="createForm.processing"
                        class="ml-3"
                    >
                        Save
                    </AppButton>
                </div>
            </form>
        </Modal>

        <!-- Edit Modal -->
        <!-- <Modal :show="showEditModal" @close="toggleEditModal">
            <form @submit.prevent="submitEdit" class="p-6">
                <h2 class="text-xl font-semibold text-gray-900">Edit Sales</h2>
                <div class="pt-6">
                    <div class="grid grid-cols-2 gap-3 pb-4">
                        <div>
                            <InputLabel
                                for="delivery_date"
                                value="Delivery Date"
                            />
                            <AppDatepicker
                                name="delivery_date"
                                v-model="editForm.delivery_date"
                                :range="false"

                            />
                            <InputError
                                class="mt-2"
                                :message="editForm.errors.delivery_date"
                            />
                        </div>
                        <div>
                            <InputLabel
                                for="program_start_date"
                                value="Program Start Date"
                            />
                            <AppDatepicker
                                name="program_start_date"
                                v-model="editForm.program_start_date"
                                :range="false"

                            />
                            <InputError
                                class="mt-2"
                                :message="editForm.errors.program_start_date"
                            />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 pb-4">
                        <div>
                            <InputLabel
                                for="organization_name"
                                value="Organization Name"
                            />
                            <TextInput
                                id="organization_name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="editForm.organization_name"
                                required
                                autocomplete="new-password"
                            />
                            <InputError
                                class="mt-2"
                                :message="editForm.errors.organization_name"
                            />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pb-4">
                        <div>
                            <InputLabel for="first_name" value="First Name" />
                            <TextInput
                                id="first_name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="editForm.first_name"
                                required
                                autocomplete="new-password"
                            />
                            <InputError
                                class="mt-2"
                                :message="editForm.errors.first_name"
                            />
                        </div>
                        <div>
                            <InputLabel for="last_name" value="Last Name" />
                            <TextInput
                                id="last_name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="editForm.last_name"
                                required
                                autocomplete="new-password"
                            />
                            <InputError
                                class="mt-2"
                                :message="editForm.errors.last_name"
                            />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pb-4">
                        <div>
                            <InputLabel for="email" value="Email" />
                            <TextInput
                                id="email"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="editForm.email"
                                required
                                autocomplete="new-password"
                            />
                            <InputError
                                class="mt-2"
                                :message="editForm.errors.email"
                            />
                        </div>
                        <div>
                            <InputLabel for="phone" value="Phone" />
                            <TextInput
                                id="phone"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="editForm.phone"
                                required
                                autocomplete="new-password"
                            />
                            <InputError
                                class="mt-2"
                                :message="editForm.errors.phone"
                            />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pb-4">
                        <div>
                            <InputLabel for="address1" value="Address 1" />
                            <TextInput
                                id="address1"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="editForm.address1"
                                required
                                autocomplete="new-password"
                            />
                            <InputError
                                class="mt-2"
                                :message="editForm.errors.address1"
                            />
                        </div>
                        <div>
                            <InputLabel for="address2" value="Address 2" />
                            <TextInput
                                id="address2"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="editForm.address2"
                                required
                                autocomplete="new-password"
                            />
                            <InputError
                                class="mt-2"
                                :message="editForm.errors.address2"
                            />
                        </div>
                    </div>
                    <div class="grid grid-cols-4 gap-3 pb-4">
                        <div>
                            <InputLabel for="city" value="City" />
                            <TextInput
                                id="city"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="editForm.city"
                                required
                                autocomplete="new-password"
                            />
                            <InputError
                                class="mt-2"
                                :message="editForm.errors.city"
                            />
                        </div>
                        <div>
                            <InputLabel for="state" value="State" />
                            <TextInput
                                id="state"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="editForm.state"
                                required
                                autocomplete="new-password"
                            />
                            <InputError
                                class="mt-2"
                                :message="editForm.errors.state"
                            />
                        </div>
                        <div>
                            <InputLabel for="zip" value="Zip Code" />
                            <TextInput
                                id="zip"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="editForm.zip"
                                required
                                autocomplete="new-password"
                            />
                            <InputError
                                class="mt-2"
                                :message="editForm.errors.zip"
                            />
                        </div>
                        <div>
                            <InputLabel for="country" value="Country" />
                            <TextInput
                                id="country"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="editForm.country"
                                required
                                autocomplete="new-password"
                            />
                            <InputError
                                class="mt-2"
                                :message="editForm.errors.country"
                            />
                        </div>
                    </div>
                </div>
                <div class="flex justify-end pt-6">
                    <AppButton
                        type="button"
                        variant="tertiary"
                        @click="toggleEditModal"
                    >
                        Cancel
                    </AppButton>
                    <AppButton type="submit" class="ml-3"> Save </AppButton>
                </div>
            </form>
        </Modal> -->
    </AuthenticatedLayout>
</template>
