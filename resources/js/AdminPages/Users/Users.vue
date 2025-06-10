<script setup>
import AppButton from "@/Components/AppButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";

defineProps({
    users: Array,
});

const showModal = ref(false);

const form = useForm({
    name: "",
    email: "",
});

const toggleModal = () => {
    showModal.value = !showModal.value;
};

const submit = () => {
    form.post(route("users.store"), {
        onSuccess: () => {
            form.reset();
            toggleModal();
        },
    });
};
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    User List
                </h2>
                <AppButton icon-left="mdi:plus" @click="toggleModal">Create User</AppButton>
            </div>
        </template>
        <div class="bg-white h-screen">
            <div>
                <div class="py-4">
                    <section>
                        <header>
                            <!-- <h2 class="text-lg font-medium text-gray-900 sm:px-6 lg:px-8">
                                User List
                            </h2> -->
                        </header>
                        <div class="overflow-x-auto">
                            <table class="w-full my-0 align-middle text-dark border-neutral-200">
                                <thead class="align-bottom">
                                    <tr class="font-semibold text-[0.95rem] text-secondary-dark bg-slate-50">
                                        <th class="p-3 pl-8 text-start min-w-[175px]">ID</th>
                                        <th class="p-3 text-start min-w-[175px]">Name</th>
                                        <th class="p-3 text-start min-w-[175px]">Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user in users" :key="user.id" class="border-b border-dashed last:border-b-0">
                                        <td class="p-3 pl-8">{{ user.id }}</td>
                                        <td class="p-3">{{ user.name }}</td>
                                        <td class="p-3">{{ user.email }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <Modal :show="showModal" @close="toggleModal">
            <form @submit.prevent="submit" class="p-6">
                <h2 class="text-xl font-semibold text-gray-900">Create User</h2>

                <div class="mt-4">
                    <InputLabel for="name" value="Name" />
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full dark:bg-white"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
                <div class="mt-4">
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full dark:bg-white"
                        v-model="form.email"
                        required
                        autocomplete="username"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-6 flex justify-end">
                    <AppButton class="mr-3" variant="tertiary" @click="toggleModal">Cancel</AppButton>
                    <AppButton type="submit" :loading="form.processing" :disabled="form.processing">Create User</AppButton>
                </div>
            </form>
        </Modal>
    </AuthenticatedLayout>
</template>
