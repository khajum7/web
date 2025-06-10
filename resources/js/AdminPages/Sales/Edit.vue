<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/vue3';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import AppDatepicker from "@/Components/AppDatePicker.vue";
import TextInput from "@/Components/TextInput.vue";

const props =defineProps({
    'sale' : Object
});

const sale = props.sale || {};

const form = useForm({
    delivery_date: sale.delivery_date,
    program_start_date: sale.program_start_date,
    organization_name: sale.organization_name,
    first_name: sale.address.first_name,
    last_name: sale.address.last_name,
    email: sale.address.email,
    phone: sale.address.phone,
    address1 : sale.address.address1,
    address2 : sale.address.address2,
    city: sale.address.city,
    state: sale.address.state,
    zip: sale.address.zip,
    country: sale.address.country,
    address_id: sale.address.id
});

const submit = () => {
    form.put(route('sales.update', [sale.id]), {
    });
};
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sales List</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <form @submit.prevent="submit" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 grid grid-cols-2 gap-5">
                    <div class="">
                        <InputLabel for="delivery_date" value="Delivery Date" />
                        <AppDatepicker
                            name="delivery_date"
                            label="Delivery Date"
                            v-model="form.delivery_date"
                        />

                        <InputError class="mt-2" :message="form.errors.delivery_date" />
                    </div>
                    <div class="">
                        <InputLabel for="program_start_date" value="Program Start Date" />

                        <AppDatepicker
                            name="program_start_date"
                            label="Program Start Date"
                            v-model="form.program_start_date"
                        />

                        <InputError class="mt-2" :message="form.errors.program_start_date" />
                    </div>
                    <div class="">
                        <InputLabel for="organization_name" value="Organization Name" />

                        <TextInput
                            id="organization_name"
                            type="text"
                            class="mt-1 block shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            v-model="form.organization_name"
                            required
                            autocomplete="new-password"

                        />

                        <InputError class="mt-2" :message="form.errors.organization_name" />
                    </div>

                    <div class="">
                        <InputLabel for="first_name" value="First Name" />

                        <TextInput
                            id="organization_name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.first_name"
                            required
                            autocomplete="new-password"
                        />

                        <InputError class="mt-2" :message="form.errors.first_name" />
                    </div>


                    <div class="">
                        <InputLabel for="last_name" value="Last Name" />

                        <TextInput
                            id="organization_name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.last_name"
                            required
                            autocomplete="new-password"
                        />

                        <InputError class="mt-2" :message="form.errors.last_name" />
                    </div>

                    <div class="">
                        <InputLabel for="email" value="Email" />

                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autocomplete="username"
                        />

                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="">
                        <InputLabel for="phone" value="Phone" />

                        <TextInput
                            id="email"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.phone"
                            required
                            autocomplete="username"
                        />

                        <InputError class="mt-2" :message="form.errors.phone" />
                    </div>

                    <div class="">
                        <InputLabel for="address1" value="Address 1" />

                        <TextInput
                            id="address1"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.address1"
                            required
                            autocomplete="username"
                        />

                        <InputError class="mt-2" :message="form.errors.address1" />
                    </div>
                    <div class="">
                        <InputLabel for="address2" value="Address 2" />

                        <TextInput
                            id="address2"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.address2"
                            autocomplete="username"
                        />

                        <InputError class="mt-2" :message="form.errors.address2" />
                    </div>

                    <div class="">
                        <InputLabel for="city" value="City" />

                        <TextInput
                            id="city"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.city"
                            required
                            autocomplete="username"
                        />

                        <InputError class="mt-2" :message="form.errors.city" />
                    </div>

                    <div class="">
                        <InputLabel for="state" value="state" />

                        <TextInput
                            id="state"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.state"
                            required
                            autocomplete="username"
                        />

                        <InputError class="mt-2" :message="form.errors.state" />
                    </div>

                    <div class="">
                        <InputLabel for="zip" value="Zip" />

                        <TextInput
                            id="zip"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.zip"
                            required
                            autocomplete="username"
                        />

                        <InputError class="mt-2" :message="form.errors.zip" />
                    </div>

                    <div class="">
                        <InputLabel for="country" value="Country" />

                        <TextInput
                            id="city"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.country"
                            required
                            autocomplete="username"
                        />

                        <InputError class="mt-2" :message="form.errors.country" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Add Sales
                        </PrimaryButton>
                    </div>
                </form>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
