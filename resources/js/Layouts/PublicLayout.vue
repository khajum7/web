<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <section class="relative grid min-h-full bg-white dark:bg-gray-900 dark:text-gray-400 place-items-center">
        <div class="relative w-full h-full xl:fixed xl:top-0 xl:left-0 xl:w-1/3 aspect-video">
            <img src="/images/background.png" alt="background-image" class="object-[center_15%] custom-image object-cover w-full h-full lg:object-center" loading="lazy">
            <div class="absolute inset-0 w-full h-full lg:h-[300px] bg-gradient-to-t from-transparent to-white/10"/>
            <Link href="/">
                <img src="/images/logo.png" alt="logo" class="z-10 absolute top-0 right-0 h-[80px] w-[150px] sm:h-[150px] sm:w-[250px]" loading="lazy">
            </Link> 
        </div>
        <div class="px-5 xl:container">
            <div class="grid grid-cols-12 gap-6">
                <div class="z-10 col-span-12 xl:col-span-8 xl:col-start-5 place-content-center">
                    <slot />
                </div>
            </div>
        </div>

    </section>
</template>