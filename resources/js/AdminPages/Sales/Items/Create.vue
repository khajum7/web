<!-- <script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
// import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import AppButton from "@/Components/AppButton.vue";

const props = defineProps({
    sale: Object,
});

const sale_id = props.sale?.id || "";

const form = useForm({
    title: "",
    quantity: "",
    sale_id: sale_id,
});

const submit = () => {
    form.post(route("sales.items.store"), {});
};
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Create Sales
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <form
                    @submit.prevent="submit"
                    class="grid grid-cols-2 gap-5 px-8 pt-6 pb-8 mb-4 bg-white rounded shadow-md"
                >
                    <div class="">
                        <InputLabel for="organization_name" value="Title" />
                        <TextInput
                            id="organization_name"
                            type="text"
                            class="block w-full px-3 py-2 mt-1 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
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
                            class="block w-full mt-1"
                            v-model="form.quantity"
                            required
                            autocomplete="new-password"
                        />

                        <InputError
                            class="mt-1"
                            :message="form.errors.quantity"
                        />
                    </div>

                    <div class="mt-3">
                        <AppButton
                            type="submit"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Add Sales
                        </AppButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template> -->
<template>
    <div></div>
</template>
