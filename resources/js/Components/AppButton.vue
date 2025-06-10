<template>
    <a
        v-if="isExternalLink"
        :href="to"
        :class="[
            classes,
            disabled ? 'cursor-not-allowed opacity-50' : '',
            !loading ? '' : 'opacity-50 relative',
        ]"
        target="_blank"
        v-bind="$attrs"
    >
        <app-icon v-if="iconLeft" :name="iconLeft" />
        <template v-else>
            <slot />
        </template>
        <app-icon v-if="iconRight" :name="iconRight" />
    </a>
    <component
        :is="linkType"
        v-else
        :to="to"
        v-bind="$attrs"
        :type="linkType === 'button' ? type : undefined"
        :disabled="!loading ? disabled : ''"
        :class="[
            disabled ? 'cursor-not-allowed opacity-50' : '',
            !loading ? '' : 'opacity-50 relative',
            classes,
        ]"
    >
        <span
            v-if="loading"
            class="absolute inset-0 flex items-center justify-center"
        >
            <app-icon
                class="animate-spin"
                name="line-md:loading-twotone-loop"
            />
        </span>
        <app-icon v-if="iconLeft" :name="iconLeft" />
        <slot />
        <app-icon v-if="iconRight" :name="iconRight" />
    </component>
</template>

<script setup lang="ts">
import { computed, useAttrs } from "vue";
import { twMerge } from "tailwind-merge";
import AppIcon from "./AppIcon.vue";

const variants = {
    primary: "bg-primary-500 text-white",
    secondary: "bg-white border border-primary-500 text-primary-500",
    tertiary:
        "border border-transparent text-indigo-500 hover:bg-gray-200 dark:hover:bg-gray-900",
} as const;
type VariantKeys = keyof typeof variants;
interface Props {
    type?: "submit" | "button" | "reset";
    disabled?: boolean;
    loading?: boolean;
    to?: string;
    outline?: boolean;
    text?: boolean;
    icon?: boolean;
    iconLeft?: string;
    iconRight?: string;
    variant?: VariantKeys;
}
const props = withDefaults(defineProps<Props>(), {
    type: "button",
    disabled: false,
    loading: false,
    outline: false,
    text: false,
    icon: false,
    iconLeft: "",
    iconRight: "",
    variant: "primary" as VariantKeys,
});

const linkType = computed(() => {
    return props.to ? "router-link" : "button";
});

const attrs: any = useAttrs();

const classes = computed(() => {
    return twMerge(
        `transition-all inline-flex justify-center py-2 rounded-md whitespace-pre items-center h-[38px] gap-x-2 no-underline`,
        props.icon ? "p-2 rounded-full" : props.iconLeft ? "pl-2 pr-4" : "px-4",
        variants[props.variant],
        attrs.class
    );
});
const isExternalLink = computed(() => {
    return typeof props.to === "string" && props.to.startsWith("https");
});
</script>
