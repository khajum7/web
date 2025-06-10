<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
// import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import AppButton from '@/Components/AppButton.vue';
// import AppDatepicker from "@/Components/AppDatePicker.vue";

const props = defineProps({
    'saleItem': Object
});

const saleItem = props.saleItem || {}

const form = useForm({
    title: saleItem.title,
    quantity: saleItem.quantity,
    sale_id: saleItem.sale_id,
});

const submit = () => {
    form.put(route('sales.items.update', [saleItem.id]), {

    });
};

</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Sales</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <form @submit.prevent="submit" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 grid grid-cols-2 gap-5">
                    <div class="">
                        <InputLabel for="organization_name" value="Title" />
                        <TextInput
                            id="organization_name"
                            type="text"
                            class="mt-1 block shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            v-model="form.title"
                            required
                            autocomplete="new-password"

                        />

                        <InputError class="mt-2" :message="form.errors.title" />
                    </div>

                    <div class="">
                        <InputLabel for="first_name" value="Quantity" />

                        <TextInput
                            id="organization_name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.quantity"
                            required
                            autocomplete="new-password"
                        />

                        <InputError class="mt-2" :message="form.errors.quantity" />
                    </div>

                    <div class="flex items-center  mt-4">
                        <AppButton type="submit"  :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Update Sales Item
                        </AppButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
