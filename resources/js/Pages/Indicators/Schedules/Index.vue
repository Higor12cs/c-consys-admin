<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ArrowLeft, Plus } from "lucide-vue-next";
import DataTable from "datatables.net-vue3";
import DataTablesCore from "datatables.net-bs5";
import "datatables.net-bs5/css/dataTables.bootstrap5.min.css";
import { onMounted, ref } from "vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";

DataTable.use(DataTablesCore);

const props = defineProps({
    schedules: {
        type: Array,
        required: true,
    },
});

const tableRef = ref(null);

const showConfirm = ref(false);
const selectedScheduleId = ref(null);
const confirmLoading = ref(false);

const openResendConfirm = (id) => {
    selectedScheduleId.value = id;
    showConfirm.value = true;
};

const handleConfirmResend = () => {
    if (!selectedScheduleId.value) return;
    confirmLoading.value = true;
    router.post(
        route("schedules.resend", {
            schedule: selectedScheduleId.value,
        })
    );
    showConfirm.value = false;
    confirmLoading.value = false;
};
const columns = [
    { data: "time", title: "Horário", width: "35%" },
    {
        data: "is_active",
        title: "Ativo",
        render: (data) => {
            return data
                ? '<span class="badge bg-success">Ativo</span>'
                : '<span class="badge bg-secondary">Inativo</span>';
        },
        width: "35%",
    },
    {
        data: null,
        title: "Ações",
        orderable: false,
        render: (data) => {
            return `
                <button class="btn btn-sm btn-secondary edit-button" data-id="${data.id}">Editar</button>
                <button class="btn btn-sm btn-secondary schedule-resend-button" data-id="${data.id}">Reenviar</button>
            `;
        },
        width: "30%",
    },
];

onMounted(() => {
    document.addEventListener("click", (e) => {
        if (e.target.classList.contains("edit-button")) {
            const scheduleId = e.target.getAttribute("data-id");
            router.visit(route("schedules.edit", { schedule: scheduleId }));
        }

        if (e.target.classList.contains("schedule-resend-button")) {
            const scheduleId = e.target.getAttribute("data-id");
            openResendConfirm(scheduleId);
        }
    });
});
</script>

<template>
    <Head title="Agendamentos" />
    <AppLayout>
        <div class="d-flex justify-content-between mb-3">
            <h1 class="h4">Agendamentos</h1>
            <Link :href="route('schedules.create')" class="btn btn-primary">
                <Plus :size="18" class="me-1" />
                Novo
            </Link>
        </div>

        <div class="card">
            <div class="card-header">Agendamentos</div>
            <div class="card-body">
                <DataTable
                    ref="tableRef"
                    :data="schedules"
                    :columns="columns"
                    :options="{
                        language: {
                            url: 'https://cdn.datatables.net/plug-ins/2.3.4/i18n/pt-BR.json',
                        },
                    }"
                    class="table table-bordered table-striped table-hover"
                />
            </div>
        </div>
    </AppLayout>

    <ConfirmationModal
        v-model:show="showConfirm"
        title="Confirmação"
        message="Deseja reenviar as imagens deste agendamento agora?"
        confirm-text="Reenviar"
        cancel-text="Cancelar"
        confirm-variant="primary"
        @confirm="handleConfirmResend"
    />
</template>
