<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head } from "@inertiajs/vue3";
import MenuLink from "@/Components/MenuLink.vue";
import { User, Calendar, Image } from "lucide-vue-next";
</script>

<template>
    <Head title="Indicadores" />
    <AppLayout>
        <div class="d-flex justify-content-between mb-3">
            <h1 class="h4">Indicadores</h1>
        </div>

        <div class="row g-4">
            <div class="col-12 col-md-6 col-lg-4">
                <MenuLink
                    :href="route('indicators.index')"
                    :title="'Indicadores'"
                    :description="'Gerencie os indicadores cadastrados.'"
                    :icon="User"
                    aria-label="Indicadores"
                />
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <MenuLink
                    :href="route('schedules.index')"
                    :title="'Agendamentos'"
                    :description="'Gerencie os horários de envio das imagens.'"
                    :icon="Calendar"
                    aria-label="Agendamentos"
                />
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <MenuLink
                    :href="route('images.index')"
                    :title="'Imagens'"
                    :description="'Gerencie as imagens enviadas pelo WhatsApp.'"
                    :icon="Image"
                    aria-label="Imagens"
                />
            </div>
        </div>
    </AppLayout>
</template>
