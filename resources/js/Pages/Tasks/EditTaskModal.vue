<script setup>
import { useForm, usePage, router } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import Input from "@/Components/Input.vue";
import Select from "@/Components/Select.vue";
import TextArea from "@/Components/TextArea.vue";

const props = defineProps({
    show: { type: Boolean, default: false },
    task: { type: Object, default: null },
    customers: { type: Array, required: true },
    users: { type: Array, required: true },
    queryParams: { type: Object, default: () => ({}) },
});

const emit = defineEmits(["update:show"]);

const page = usePage();
const isVisible = ref(props.show);
const showDeleteModal = ref(false);

const form = useForm({
    customer_id: "",
    title: "",
    description: "",
    priority: "medium",
    status: "pending",
    due_date: "",
    executed_by: "",
    supervised_by: "",
});

const isSupervisor = computed(() => {
    return props.task && props.task.supervised_by === page.props.auth.user.id;
});

const canFinish = computed(() => {
    return (
        props.task && props.task.status === "completed" && isSupervisor.value
    );
});

watch(
    () => props.show,
    (newVal) => {
        isVisible.value = newVal;
        if (newVal) {
            document.body.classList.add("modal-open");
            if (props.task) {
                form.customer_id = props.task.customer_id;
                form.title = props.task.title;
                form.description = props.task.description || "";
                form.priority = props.task.priority;
                form.status = props.task.status;
                form.due_date = props.task.due_date || "";
                form.executed_by = props.task.executed_by;
                form.supervised_by = props.task.supervised_by;
            }
        } else {
            document.body.classList.remove("modal-open");
            form.reset();
        }
    }
);

const close = () => {
    if (!showDeleteModal.value) {
        emit("update:show", false);
    }
};

const submit = () => {
    form.put(
        route("tasks.update", { task: props.task.id, ...props.queryParams }),
        {
            onSuccess: () => close(),
        }
    );
};

const openDeleteModal = () => {
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    router.delete(
        route("tasks.destroy", { task: props.task.id, ...props.queryParams }),
        {
            onSuccess: () => {
                showDeleteModal.value = false;
                close();
            },
        }
    );
};

const finishTask = () => {
    router.post(
        route("tasks.finish", { task: props.task.id, ...props.queryParams }),
        {},
        {
            onSuccess: () => close(),
        }
    );
};
</script>

<template>
    <Teleport to="body">
        <Transition name="modal">
            <div
                v-if="isVisible"
                class="modal fade show d-block"
                tabindex="-1"
                @click.self="close"
            >
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Tarefa</h5>
                            <button
                                type="button"
                                class="btn-close"
                                @click="close"
                            ></button>
                        </div>
                        <form @submit.prevent="submit">
                            <div class="modal-body">
                                <Select
                                    v-model="form.customer_id"
                                    label="Cliente"
                                    :options="customers"
                                    :error="form.errors.customer_id"
                                />

                                <Input
                                    v-model="form.title"
                                    label="Título"
                                    :error="form.errors.title"
                                />

                                <TextArea
                                    v-model="form.description"
                                    label="Descrição"
                                    :error="form.errors.description"
                                />

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label"
                                                >Prioridade</label
                                            >
                                            <div class="space-x-2" role="group">
                                                <input
                                                    type="radio"
                                                    v-model="form.priority"
                                                    value="low"
                                                    class="btn-check"
                                                    id="priority-low-edit"
                                                />
                                                <label
                                                    class="btn btn-sm btn-outline-success"
                                                    for="priority-low-edit"
                                                    >Baixa</label
                                                >
                                                <input
                                                    type="radio"
                                                    v-model="form.priority"
                                                    value="medium"
                                                    class="btn-check"
                                                    id="priority-medium-edit"
                                                />
                                                <label
                                                    class="btn btn-sm btn-outline-warning"
                                                    for="priority-medium-edit"
                                                    >Média</label
                                                >
                                                <input
                                                    type="radio"
                                                    v-model="form.priority"
                                                    value="high"
                                                    class="btn-check"
                                                    id="priority-high-edit"
                                                />
                                                <label
                                                    class="btn btn-sm btn-outline-danger"
                                                    for="priority-high-edit"
                                                    >Alta</label
                                                >
                                            </div>
                                            <div
                                                v-if="form.errors.priority"
                                                class="text-danger small mt-1"
                                            >
                                                {{ form.errors.priority }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label"
                                                >Status</label
                                            >
                                            <div class="space-x-2" role="group">
                                                <input
                                                    type="radio"
                                                    v-model="form.status"
                                                    value="pending"
                                                    class="btn-check"
                                                    id="status-pending-edit"
                                                />
                                                <label
                                                    class="btn btn-sm btn-outline-secondary"
                                                    for="status-pending-edit"
                                                    >Pendente</label
                                                >
                                                <input
                                                    type="radio"
                                                    v-model="form.status"
                                                    value="waiting"
                                                    class="btn-check"
                                                    id="status-waiting-edit"
                                                />
                                                <label
                                                    class="btn btn-sm btn-outline-primary"
                                                    for="status-waiting-edit"
                                                    >Aguardando</label
                                                >
                                                <input
                                                    type="radio"
                                                    v-model="form.status"
                                                    value="in_progress"
                                                    class="btn-check"
                                                    id="status-progress-edit"
                                                />
                                                <label
                                                    class="btn btn-sm btn-outline-primary"
                                                    for="status-progress-edit"
                                                    >Em Progresso</label
                                                >
                                                <input
                                                    type="radio"
                                                    v-model="form.status"
                                                    value="completed"
                                                    class="btn-check"
                                                    id="status-completed-edit"
                                                />
                                                <label
                                                    class="btn btn-sm btn-outline-success"
                                                    for="status-completed-edit"
                                                    >Concluída</label
                                                >
                                            </div>
                                            <div
                                                v-if="form.errors.status"
                                                class="text-danger small mt-1"
                                            >
                                                {{ form.errors.status }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <Input
                                    v-model="form.due_date"
                                    label="Data de Vencimento"
                                    type="date"
                                    :error="form.errors.due_date"
                                />

                                <div class="row">
                                    <div class="col-md-6">
                                        <Select
                                            v-model="form.executed_by"
                                            label="Executor"
                                            :options="users"
                                            :error="form.errors.executed_by"
                                        />
                                    </div>

                                    <div class="col-md-6">
                                        <Select
                                            v-model="form.supervised_by"
                                            label="Supervisor"
                                            :options="users"
                                            :error="form.errors.supervised_by"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button
                                    type="button"
                                    class="btn btn-danger me-auto"
                                    @click="openDeleteModal"
                                >
                                    Deletar
                                </button>
                                <button
                                    v-if="canFinish"
                                    type="button"
                                    class="btn btn-success"
                                    @click="finishTask"
                                >
                                    Finalizar
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    @click="close"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                    :disabled="form.processing"
                                >
                                    Salvar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Transition>
        <Transition name="backdrop">
            <div v-if="isVisible" class="modal-backdrop fade show"></div>
        </Transition>

        <ConfirmationModal
            v-model:show="showDeleteModal"
            title="Remover Tarefa"
            message="Tem certeza que deseja remover esta tarefa?"
            confirm-text="Remover"
            cancel-text="Cancelar"
            confirm-variant="danger"
            @confirm="confirmDelete"
        />
    </Teleport>
</template>

<style scoped>
.modal-body {
    max-height: 60vh;
    overflow-y: auto;
}

.modal-enter-active,
.modal-leave-active,
.backdrop-enter-active,
.backdrop-leave-active {
    transition: opacity 0.15s ease;
}

.modal-enter-from,
.modal-leave-to,
.backdrop-enter-from,
.backdrop-leave-to {
    opacity: 0;
}
</style>
