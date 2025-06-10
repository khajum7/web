<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import AppButton from "@/Components/AppButton.vue";

const props = defineProps({
    sale: Object,
});

const form = useForm({
    note: props.sale?.notes,
});

const emit = defineEmits(["reloadLogs"]);

const reloadLogs = () => {
    emit("reloadLogs");
};
const handleSubmit = async () => {
    form.put(route("sales.update.note", [props.sale.id]), {
        onSuccess: () => {
            reloadLogs();
        },
    });
};
</script>

<template>
    <div class="max-h-[500px] overflow-y-auto scrollbar-hide mt-8 px-6">
        <form @submit.prevent="handleSubmit">
            <!-- <div
                class="w-full mb-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600"
            > -->
              <div
                class="w-full mb-4 border border-gray-200 rounded-lg bg-gray-50 0"
            >
                <div class="p-4  bg-white rounded-t-lg">
                    <label for="comment" class="sr-only"
                        >Your Order Notes</label
                    >
                    <textarea
                        id="comment"
                        v-model="form.note"
                        rows="4"
                        class="w-full  text-gray-900 bg-white border-0 "
                        placeholder="Write a order notes..."
                        required
                    >
                    </textarea>
                </div>
                <div
                    class="flex items-center justify-end px-4 py-2 border-t "
                >
                    <AppButton type="submit"> Post Order Notes </AppButton>
                </div>
            </div>
        </form>
    </div>
</template>

<style scoped></style>
