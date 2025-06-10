<script setup>
import { computed } from "vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import { Link } from "@inertiajs/vue3";
import AppIcon from "@/Components/AppIcon.vue";
import AppButton from "@/Components/AppButton.vue";
import FlashMessage from "@/Components/FlashMessage.vue";

const page = usePage();
const userName = computed(() => page.props.auth.user.name);

const initials = computed(() => {
    const nameParts = userName.value.split(" ");
    if (nameParts.length === 1) {
        return nameParts[0].substring(0, 2).toUpperCase();
    } else {
        // taking the first letter of each word
        return nameParts
            .map((part) => part[0])
            .join("")
            .toUpperCase();
    }
});

const searchData = useForm({
    search: "",
});

const search = () => {
    const params = {
        search: searchData.search,
    };
    searchData.get(route("sales.search", params));
};
</script>

<template>
    <div class="bg-gray-100">
        <!-- <FlashMessage /> -->
        <!-- Sidebar -->

        <nav
            class="bg-white w-64 h-full flex flex-col border border-indigo-50 fixed overflow-y-auto z-40 overflow-x-hidden"
        >
            <div class="px-6">
                <Link :href="route('dashboard')">
                    <ApplicationLogo
                        class="block h-6 w-auto fill-current text-gray-800"
                    />
                </Link>
            </div>
            <!-- <div class="mt-8 px-8">
                <p class="text-sm ">
                    {{ initials }}<br/>
                    {{ page.props.auth.user.email }}
                </p>
            </div> -->
            <!-- Navigation Links -->
            <div class="flex flex-col mt-2 mx-4 py-4 space-y-1">
                <NavLink
                    :href="route('dashboard')"
                    :active="route().current('dashboard')"
                >
                    <AppIcon name="mdi:home" class="h-4 mr-1" />
                    Dashboard
                </NavLink>
                <NavLink
                    class="flex items-center"
                    :href="route('users.index')"
                    :active="route().current('users.index')"
                >
                    <AppIcon name="mdi:user" class="h-4 mr-1" />
                    Users
                </NavLink>
                <NavLink
                    :href="route('sales.index')"
                    :active="route().current('sales.index')"
                >
                    <AppIcon name="mdi:cart-percent" class="h-4 mr-1" />
                    Sales
                </NavLink>
                <NavLink
                    :href="route('product.items.index')"
                    :active="route().current('product.items.index')"
                >
                    <AppIcon name="mdi:clipboard-list" class="h-4 mr-1" />
                    Products
                </NavLink>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col ml-64">
            <!-- Top Navigation Bar -->
            <nav class="bg-gray-50 border-b border-gray-100 sticky top-0 z-10">
                <div class="sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-20 gap-6">
                        <!-- Search Form -->
                        <div class="w-full">
                            <form
                                @submit.prevent="search"
                                class="flex items-center w-full bg-white shadow rounded-full"
                            >
                                <input
                                    class="flex-grow bg-white border-0 rounded-full hover:bg-white focus:ring-2 focus:ring-indigo-600 text-gray-800 mx-4"
                                    placeholder="Search Anything..."
                                    v-model="searchData.search"
                                    data-cy="search"
                                    required
                                />
                                <div class="flex items-center py-2 pr-3">
                                    <AppButton
                                        type="submit"
                                        icon-left="mdi:magnify"
                                        class="rounded-full"
                                        >Search</AppButton
                                    >
                                </div>
                            </form>
                        </div>
                        <!-- <div class="flex items-center w-3/5">
                            <form @submit.prevent="search" class="flex w-full">
                                <input 
                                    type="text" 
                                    placeholder="search sales..." 
                                    v-model="searchData.search" 
                                    class="w-full rounded-lg" 
                                    required
                                    autocomplete="off">
                                <button type="submit" class="ml-2 px-4 py-2 bg-indigo-600 text-white rounded-lg">
                                    Search
                                </button>
                            </form>
                        </div> -->

                        <div class="flex items-center">
                            <!-- User Dropdown -->
                            <div class="flex justify-end items-center gap-4">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button
                                            type="button"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-full bg-gray-200 text-gray-800 hover:text-gray-900 focus:outline-none transition ease-in-out duration-150"
                                        >
                                            {{ initials }}
                                            <svg
                                                class="ml-2 -mr-0.5 h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                            icon-left="mdi:account-outline"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                            icon-left="mdi:login"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white" v-if="$slots.header">
                <div class="py-6 px-8 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1">
                <slot />
            </main>
        </div>
    </div>
</template>
