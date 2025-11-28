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
    users: { type: Array, required: true },
});

const tableRef = ref(null);
const showDeleteModal = ref(false);
const userToDelete = ref(null);

const openDeleteModal = (userId) => {
    userToDelete.value = userId;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    if (userToDelete.value) {
        router.delete(route("users.destroy", { user: userToDelete.value }));
        userToDelete.value = null;
    }
};

const columns = [
    { data: "name", title: "Nome", width: "40%" },
    { data: "email", title: "E-mail", width: "40%" },
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
            const userId = el.getAttribute("data-id");
            router.visit(route("users.edit", { user: userId }));
        }

        if (el.classList.contains("delete-btn")) {
            const userId = el.getAttribute("data-id");
            openDeleteModal(userId);
        }
    });
});
</script>

<template>
    <Head title="Usuários" />
    <AppLayout>
        <div class="d-flex justify-content-between mb-3">
            <h1 class="h4">Usuários</h1>
            <Link :href="route('users.create')" class="btn btn-primary">
                <Plus :size="18" class="me-1" />
                Novo
            </Link>
        </div>

        <div class="card">
            <div class="card-header">Usuários</div>
            <div class="card-body">
                <DataTable
                    ref="tableRef"
                    :data="users"
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
        v-model:show="showDeleteModal"
        title="Remover Usuário"
        message="Tem certeza que deseja remover este usuário?"
        confirm-text="Remover"
        cancel-text="Cancelar"
        confirm-variant="danger"
        @confirm="confirmDelete"
    />
</template>
