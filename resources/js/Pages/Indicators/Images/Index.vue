<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ArrowLeft, Plus } from "lucide-vue-next";
import DataTable from "datatables.net-vue3";
import DataTablesCore from "datatables.net-bs5";
import "datatables.net-bs5/css/dataTables.bootstrap5.min.css";
import "datatables.net-responsive";
import { onMounted, ref } from "vue";

DataTable.use(DataTablesCore);

const props = defineProps({
    images: {
        type: Array,
        required: true,
    },
});

const columns = [
    { data: "customer.name", title: "Cliente", width: "10%" },
    { data: "company", title: "Empresa", width: "10%" },
    { data: "name", title: "Imagem", width: "10%" },
    {
        data: "schedules",
        title: "Agendamentos",
        render: (data) => {
            return data && data.length > 0 ? data.length : "0";
        },
        width: "10%",
    },
    {
        data: "destinations",
        title: "Destinatários",
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
                <button class="btn btn-sm btn-secondary preview-button" data-id="${data.id}">Visualizar</button>
                <button class="btn btn-sm btn-primary resend-button" data-id="${data.id}">Reenviar</button>
                <button class="btn btn-sm btn-danger delete-button" data-id="${data.id}">Excluir</button>
            `;
        },
        width: "30%",
    },
];

const showDeleteModal = ref(false);
const showResendModal = ref(false);
const imageToResend = ref(null);
const imageToDelete = ref(null);

const openDeleteModal = (customerId) => {
    imageToDelete.value = customerId;
    showDeleteModal.value = true;
};

const openResendModal = (imageId) => {
    imageToResend.value = imageId;
    showResendModal.value = true;
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

const confirmResend = () => {
    if (imageToResend.value) {
        router.post(
            route("images.resend", {
                image: imageToResend.value,
            })
        );
        imageToResend.value = null;
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

        if (e.target.classList.contains("preview-button")) {
            const imageId = e.target.getAttribute("data-id");
            router.visit(
                route("images.preview", {
                    image: imageId,
                })
            );
        }

        if (e.target.classList.contains("resend-button")) {
            const imageId = e.target.getAttribute("data-id");
            openResendModal(imageId);
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
            <Link :href="route('images.create')" class="btn btn-primary">
                <Plus :size="18" class="me-1" />
                Novo
            </Link>
        </div>

        <div class="card">
            <div class="card-header">Imagens</div>
            <div class="card-body">
                <DataTable
                    ref="tableRef"
                    :data="images"
                    :columns="columns"
                    :options="{
                        pageLength: 100,
                        lengthMenu: [10, 25, 50, 100],
                        language: {
                            url: 'https://cdn.datatables.net/plug-ins/2.3.4/i18n/pt-BR.json',
                        },
                        responsive: true,
                        columnDefs: [
                            { responsivePriority: 1, targets: 0 },
                            { responsivePriority: 2, targets: -1 },
                        ],
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

        <ConfirmationModal
            v-model:show="showResendModal"
            title="Reenviar Imagem"
            message="Tem certeza que deseja reenviar esta imagem?"
            confirm-text="Reenviar"
            cancel-text="Cancelar"
            confirm-variant="primary"
            @confirm="confirmResend"
        />
    </AppLayout>
</template>
