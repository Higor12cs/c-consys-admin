<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Plus } from "lucide-vue-next";
import DataTable from "datatables.net-vue3";
import DataTablesCore from "datatables.net-bs5";
import "datatables.net-bs5/css/dataTables.bootstrap5.min.css";
import { onMounted, ref } from "vue";

DataTable.use(DataTablesCore);

const props = defineProps({
    customers: {
        type: Array,
        required: true,
    },
});

const tableRef = ref(null);

const columns = [
    { data: "name", title: "Nome", width: "50%" },
    { data: "external_id", title: "Código Externo", width: "20%" },
    {
        data: "is_active",
        title: "Ativo",
        render: (data) => {
            return data
                ? '<span class="badge bg-success">Ativo</span>'
                : '<span class="badge bg-secondary">Inativo</span>';
        },
        width: "15%",
    },
    {
        data: null,
        title: "Ações",
        orderable: false,
        render: (data) => {
            return `<button class="btn btn-sm btn-secondary edit-btn" data-id="${data.id}">Editar</button>`;
        },
        width: "15%",
    },
];

onMounted(() => {
    document.addEventListener("click", (e) => {
        if (e.target.classList.contains("edit-btn")) {
            const customerId = e.target.getAttribute("data-id");
            router.visit(route("customers.edit", { customer: customerId }));
        }
    });
});
</script>

<template>
    <Head title="Clientes" />
    <AppLayout>
        <div class="d-flex justify-content-between mb-3">
            <h1 class="h4">Clientes</h1>
            <Link :href="route('customers.create')" class="btn btn-primary">
                <Plus :size="18" class="me-1" />
                Novo
            </Link>
        </div>

        <div class="card">
            <div class="card-header">Clientes</div>
            <div class="card-body">
                <DataTable
                    ref="tableRef"
                    :data="customers"
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
    </AppLayout>
</template>
