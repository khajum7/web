<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import AppButton from "@/Components/AppButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};

const currentYear = ref(new Date().getFullYear());

</script>

<template>
    <GuestLayout>
        <Head title="Log in" />
        <div class="flex flex-col items-center mb-4">
            <ApplicationLogo class="w-20 h-20 fill-current text-gray-500" />
        </div>

        <form @submit.prevent="submit" class="space-y-6 mt-6 px-4">
            <div>
                <InputLabel for="email" value="Enter your email address" />

                <TextInput
                    id="email"
                    type="email"
                    class="dark:text-gray-300"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError
                    v-if="form.errors.email"
                    class="mt-2"
                    :message="form.errors.email"
                />
            </div>

            <div>
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    label="Password"
                    class="dark:text-gray-300"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError
                    v-if="form.errors.password"
                    class="mt-2"
                    :message="form.errors.password"
                />
            </div>

            <div class="flex flex-col items-center">
                <AppButton
                    icon-left="mdi:login"
                    type="submit"
                    aria-label="Sign In"
                    class="w-full"
                    :loading="form.processing"
                    :disabled="form.processing"
                >
                    Log In
                </AppButton>
               
            </div>
        </form>
        <div class="mt-6 px-4">
            <Link href="/forget-password">Forget password</Link>
        </div>
        
        <div class="mt-6 px-4">
            <a class="text-xs text-gray-400">
                © {{ currentYear }} Junior Jazz Order Portal. All rights reserved.
            </a>
        </div>
    </GuestLayout>
</template>
