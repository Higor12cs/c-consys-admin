<script setup>
import { Plus, Save, GripVertical } from "lucide-vue-next";
import { ref, onMounted } from "vue";
import { toast } from "vue3-toastify";
import draggable from "vuedraggable";
import GroupListModal from "@/Pages/Images/GroupListModal.vue";
import GroupCreateModal from "@/Pages/Images/GroupCreateModal.vue";

const props = defineProps({
    image: {
        type: Object,
        default: null,
    },
    customers: {
        type: Array,
        required: true,
    },
    schedules: {
        type: Array,
        required: true,
    },
    chartTypes: {
        type: Array,
        required: true,
    },
    destinationTypes: {
        type: Array,
        required: true,
    },
    indicators: {
        type: Array,
        required: false,
        default: () => [],
    },
    processing: {
        type: Boolean,
        default: false,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(["submit"]);

const formData = ref({
    customer_id: props.image?.customer_id || "",
    name: props.image?.name || "",
    company: props.image?.company || "",
    indicators: props.image?.indicators || [],
    charts: props.image?.charts || [],
    destinations: props.image?.destinations || [],
    schedules: [],
    is_active: props.image ? (props.image.is_active ? 1 : 0) : 1,
});

const grid = ref([
    [
        { type: null, value: "" },
        { type: null, value: "" },
        { type: null, value: "" },
        { type: null, value: "" },
    ],
]);

const newDestination = ref({ type: "contact", value: "" });
const newSchedule = ref({
    schedule_id: "",
    sun: false,
    mon: true,
    tue: true,
    wed: true,
    thu: true,
    fri: true,
    sat: false,
});

const groupsMap = ref({});

const fetchGroups = async () => {
    try {
        const res = await fetch("/whatsapp/groups");
        if (!res.ok) return;
        const data = await res.json();
        if (!Array.isArray(data)) return;
        const map = {};
        data.forEach((g) => {
            const id = g.groupId || g.groupMetadata?.id || g.id || null;
            const name =
                g.groupMetadata?.subject ||
                g.groupMetadata?.desc ||
                g.name ||
                g.groupName ||
                "";
            if (id) map[id] = name || id;
        });
        groupsMap.value = map;
    } catch (e) {
        toast.info(
            "Não foi possível buscar o nome dos grupos na API do WhatsApp."
        );
    }
};

onMounted(() => {
    fetchGroups();
});

const initializeGrid = () => {
    if (!props.image) return;

    if (props.image.schedules) {
        formData.value.schedules = props.image.schedules.map((s) => ({
            schedule_id: s.id,
            sun: s.pivot.sun || false,
            mon: s.pivot.mon || false,
            tue: s.pivot.tue || false,
            wed: s.pivot.wed || false,
            thu: s.pivot.thu || false,
            fri: s.pivot.fri || false,
            sat: s.pivot.sat || false,
        }));
    }

    let maxRow = 0;
    if (props.image.indicators && props.image.indicators.length > 0) {
        props.image.indicators.forEach((indicator) => {
            const row = Math.floor(indicator.position / 4);
            maxRow = Math.max(maxRow, row);
        });
    }

    if (props.image.charts && props.image.charts.length > 0) {
        props.image.charts.forEach((chart) => {
            maxRow = Math.max(maxRow, chart.row);
        });
    }

    while (grid.value.length <= maxRow) {
        grid.value.push([
            { type: null, value: "" },
            { type: null, value: "" },
            { type: null, value: "" },
            { type: null, value: "" },
        ]);
    }

    if (props.image.indicators) {
        props.image.indicators.forEach((indicator) => {
            const row = Math.floor(indicator.position / 4);
            const col = indicator.position % 4;
            if (grid.value[row]) {
                grid.value[row][col] = {
                    type: "indicator",
                    value: indicator.code,
                };
            }
        });
    }

    if (props.image.charts) {
        props.image.charts.forEach((chart) => {
            if (grid.value[chart.row]) {
                grid.value[chart.row][chart.col] = {
                    type: "chart",
                    value: chart.type,
                };
                if (chart.col < 3) {
                    grid.value[chart.row][chart.col + 1] = {
                        type: "chart-span",
                        value: chart.type,
                    };
                }
            }
        });
    }
};

initializeGrid();

const updateFormFromGrid = () => {
    formData.value.indicators = [];
    formData.value.charts = [];

    grid.value.forEach((row, rowIndex) => {
        row.forEach((cell, colIndex) => {
            const position = rowIndex * 4 + colIndex;

            if (cell.type === "indicator" && cell.value) {
                formData.value.indicators.push({
                    code: cell.value,
                    position: position,
                });
            } else if (cell.type === "chart" && cell.value) {
                const existingChart = formData.value.charts.find(
                    (c) => c.row === rowIndex && c.col === colIndex
                );
                if (!existingChart) {
                    formData.value.charts.push({
                        type: cell.value,
                        row: rowIndex,
                        col: colIndex,
                    });
                }
            }
        });
    });
};

const clearCell = (rowIndex, colIndex) => {
    grid.value[rowIndex][colIndex] = { type: null, value: "" };

    if (colIndex < 3) {
        const nextCell = grid.value[rowIndex][colIndex + 1];
        if (nextCell.type === "chart-span") {
            nextCell.type = null;
            nextCell.value = "";
        }
    }

    updateFormFromGrid();
};

const handleCellTypeChange = (rowIndex, colIndex) => {
    const cell = grid.value[rowIndex][colIndex];
    cell.value = "";

    if (colIndex < 3) {
        const nextCell = grid.value[rowIndex][colIndex + 1];
        if (nextCell.type === "chart-span") {
            nextCell.type = null;
            nextCell.value = "";
        }
    }

    updateFormFromGrid();
};

const handleCellValueChange = (rowIndex, colIndex) => {
    const cell = grid.value[rowIndex][colIndex];

    if (cell.type === "chart" && cell.value) {
        if (colIndex % 2 === 0 && colIndex < 3) {
            const nextCell = grid.value[rowIndex][colIndex + 1];

            if (nextCell.type && nextCell.type !== "chart-span") {
                toast.error(
                    "Não há espaço para o gráfico! A próxima célula está ocupada."
                );
                cell.value = "";
                return;
            }

            nextCell.type = "chart-span";
            nextCell.value = cell.value;
        }
    }

    updateFormFromGrid();
};

const isCellDisabled = (rowIndex, colIndex) => {
    if (colIndex > 0) {
        const prevCell = grid.value[rowIndex][colIndex - 1];
        if (prevCell.type === "chart" && prevCell.value) {
            return true;
        }
    }
    return false;
};

const getCellSpan = (rowIndex, colIndex) => {
    const cell = grid.value[rowIndex][colIndex];
    if (cell.type === "chart" && cell.value) {
        return 2;
    }
    return 1;
};

const shouldShowCell = (rowIndex, colIndex) => {
    if (colIndex > 0) {
        const prevCell = grid.value[rowIndex][colIndex - 1];
        if (prevCell.type === "chart" && prevCell.value) {
            return false;
        }
    }
    return true;
};

const addRow = () => {
    grid.value.push([
        { type: null, value: "" },
        { type: null, value: "" },
        { type: null, value: "" },
        { type: null, value: "" },
    ]);
};

const removeRow = (rowIndex) => {
    if (grid.value.length > 1) {
        grid.value.splice(rowIndex, 1);
        updateFormFromGrid();
    }
};

const onRowDragEnd = () => {
    updateFormFromGrid();
};

const addDestination = () => {
    if (!newDestination.value.type) {
        toast.error("Selecione o tipo de destinatário!");
        return;
    }
    if (!newDestination.value.value) {
        toast.error("Informe o valor do destinatário!");
        return;
    }
    let dest = { ...newDestination.value };
    if (typeof dest.value === "string" && dest.value.startsWith("+")) {
        dest.value = dest.value.slice(1);
    }
    formData.value.destinations.push(dest);
    newDestination.value = { type: "contact", value: "" };
};

const addSchedule = () => {
    if (!newSchedule.value.schedule_id) {
        toast.error("Selecione um horário de agendamento!");
        return;
    }

    if (
        formData.value.schedules.find(
            (s) => s.schedule_id === newSchedule.value.schedule_id
        )
    ) {
        toast.error("Este agendamento já foi adicionado!");
        return;
    }

    formData.value.schedules.push({ ...newSchedule.value });
    newSchedule.value = {
        schedule_id: "",
        sun: false,
        mon: true,
        tue: true,
        wed: true,
        thu: true,
        fri: true,
        sat: false,
    };
};

const removeDestination = (index) => {
    formData.value.destinations.splice(index, 1);
};

const removeSchedule = (index) => {
    formData.value.schedules.splice(index, 1);
};

const getChartTypeLabel = (value) => {
    return props.chartTypes.find((t) => t.value === value)?.label || value;
};

const getIndicatorLabel = (value) => {
    return props.indicators.find((i) => i.value === value)?.label || value;
};

const getDestinationTypeLabel = (value) => {
    return (
        props.destinationTypes.find((t) => t.value === value)?.label || value
    );
};

const getScheduleTime = (id) => {
    return props.schedules.find((s) => s.id === id)?.time || "";
};

const showGroupListModal = ref(false);
const showGroupCreateModal = ref(false);

const openGroupListModal = () => {
    showGroupListModal.value = true;
};

const onGroupSelect = (g) => {
    const id = g.id || g.groupId || g.groupMetadata?.id || null;
    const name =
        g.groupMetadata?.subject ||
        g.groupMetadata?.desc ||
        g.groupName ||
        g.name ||
        "";
    if (id) {
        formData.value.destinations.push({ type: "group", value: id });
        groupsMap.value = { ...groupsMap.value, [id]: name || id };
        if (name) {
            toast.success(`Grupo "${name}" adicionado!`);
        }
    }
    showGroupListModal.value = false;
};

const onGroupCreated = (data) => {
    const id = data.groupId || data.groupMetadata?.id || data.id || null;
    const name =
        data.groupMetadata?.subject ||
        data.groupMetadata?.desc ||
        data.groupName ||
        data.name ||
        "";
    if (id) {
        formData.value.destinations.push({ type: "group", value: id });
        groupsMap.value = { ...groupsMap.value, [id]: name || id };
    }
    if (name && id) {
        toast.success(`Grupo "${name}" adicionado!`);
    }
};

const handleSubmit = () => {
    updateFormFromGrid();
    emit("submit", formData.value);
};
</script>

<template>
    <form @submit.prevent="handleSubmit">
        <!-- Image Details -->
        <div class="card mb-3">
            <div class="card-header">Detalhes da Imagem</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="customer_id" class="form-label"
                                >Cliente</label
                            >
                            <select
                                v-model="formData.customer_id"
                                class="form-select"
                                :class="{ 'is-invalid': errors.customer_id }"
                                id="customer_id"
                            >
                                <option value="">-</option>
                                <option
                                    v-for="customer in customers"
                                    :key="customer.id"
                                    :value="customer.id"
                                >
                                    {{ customer.name }}
                                </option>
                            </select>
                            <div
                                v-if="errors.customer_id"
                                class="invalid-feedback"
                            >
                                {{ errors.customer_id }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome</label>
                            <input
                                v-model="formData.name"
                                type="text"
                                class="form-control"
                                :class="{ 'is-invalid': errors.name }"
                                id="name"
                                placeholder="Nome da imagem"
                            />
                            <div v-if="errors.name" class="invalid-feedback">
                                {{ errors.name }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="company" class="form-label"
                                >Empresa</label
                            >
                            <input
                                v-model="formData.company"
                                type="text"
                                class="form-control"
                                :class="{ 'is-invalid': errors.company }"
                                id="company"
                                placeholder="Nome da empresa"
                            />
                            <div v-if="errors.company" class="invalid-feedback">
                                {{ errors.company }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="is_active"
                                >Ativo</label
                            >
                            <select
                                v-model="formData.is_active"
                                class="form-select"
                                :class="{ 'is-invalid': errors.is_active }"
                                id="is_active"
                            >
                                <option :value="1">Sim</option>
                                <option :value="0">Não</option>
                            </select>
                            <div
                                v-if="errors.is_active"
                                class="invalid-feedback"
                            >
                                {{ errors.is_active }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Layout -->
        <div class="card mb-3">
            <div class="card-header">Layout da Imagem (Grid Nx4)</div>
            <div class="card-body">
                <div v-if="image == null" class="alert alert-info">
                    <strong>Sobre o Layout:</strong> O layout é formado por um
                    grid de N linhas e quatro colunas. Cada célula do grid pode
                    conter um indicador.
                    <strong>Gráficos ocupam duas colunas</strong> e só podem
                    começar nas <strong>posições 1 ou 3</strong> (expandindo
                    para as posições 2 ou 4). Ao selecionar o tipo do gráfico,
                    ele expandirá automaticamente para ocupar duas colunas.
                    <br /><br />
                    <strong>Dica:</strong> Você pode arrastar o ícone
                    <GripVertical :size="14" class="d-inline" /> na lateral
                    esquerda para reordenar linhas inteiras.
                </div>
                <div class="table-responsive mb-4">
                    <table
                        class="table table-bordered"
                        style="table-layout: fixed; width: 100%"
                    >
                        <colgroup>
                            <col style="width: 80px" />
                            <col style="width: calc((100% - 60px) / 4)" />
                            <col style="width: calc((100% - 60px) / 4)" />
                            <col style="width: calc((100% - 60px) / 4)" />
                            <col style="width: calc((100% - 60px) / 4)" />
                        </colgroup>
                        <draggable
                            v-model="grid"
                            tag="tbody"
                            item-key="index"
                            handle=".drag-handle"
                            @end="onRowDragEnd"
                        >
                            <template #item="{ element: row, index: rowIndex }">
                                <tr>
                                    <td
                                        class="align-middle text-center bg-light drag-handle"
                                        style="width: 60px; cursor: move"
                                    >
                                        <div
                                            class="d-flex flex-column gap-1 align-items-center"
                                        >
                                            <GripVertical
                                                :size="20"
                                                class="text-muted"
                                            />
                                            <small class="text-muted fw-bold">
                                                [{{ rowIndex + 1 }}]
                                            </small>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-secondary"
                                                @click="removeRow(rowIndex)"
                                                title="Remover linha"
                                                :disabled="grid.length <= 1"
                                            >
                                                Excluir
                                            </button>
                                        </div>
                                    </td>
                                    <template
                                        v-for="(cell, colIndex) in row"
                                        :key="colIndex"
                                    >
                                        <td
                                            v-if="
                                                shouldShowCell(
                                                    rowIndex,
                                                    colIndex
                                                )
                                            "
                                            :colspan="
                                                getCellSpan(rowIndex, colIndex)
                                            "
                                            class="p-3"
                                        >
                                            <div
                                                class="d-flex justify-content-between align-items-start mb-2"
                                            >
                                                <small class="text-muted">
                                                    Posição [{{
                                                        rowIndex * 4 +
                                                        colIndex +
                                                        1
                                                    }}]
                                                </small>
                                                <button
                                                    v-if="cell.type"
                                                    type="button"
                                                    class="btn btn-sm btn-secondary"
                                                    @click="
                                                        clearCell(
                                                            rowIndex,
                                                            colIndex
                                                        )
                                                    "
                                                >
                                                    Excluir
                                                </button>
                                            </div>

                                            <div
                                                v-if="
                                                    isCellDisabled(
                                                        rowIndex,
                                                        colIndex
                                                    )
                                                "
                                                class="text-center text-muted py-3"
                                            >
                                                <small
                                                    >Ocupado pelo gráfico
                                                    anterior</small
                                                >
                                            </div>

                                            <div v-else>
                                                <select
                                                    v-model="cell.type"
                                                    class="form-select form-select-sm mb-2"
                                                    @change="
                                                        handleCellTypeChange(
                                                            rowIndex,
                                                            colIndex
                                                        )
                                                    "
                                                >
                                                    <option :value="null">
                                                        -
                                                    </option>
                                                    <option value="indicator">
                                                        Indicador
                                                    </option>
                                                    <option
                                                        value="chart"
                                                        :disabled="
                                                            colIndex % 2 !== 0
                                                        "
                                                    >
                                                        Gráfico
                                                    </option>
                                                </select>

                                                <select
                                                    v-if="
                                                        cell.type ===
                                                        'indicator'
                                                    "
                                                    v-model="cell.value"
                                                    class="form-select form-select-sm"
                                                    @change="
                                                        handleCellValueChange(
                                                            rowIndex,
                                                            colIndex
                                                        )
                                                    "
                                                >
                                                    <option value="">-</option>
                                                    <option
                                                        v-for="opt in indicators"
                                                        :key="opt.value"
                                                        :value="opt.value"
                                                    >
                                                        {{ opt.label }}
                                                    </option>
                                                </select>

                                                <select
                                                    v-else-if="
                                                        cell.type === 'chart'
                                                    "
                                                    v-model="cell.value"
                                                    class="form-select form-select-sm"
                                                    @change="
                                                        handleCellValueChange(
                                                            rowIndex,
                                                            colIndex
                                                        )
                                                    "
                                                >
                                                    <option value="">-</option>
                                                    <option
                                                        v-for="type in chartTypes"
                                                        :key="type.value"
                                                        :value="type.value"
                                                    >
                                                        {{ type.label }}
                                                    </option>
                                                </select>

                                                <div
                                                    v-if="
                                                        cell.type ===
                                                            'indicator' &&
                                                        cell.value
                                                    "
                                                    class="mt-2 text-center"
                                                >
                                                    <span
                                                        class="badge bg-primary"
                                                        >{{
                                                            getIndicatorLabel(
                                                                cell.value
                                                            )
                                                        }}</span
                                                    >
                                                </div>

                                                <div
                                                    v-if="
                                                        cell.type === 'chart' &&
                                                        cell.value
                                                    "
                                                    class="mt-2 text-center"
                                                >
                                                    <span
                                                        class="badge bg-primary"
                                                    >
                                                        {{
                                                            getChartTypeLabel(
                                                                cell.value
                                                            )
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                    </template>
                                </tr>
                            </template>
                        </draggable>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-end">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-primary"
                                        @click="addRow"
                                    >
                                        <Plus :size="16" class="me-1" />
                                        Adicionar
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Destinations -->
        <div class="card mb-3">
            <div
                class="card-header d-flex justify-content-between align-items-center"
            >
                <span>Destinatários</span>
                <div>
                    <button
                        type="button"
                        class="btn btn-sm btn-secondary me-2"
                        @click="openGroupListModal"
                    >
                        Listar Grupos
                    </button>
                    <button
                        type="button"
                        class="btn btn-sm btn-success"
                        @click="showGroupCreateModal = true"
                    >
                        Criar Grupo
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="input-group mb-2">
                            <select
                                v-model="newDestination.type"
                                class="form-select"
                                style="max-width: 150px"
                            >
                                <option
                                    v-for="type in destinationTypes"
                                    :key="type.value"
                                    :value="type.value"
                                >
                                    {{ type.label }}
                                </option>
                            </select>
                            <input
                                v-model="newDestination.value"
                                type="text"
                                class="form-control"
                                :placeholder="
                                    newDestination.type === 'group'
                                        ? 'Código do Grupo'
                                        : 'Número do WhatsApp'
                                "
                            />
                        </div>

                        <button
                            type="button"
                            class="btn btn-sm btn-primary float-end"
                            @click="addDestination"
                        >
                            <Plus :size="16" class="me-1" />
                            Adicionar
                        </button>
                    </div>
                </div>

                <div v-if="formData.destinations.length > 0">
                    <div
                        v-for="(dest, index) in formData.destinations"
                        :key="index"
                        class="card mb-2"
                    >
                        <div
                            class="card-body d-flex justify-content-between align-items-center"
                        >
                            <div>
                                <strong>{{
                                    getDestinationTypeLabel(dest.type)
                                }}</strong>
                                <span class="text-muted ms-2"
                                    >[{{
                                        dest.type === "group"
                                            ? groupsMap[dest.value] ||
                                              dest.value
                                            : dest.value
                                    }}]</span
                                >
                            </div>
                            <button
                                type="button"
                                class="btn btn-sm btn-secondary"
                                @click="removeDestination(index)"
                            >
                                Excluir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedules -->
        <div class="card mb-3">
            <div class="card-header">Agendamentos</div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label class="form-label">Horário</label>
                        <select
                            v-model="newSchedule.schedule_id"
                            class="form-select"
                        >
                            <option value="">-</option>
                            <option
                                v-for="schedule in schedules"
                                :key="schedule.id"
                                :value="schedule.id"
                            >
                                {{ schedule.time }}
                            </option>
                        </select>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Dias da Semana</label>
                        <div class="d-flex gap-2">
                            <div class="form-check">
                                <input
                                    v-model="newSchedule.sun"
                                    class="form-check-input"
                                    type="checkbox"
                                    id="new-sun"
                                />
                                <label class="form-check-label" for="new-sun">
                                    Dom
                                </label>
                            </div>
                            <div class="form-check">
                                <input
                                    v-model="newSchedule.mon"
                                    class="form-check-input"
                                    type="checkbox"
                                    id="new-mon"
                                />
                                <label class="form-check-label" for="new-mon">
                                    Seg
                                </label>
                            </div>
                            <div class="form-check">
                                <input
                                    v-model="newSchedule.tue"
                                    class="form-check-input"
                                    type="checkbox"
                                    id="new-tue"
                                />
                                <label class="form-check-label" for="new-tue">
                                    Ter
                                </label>
                            </div>
                            <div class="form-check">
                                <input
                                    v-model="newSchedule.wed"
                                    class="form-check-input"
                                    type="checkbox"
                                    id="new-wed"
                                />
                                <label class="form-check-label" for="new-wed">
                                    Qua
                                </label>
                            </div>
                            <div class="form-check">
                                <input
                                    v-model="newSchedule.thu"
                                    class="form-check-input"
                                    type="checkbox"
                                    id="new-thu"
                                />
                                <label class="form-check-label" for="new-thu">
                                    Qui
                                </label>
                            </div>
                            <div class="form-check">
                                <input
                                    v-model="newSchedule.fri"
                                    class="form-check-input"
                                    type="checkbox"
                                    id="new-fri"
                                />
                                <label class="form-check-label" for="new-fri">
                                    Sex
                                </label>
                            </div>
                            <div class="form-check">
                                <input
                                    v-model="newSchedule.sat"
                                    class="form-check-input"
                                    type="checkbox"
                                    id="new-sat"
                                />
                                <label class="form-check-label" for="new-sat">
                                    Sáb
                                </label>
                            </div>
                        </div>
                    </div>

                    <div
                        class="col-md-2 d-flex justify-content-end align-items-end"
                    >
                        <button
                            type="button"
                            class="btn btn-sm btn-primary"
                            @click="addSchedule"
                        >
                            <Plus :size="16" class="me-1" />
                            Adicionar
                        </button>
                    </div>
                </div>

                <div v-if="formData.schedules.length > 0">
                    <div
                        v-for="(sched, index) in formData.schedules"
                        :key="index"
                        class="card mb-2"
                    >
                        <div
                            class="card-body d-flex justify-content-between align-items-center"
                        >
                            <div>
                                <strong>{{
                                    getScheduleTime(sched.schedule_id)
                                }}</strong>
                                -
                                <span class="text-muted">
                                    <span v-if="sched.sun" class="me-1"
                                        >Dom</span
                                    >
                                    <span v-if="sched.mon" class="me-1"
                                        >Seg</span
                                    >
                                    <span v-if="sched.tue" class="me-1"
                                        >Ter</span
                                    >
                                    <span v-if="sched.wed" class="me-1"
                                        >Qua</span
                                    >
                                    <span v-if="sched.thu" class="me-1"
                                        >Qui</span
                                    >
                                    <span v-if="sched.fri" class="me-1"
                                        >Sex</span
                                    >
                                    <span v-if="sched.sat" class="me-1"
                                        >Sáb</span
                                    >
                                </span>
                            </div>
                            <button
                                type="button"
                                class="btn btn-sm btn-secondary"
                                @click="removeSchedule(index)"
                            >
                                Excluir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button
                type="submit"
                class="btn btn-primary"
                :disabled="processing"
            >
                <span
                    v-if="processing"
                    class="spinner-border spinner-border-sm me-1"
                    role="status"
                    aria-hidden="true"
                ></span>
                <Save :size="18" class="me-1" v-else />
                {{ image ? "Atualizar" : "Salvar" }}
            </button>
        </div>
    </form>

    <GroupListModal v-model:show="showGroupListModal" @select="onGroupSelect" />
    <GroupCreateModal
        v-model:show="showGroupCreateModal"
        @created="onGroupCreated"
    />
</template>

<style scoped>
.form-check {
    min-width: 60px;
}

.drag-handle {
    cursor: move;
    user-select: none;
}

.drag-handle:hover {
    background-color: #e9ecef !important;
}

:deep(.sortable-ghost) {
    opacity: 0.4;
    background-color: #f8f9fa;
}

:deep(.sortable-chosen) {
    background-color: #e7f3ff;
}

:deep(.sortable-drag) {
    opacity: 0.8;
}

/* Layout uniforme da tabela */
.table-bordered td {
    vertical-align: top;
}
</style>
