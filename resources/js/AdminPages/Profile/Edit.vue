<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Profile</h2>
        </template>

        <div class="bg-white h-full">
            <div class=" py-4 sm:px-6 lg:px-8">
                <div class=" ">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div class="py-8">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <!-- <div class="p-4 sm:p-8 "> -->
                    <!-- <DeleteUserForm class="max-w-xl" /> -->
                <!-- </div> -->
            </div>
        </div>
    </AuthenticatedLayout>
</template>
