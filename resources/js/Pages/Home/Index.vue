<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head } from "@inertiajs/vue3";
import { LayoutDashboard, Users, Clock, Bell } from "lucide-vue-next";

const props = defineProps({
    customersCount: { type: Number, required: false, default: 0 },
    imagesCount: { type: Number, required: false, default: 0 },
    schedulesCount: { type: Number, required: false, default: 0 },
    unreadNotificationsCount: { type: Number, required: false, default: 0 },
    imagesSentToday: { type: Number, required: false, default: 0 },
    imagesSentYesterday: { type: Number, required: false, default: 0 },
    notifications: { type: Array, required: false, default: () => [] },
    recentExecutionLogs: { type: Array, required: false, default: () => [] },
});

function formatDateTime(dateTimeString) {
    if (!dateTimeString) return "-";

    const [datePart, timePart] = dateTimeString.split(" ");
    const [year, month, day] = datePart.split("-");

    return `${day}/${month}/${year} ${timePart || ""}`.trim();
}

function formatDate(dateString) {
    if (!dateString) return "-";
    const [year, month, day] = dateString.split("-");
    return `${day}/${month}/${year}`;
}
</script>

<template>
    <Head title="Início" />
    <AppLayout>
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h4 mb-0">Início</h1>
        </div>

        <div class="row mt-3">
            <div class="col-lg-6 mb-4">
                <div class="card border-left-primary h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col me-2">
                                <div
                                    class="text-xs fw-bold text-uppercase mb-1"
                                >
                                    Imagens Enviadas (Hoje)
                                </div>
                                <div class="h5 mb-0 fw-bold">
                                    {{ imagesSentToday }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <LayoutDashboard
                                    :size="32"
                                    class="text-secondary"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card border-left-primary h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col me-2">
                                <div
                                    class="text-xs fw-bold text-uppercase mb-1"
                                >
                                    Imagens Enviadas (Ontem)
                                </div>
                                <div class="h5 mb-0 fw-bold">
                                    {{ imagesSentYesterday }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <LayoutDashboard
                                    :size="32"
                                    class="text-secondary"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col me-2">
                                <div
                                    class="text-xs fw-bold text-uppercase mb-1"
                                >
                                    Clientes
                                </div>
                                <div class="h5 mb-0 fw-bold">
                                    {{ customersCount }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <Users :size="32" class="text-secondary" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col me-2">
                                <div
                                    class="text-xs fw-bold text-uppercase mb-1"
                                >
                                    Imagens
                                </div>
                                <div class="h5 mb-0 fw-bold">
                                    {{ imagesCount }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <LayoutDashboard
                                    :size="32"
                                    class="text-secondary"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col me-2">
                                <div
                                    class="text-xs fw-bold text-uppercase mb-1"
                                >
                                    Agendamentos
                                </div>
                                <div class="h5 mb-0 fw-bold">
                                    {{ schedulesCount }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <Clock :size="32" class="text-secondary" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col me-2">
                                <div
                                    class="text-xs fw-bold text-uppercase mb-1"
                                >
                                    Notificações
                                </div>
                                <div class="h5 mb-0 fw-bold">
                                    {{ unreadNotificationsCount }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <Bell :size="32" class="text-secondary" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold">Notificações Recentes</h6>
                    </div>
                    <div class="card-body">
                        <div
                            v-if="notifications.length === 0"
                            class="text-center text-muted"
                        >
                            Nenhuma notificação recente.
                        </div>
                        <div v-else>
                            <div
                                v-for="note in notifications.slice(0, 5)"
                                :key="note.id"
                                class="mb-3"
                            >
                                <strong>{{ note.title }}</strong>
                                <div class="small text-muted">
                                    {{ note.message }}
                                </div>
                                <div class="small text-muted">
                                    {{ formatDateTime(note.created_at) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold">
                            Últimos Agendamentos Executados
                        </h6>
                    </div>
                    <div class="card-body">
                        <div
                            v-if="recentExecutionLogs.length === 0"
                            class="text-center text-muted"
                        >
                            Nenhum registro de execução recente.
                        </div>
                        <div v-else>
                            <ul class="list-group">
                                <li
                                    v-for="log in recentExecutionLogs"
                                    :key="log.id"
                                    class="list-group-item"
                                >
                                    <div
                                        class="d-flex w-100 justify-content-between align-items-start"
                                    >
                                        <div>
                                            <div class="fw-bold">
                                                #{{ log.id }} — Agendamento:
                                                {{ log.schedule_id ?? "-" }} [{{
                                                    log.schedule_time
                                                }}]
                                            </div>
                                            <div class="small text-muted">
                                                Horário do Agendamento:
                                                {{ log.schedule_time ?? "-" }}
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="small text-muted">
                                                {{ formatDateTime(log.execution_date) ?? "-" }}
                                            </div>
                                            <div class="small">
                                                {{ log.execution_time ?? "-" }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-2">
                                        <span
                                            v-if="log.status === 'success'"
                                            class="badge bg-success me-2"
                                        >
                                            Sucesso
                                        </span>
                                        <span
                                            v-else-if="log.status === 'failed'"
                                            class="badge bg-danger me-2"
                                        >
                                            Falha
                                        </span>
                                        <span
                                            v-else
                                            class="badge bg-secondary me-2"
                                        >
                                            {{ log.status }}
                                        </span>

                                        <div
                                            v-if="log.error_message"
                                            class="mt-2 text-danger small"
                                        >
                                            {{ log.error_message }}
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
