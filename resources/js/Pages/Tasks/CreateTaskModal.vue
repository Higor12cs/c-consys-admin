<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import Input from "@/Components/Input.vue";
import Select from "@/Components/Select.vue";
import TextArea from "@/Components/TextArea.vue";

const props = defineProps({
    show: { type: Boolean, default: false },
    customers: { type: Array, required: true },
    users: { type: Array, required: true },
    queryParams: { type: Object, default: () => ({}) },
});

const emit = defineEmits(["update:show"]);

const isVisible = ref(props.show);

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

watch(
    () => props.show,
    (newVal) => {
        isVisible.value = newVal;
        if (newVal) {
            document.body.classList.add("modal-open");
        } else {
            document.body.classList.remove("modal-open");
            form.reset();
        }
    }
);

const close = () => {
    emit("update:show", false);
};

const submit = () => {
    form.post(route("tasks.store", props.queryParams), {
        onSuccess: () => close(),
    });
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
                <div class="modal-dialog modal-dialog-scrollable modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Nova Tarefa</h5>
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
                                                    id="priority-low-create"
                                                />
                                                <label
                                                    class="btn btn-sm btn-outline-success"
                                                    for="priority-low-create"
                                                    >Baixa</label
                                                >
                                                <input
                                                    type="radio"
                                                    v-model="form.priority"
                                                    value="medium"
                                                    class="btn-check"
                                                    id="priority-medium-create"
                                                />
                                                <label
                                                    class="btn btn-sm btn-outline-warning"
                                                    for="priority-medium-create"
                                                    >Média</label
                                                >
                                                <input
                                                    type="radio"
                                                    v-model="form.priority"
                                                    value="high"
                                                    class="btn-check"
                                                    id="priority-high-create"
                                                />
                                                <label
                                                    class="btn btn-sm btn-outline-danger"
                                                    for="priority-high-create"
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
                                                    id="status-pending-create"
                                                />
                                                <label
                                                    class="btn btn-sm btn-outline-secondary"
                                                    for="status-pending-create"
                                                    >Pendente</label
                                                >
                                                <input
                                                    type="radio"
                                                    v-model="form.status"
                                                    value="waiting"
                                                    class="btn-check"
                                                    id="status-waiting-create"
                                                />
                                                <label
                                                    class="btn btn-sm btn-outline-primary"
                                                    for="status-waiting-create"
                                                    >Aguardando</label
                                                >
                                                <input
                                                    type="radio"
                                                    v-model="form.status"
                                                    value="in_progress"
                                                    class="btn-check"
                                                    id="status-progress-create"
                                                />
                                                <label
                                                    class="btn btn-sm btn-outline-primary"
                                                    for="status-progress-create"
                                                    >Em Progresso</label
                                                >
                                                <input
                                                    type="radio"
                                                    v-model="form.status"
                                                    value="completed"
                                                    class="btn-check"
                                                    id="status-completed-create"
                                                />
                                                <label
                                                    class="btn btn-sm btn-outline-success"
                                                    for="status-completed-create"
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
                                    Criar
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
    </Teleport>
</template>

<style scoped>
.modal-body {
    max-height: 80vh;
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
