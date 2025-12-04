<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import ImageForm from "@/Pages/Indicators/Images/ImageForm.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ArrowLeft } from "lucide-vue-next";
import { ref } from "vue";

const props = defineProps({
    schedules: { type: Array, required: true },
    customers: { type: Array, required: true },
    chartTypes: { type: Array, required: true },
    destinationTypes: { type: Array, required: true },
    indicators: { type: Array, required: false, default: () => [] },
});

const processing = ref(false);
const errors = ref({});

const handleSubmit = (formData) => {
    processing.value = true;
    errors.value = {};
    router.post(route("indicators.images.store"), formData, {
        onSuccess: () => {
            processing.value = false;
        },
        onError: (err) => {
            processing.value = false;
            errors.value = err;
        },
    });
};
</script>

<template>
    <Head title="Nova Imagem" />
    <AppLayout>
        <div class="d-flex justify-content-between mb-3">
            <h1 class="h4">Nova Imagem</h1>
            <Link :href="route('indicators.images.index')" class="btn btn-secondary">
                <ArrowLeft :size="18" class="me-1" />Voltar
            </Link>
        </div>

        <ImageForm
            :customers="customers"
            :schedules="schedules"
            :chart-types="chartTypes"
            :destination-types="destinationTypes"
            :indicators="indicators"
            :processing="processing"
            :errors="errors"
            @submit="handleSubmit"
        />
    </AppLayout>
</template>
