<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ArrowLeft, Plus } from "lucide-vue-next";
import DataTable from "datatables.net-vue3";
import DataTablesCore from "datatables.net-bs5";
import "datatables.net-bs5/css/dataTables.bootstrap5.min.css";
import { onMounted, ref } from "vue";

DataTable.use(DataTablesCore);

const props = defineProps({
    indicators: { type: Object, required: true },
});

const tableRef = ref(null);
const showDeleteModal = ref(false);
const indicatorToDelete = ref(null);

const openDeleteModal = (id) => {
    indicatorToDelete.value = id;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    if (indicatorToDelete.value) {
        router.delete(
            route("indicators.destroy", {
                indicator: indicatorToDelete.value,
            })
        );
        indicatorToDelete.value = null;
    }
};

const columns = [
    { data: "code", title: "Código", width: "10%" },
    { data: "description", title: "Descrição", width: "60%" },
    {
        data: "is_percentage",
        title: "Porcentagem",
        render: (data) =>
            data
                ? '<span class="badge bg-primary">Sim</span>'
                : '<span class="badge bg-secondary">Não</span>',
        width: "10%",
    },
    {
        data: null,
        title: "Ações",
        orderable: false,
        render: (data) => {
            return `<button class="btn btn-sm btn-secondary edit-btn" data-id="${data.id}">Editar</button> <button class="btn btn-sm btn-danger delete-btn" data-id="${data.id}">Remover</button>`;
        },
        width: "20%",
    },
];

onMounted(() => {
    document.addEventListener("click", (e) => {
        const el = e.target;
        if (el.classList.contains("edit-btn")) {
            const id = el.getAttribute("data-id");
            router.visit(route("indicators.edit", { indicator: id }));
        }

        if (el.classList.contains("delete-btn")) {
            const id = el.getAttribute("data-id");
            openDeleteModal(id);
        }
    });
});
</script>

<template>
    <Head title="Indicadores" />
    <AppLayout>
        <div class="d-flex justify-content-between mb-3">
            <h1 class="h4">Indicadores</h1>
            <Link :href="route('indicators.create')" class="btn btn-primary">
                <Plus :size="18" class="me-1" />
                Novo
            </Link>
        </div>

        <div class="card">
            <div class="card-header">Indicadores</div>
            <div class="card-body">
                <DataTable
                    ref="tableRef"
                    :data="indicators"
                    :columns="columns"
                    :options="{
                        pageLength: 100,
                        lengthMenu: [10, 25, 50, 100],
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
        v-model:show="showDeleteModal"
        title="Remover Indicador"
        message="Tem certeza que deseja remover este indicador?"
        confirm-text="Remover"
        cancel-text="Cancelar"
        confirm-variant="danger"
        @confirm="confirmDelete"
    />
</template>
