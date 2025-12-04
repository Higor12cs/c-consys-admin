<script setup>
import { ref, watch } from "vue";
import { toast } from "vue3-toastify";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:show", "select"]);

const isVisible = ref(props.show);
const groups = ref([]);
const loading = ref(false);
const error = ref(null);
const search = ref("");

watch(
    () => props.show,
    (val) => {
        isVisible.value = val;
        if (val) fetchGroups();
        if (val) document.body.classList.add("modal-open");
        else document.body.classList.remove("modal-open");
    }
);

const fetchGroups = async () => {
    loading.value = true;
    error.value = null;
    try {
        const res = await fetch("/whatsapp/groups");
        if (!res.ok) {
            const text = await res.text();
            toast.error(text || "Erro ao buscar grupos");
            groups.value = [];
            loading.value = false;
            return;
        }
        const data = await res.json();
        if (Array.isArray(data)) {
            groups.value = data.map((g) => ({
                id: g.groupId || g.groupMetadata?.id || g.id || null,
                name:
                    g.groupMetadata?.subject ||
                    g.groupMetadata?.desc ||
                    g.name ||
                    g.groupName ||
                    "",
            }));
        } else {
            groups.value = [];
        }
    } catch (e) {
        toast.error(e.message || String(e));
        groups.value = [];
    } finally {
        loading.value = false;
    }
};

const filtered = () => {
    const q = search.value.trim().toLowerCase();
    if (!q) return groups.value;
    return groups.value.filter(
        (g) =>
            (g.id && String(g.id).toLowerCase().includes(q)) ||
            (g.name && g.name.toLowerCase().includes(q))
    );
};

const copyId = async (id) => {
    try {
        await navigator.clipboard.writeText(String(id));
        toast.success("Código copiado para a área de transferência!");
    } catch (e) {
        toast.error("Falha ao copiar o código: " + (e.message || String(e)));
    }
};

const close = () => {
    emit("update:show", false);
};

const select = (g) => {
    emit("select", g);
    close();
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
                            <h5 class="modal-title">Grupos</h5>
                            <button
                                type="button"
                                class="btn-close"
                                @click="close"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <input
                                    v-model="search"
                                    type="text"
                                    class="form-control"
                                    placeholder="Pesquisar"
                                />
                            </div>
                            <div v-if="loading" class="text-center py-3">
                                Carregando...
                            </div>
                            <div v-else>
                                <div v-if="error" class="alert alert-danger">
                                    {{ error }}
                                </div>
                                <div
                                    v-if="filtered().length === 0"
                                    class="text-muted"
                                >
                                    Nenhum grupo encontrado.
                                </div>
                                <div
                                    v-for="g in filtered()"
                                    :key="g.id"
                                    class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded"
                                >
                                    <div>
                                        <div class="fw-bold">{{ g.name }}</div>
                                        <div class="text-muted small">
                                            {{ g.id }}
                                        </div>
                                    </div>
                                    <div>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-secondary me-2"
                                            @click="copyId(g.id)"
                                        >
                                            Copiar Código
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-primary"
                                            @click="select(g)"
                                        >
                                            Selecionar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
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
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.15s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.backdrop-enter-active,
.backdrop-leave-active {
    transition: opacity 0.15s ease;
}

.backdrop-enter-from,
.backdrop-leave-to {
    opacity: 0;
}
</style>
