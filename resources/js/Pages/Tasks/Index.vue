<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import CreateTaskModal from "./CreateTaskModal.vue";
import EditTaskModal from "./EditTaskModal.vue";
import Select from "@/Components/Select.vue";
import { Head, router } from "@inertiajs/vue3";
import { Plus, ChevronDown } from "lucide-vue-next";
import { ref, computed, watch } from "vue";

const props = defineProps({
    tasks: { type: Object, required: true },
    customers: { type: Array, required: true },
    users: { type: Array, required: true },
    filters: { type: Object, default: () => ({}) },
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const taskToEdit = ref(null);
const draggedTask = ref(null);
const showFilters = ref(false);

const executedByFilter = ref(props.filters.e || "");
const supervisedByFilter = ref(props.filters.s || "");

watch([executedByFilter, supervisedByFilter], () => {
    router.get(
        route("tasks.index"),
        {
            e: executedByFilter.value,
            s: supervisedByFilter.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
});

const statusColumns = [
    { key: "pending", label: "Pendente", color: "secondary" },
    { key: "waiting", label: "Aguardando", color: "primary" },
    { key: "in_progress", label: "Em Progresso", color: "primary" },
    { key: "completed", label: "Concluída", color: "success" },
    { key: "finished", label: "Finalizada", color: "success" },
];

const tasksByStatus = computed(() => {
    const grouped = {};
    statusColumns.forEach((status) => {
        grouped[status.key] = props.tasks.filter(
            (task) => task.status === status.key
        );
    });
    return grouped;
});

const openCreateModal = () => {
    showCreateModal.value = true;
};

const openEditModal = (task) => {
    taskToEdit.value = task;
    showEditModal.value = true;
};

const getPriorityColor = (priority) => {
    if (priority === "low") return "success";
    if (priority === "medium") return "warning";
    if (priority === "high") return "danger";
    return "secondary";
};

const getPriorityLabel = (priority) => {
    if (priority === "low") return "Baixa";
    if (priority === "medium") return "Média";
    if (priority === "high") return "Alta";
    return priority;
};

const queryParams = computed(() => {
    return {
        e: executedByFilter.value,
        s: supervisedByFilter.value,
    };
});

const isOverdue = (dueDate) => {
    if (!dueDate) return false;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const due = new Date(dueDate);
    return due < today;
};

const getUserName = (userId) => {
    const user = props.users.find((u) => u.id === userId);
    return user ? user.name : "";
};

const handleDragStart = (event, task) => {
    draggedTask.value = task;
    event.dataTransfer.effectAllowed = "move";
};

const handleDragOver = (event) => {
    event.preventDefault();
    event.dataTransfer.dropEffect = "move";
};

const handleDrop = (event, newStatus) => {
    event.preventDefault();
    if (draggedTask.value && draggedTask.value.status !== newStatus) {
        updateTaskStatus(draggedTask.value.id, newStatus);
    }
    draggedTask.value = null;
};

const updateTaskStatus = (taskId, newStatus) => {
    router.put(
        route("tasks.update", { task: taskId, ...queryParams.value }),
        { status: newStatus },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};
</script>

<template>
    <Head title="Tarefas" />
    <AppLayout>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Tarefas</h1>
            <button @click="openCreateModal" class="btn btn-primary">
                <Plus :size="18" class="me-1" />
                Novo
            </button>
        </div>

        <div class="card mb-3">
            <div
                class="d-flex justify-content-between align-items-center p-3"
                style="cursor: pointer"
                @click="showFilters = !showFilters"
            >
                <h6 class="mb-0">Filtros</h6>
                <ChevronDown
                    :size="18"
                    :style="{
                        transform: showFilters
                            ? 'rotate(0deg)'
                            : 'rotate(-90deg)',
                        transition: 'transform 0.2s',
                    }"
                />
            </div>

            <div v-show="showFilters" class="px-3 pb-1">
                <div class="row g-3">
                    <div class="col-md-6">
                        <Select
                            v-model="executedByFilter"
                            label="Executor"
                            :options="users"
                            placeholder="Todos"
                        />
                    </div>
                    <div class="col-md-6">
                        <Select
                            v-model="supervisedByFilter"
                            label="Supervisor"
                            :options="users"
                            placeholder="Todos"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="kanban-board">
            <div
                v-for="status in statusColumns"
                :key="status.key"
                class="kanban-column"
            >
                <div class="kanban-header" :class="`border-${status.color}`">
                    <h6 class="mb-0">
                        {{ status.label }}
                    </h6>
                    <span :class="`badge bg-${status.color}`">
                        {{ tasksByStatus[status.key].length }}
                    </span>
                </div>
                <div
                    class="kanban-tasks"
                    @dragover="handleDragOver"
                    @drop="handleDrop($event, status.key)"
                >
                    <div
                        v-for="task in tasksByStatus[status.key]"
                        :key="task.id"
                        class="kanban-task"
                        :class="{
                            'task-overdue':
                                isOverdue(task.due_date) &&
                                status.key !== 'completed' &&
                                status.key !== 'finished',
                        }"
                        draggable="true"
                        @dragstart="handleDragStart($event, task)"
                        @click="openEditModal(task)"
                    >
                        <div class="task-header mb-2">
                            <div class="task-customer fw-bold">
                                {{ task.customer?.name }}
                            </div>
                            <div class="task-priority-date">
                                <span
                                    :class="`badge badge-sm bg-${getPriorityColor(
                                        task.priority
                                    )}`"
                                >
                                    {{ getPriorityLabel(task.priority) }}
                                </span>
                            </div>
                        </div>
                        <div class="task-title mb-2">{{ task.title }}</div>
                        <div class="task-footer">
                            <div class="task-users">
                                <small class="text-muted d-block">
                                    <strong>Executor:</strong>
                                    {{ getUserName(task.executed_by) }}
                                </small>
                                <small class="text-muted d-block">
                                    <strong>Supervisor:</strong>
                                    {{ getUserName(task.supervised_by) }}
                                </small>
                            </div>
                            <div v-if="task.due_date" class="task-due-date">
                                <small
                                    :class="{
                                        'text-danger fw-bold':
                                            isOverdue(task.due_date) &&
                                            task.status !== 'completed' &&
                                            task.status !== 'finished',
                                        'text-muted':
                                            !isOverdue(task.due_date) ||
                                            task.status === 'completed' ||
                                            task.status === 'finished',
                                    }"
                                >
                                    {{ task.due_date }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

    <CreateTaskModal
        v-model:show="showCreateModal"
        :customers="customers"
        :users="users"
        :query-params="queryParams"
    />

    <EditTaskModal
        v-model:show="showEditModal"
        :task="taskToEdit"
        :customers="customers"
        :users="users"
        :query-params="queryParams"
    />
</template>

<style scoped>
.kanban-board {
    display: flex;
    min-height: calc(100vh - 150px);
    gap: 0.75rem;
    overflow-x: auto;
    padding-bottom: 1rem;
}

.kanban-column {
    min-width: 300px;
    flex: 1;
    background: #f8f9fa;
    border-radius: 0.25rem;
    border: 1px solid #e5e5e5;
    display: flex;
    flex-direction: column;
}

.kanban-header {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e5e5e5;
    background: white;
    border-radius: 0.25rem 0.25rem 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 3px solid;
}

.kanban-header.border-secondary {
    border-top-color: #6c757d;
}

.kanban-header.border-primary {
    border-top-color: #0d6efd;
}

.kanban-header.border-success {
    border-top-color: #198754;
}

.kanban-tasks {
    padding: 0.5rem;
    flex: 1;
    overflow-y: auto;
    max-height: calc(100vh - 280px);
}

.kanban-task {
    background: white;
    border-radius: 0.375rem;
    padding: 0.875rem;
    margin-bottom: 0.5rem;
    cursor: grab;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: box-shadow 0.2s, transform 0.2s;
    border: 1px solid #e5e5e5;
}

.kanban-task:active {
    cursor: grabbing;
}

.kanban-task:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.kanban-task.task-overdue {
    background-color: #fff5f5;
    border-color: #ff6b6b;
}

.task-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.task-customer {
    font-size: 0.875rem;
    color: #212529;
    flex: 1;
}

.task-priority-date {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.25rem;
}

.task-title {
    font-size: 0.875rem;
    color: #495057;
    line-height: 1.4;
}

.task-footer {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-top: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px solid #f0f0f0;
}

.task-users {
    flex: 1;
}

.task-users small {
    font-size: 0.75rem;
    line-height: 1.3;
}

.task-due-date {
    text-align: right;
}

.badge-sm {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}
</style>
