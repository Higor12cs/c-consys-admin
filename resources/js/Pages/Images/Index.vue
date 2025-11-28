<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Plus } from "lucide-vue-next";
import DataTable from "datatables.net-vue3";
import DataTablesCore from "datatables.net-bs5";
import "datatables.net-bs5/css/dataTables.bootstrap5.min.css";
import { onMounted, ref } from "vue";

DataTable.use(DataTablesCore);

const props = defineProps({
    images: {
        type: Array,
        required: true,
    },
});

const tableRef = ref(null);
const showDeleteModal = ref(false);
const imageToDelete = ref(null);

const columns = [
    { data: "customer.name", title: "Cliente", width: "20%" },
    { data: "name", title: "Nome", width: "15%" },
    { data: "company", title: "Empresa", width: "15%" },
    {
        data: "indicators",
        title: "Indicadores",
        render: (data) => {
            return data && data.length > 0 ? data.length : "0";
        },
        width: "10%",
    },
    {
        data: "charts",
        title: "Gráficos",
        render: (data) => {
            return data && data.length > 0 ? data.length : "0";
        },
        width: "10%",
    },
    {
        data: "is_active",
        title: "Status",
        render: (data) => {
            return data
                ? '<span class="badge bg-success">Ativo</span>'
                : '<span class="badge bg-secondary">Inativo</span>';
        },
        width: "10%",
    },
    {
        data: null,
        title: "Ações",
        orderable: false,
        render: (data) => {
            return `
                <button class="btn btn-sm btn-secondary edit-button" data-id="${data.id}">Editar</button>
                <button class="btn btn-sm btn-danger delete-button" data-id="${data.id}">Excluir</button>
            `;
        },
        width: "20%",
    },
];

const openDeleteModal = (customerId) => {
    imageToDelete.value = customerId;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    if (imageToDelete.value) {
        router.delete(
            route("images.destroy", {
                image: imageToDelete.value,
            })
        );
        imageToDelete.value = null;
    }
};

onMounted(() => {
    document.addEventListener("click", (e) => {
        if (e.target.classList.contains("edit-button")) {
            const imageId = e.target.getAttribute("data-id");
            router.visit(
                route("images.edit", {
                    image: imageId,
                })
            );
        }

        if (e.target.classList.contains("delete-button")) {
            const imageId = e.target.getAttribute("data-id");
            openDeleteModal(imageId);
        }
    });
});
</script>

<template>
    <Head title="Imagens" />
    <AppLayout>
        <div class="d-flex justify-content-between mb-3">
            <h1 class="h4">Imagens</h1>
            <div>
                <Link :href="route('images.create')" class="btn btn-primary">
                    <Plus :size="18" class="me-1" />
                    Novo
                </Link>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Imagens</div>
            <div class="card-body">
                <DataTable
                    ref="tableRef"
                    :data="images"
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

        <ConfirmationModal
            v-model:show="showDeleteModal"
            title="Remover Imagem"
            message="Tem certeza que deseja remover esta imagem?"
            confirm-text="Remover"
            cancel-text="Cancelar"
            confirm-variant="danger"
            @confirm="confirmDelete"
        />
    </AppLayout>
</template>
