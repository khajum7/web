<script setup lang="ts">
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import { onMounted, ref, toRef, watch } from "vue";
import { Field, useField } from "vee-validate";

defineOptions({
    name: "AppDatepicker",
    inheritAttrs: false,
});

interface PropsInterface {
    label?: string;
    modelValue?: any[] | string;
    name: string;
    range?: boolean;
    format?: string;
    clearable?: boolean;
    placeholder?: string; 
}

const props = withDefaults(defineProps<PropsInterface>(), {
    label: "",
    range: false,
    format: "MM/dd/yyyy",
    clearable: true,
    placeholder: "MM/DD/YYYY", 
});

const name = toRef(props, "name");
const emit = defineEmits(["update:modelValue"]);
const inputPdding = ref("12px 16px");
const iconTransform = ref("-50%");

const { value: inputValue } = useField(name, undefined, {
    initialValue: props.modelValue,
});

onMounted(() => {
    if (props.modelValue) {
        inputPdding.value = "14px 16px 6px 16px";
        iconTransform.value = "-40%";
    } else {
        inputPdding.value = "12px 16px";
        iconTransform.value = "-50%";
    }
});

watch(inputValue, (val) => {
    emit("update:modelValue", val);
});

watch(
    () => props.modelValue,
    (val) => {
        if (val !== inputValue.value) {
            inputValue.value = val as any;
        }
        if (val) {
            inputPdding.value = "14px 16px 6px 16px";
            iconTransform.value = "-38%";
        } else {
            inputPdding.value = "12px 16px";
            iconTransform.value = "-50%";
        }
    }
);
</script>

<template>
    <Field :name="name" v-model="inputValue">
        <label v-if="label" class="block mb-1 text-sm font-medium">{{ label }}</label>
        <div
            class="relative flex items-end overflow-hidden border border-gray-300 rounded-md dark:border-gray-600"
            :class="range ? 'min-w-fit' : 'min-w-fit'"
        >
            <VueDatePicker
                v-model="inputValue"
                :enable-time-picker="false"
                auto-apply
                auto-position
                allow-prevent-default
                :teleport="true"

                :dark="false"
                :range="range"
                model-type="yyyy-MM-dd"
                :format="format"
                class="app_datepicker"
                :clearable="clearable"
                :placeholder="props.placeholder"
            />
        </div>
    </Field>
</template>

<style>
.app_datepicker .dp__input {
    @apply border-none transition-all !bg-white dark:!bg-gray-800 dark:text-gray-200;
    --dp-font-size: 12px;
    --dp-input-padding: 8px 12px;
    height: 36px;
    display: flex;
    align-items: center;
}

.dp__range_end,
.dp__range_start,
.dp__active_date {
    @apply bg-blue-500 dark:text-blue-50;
}

.dp__today {
    @apply border-blue-300 dark:border-slate-300;
}

.dp__range_between {
    @apply bg-blue-50/70 dark:border-gray-700;
}

.dp__arrow_top,
.dp__theme_light {
    @apply dark:bg-gray-800;
}

.dp__btn,
.dp--time-overlay-btn,
.dp--time-invalid,
.dp__calendar_header_item,
.dp__cell_inner {
    @apply dark:text-gray-300;
}

.dp__cell_offset {
    @apply dark:text-gray-500;
}

.dp__menu {
    @apply border-gray-200 dark:border-gray-500 shadow-md dark:shadow-gray-600;
}

</style>
