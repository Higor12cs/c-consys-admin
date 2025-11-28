<script setup>
import { ref, watch } from "vue";
import { toast } from "vue3-toastify";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:show", "created"]);

const isVisible = ref(props.show);
const name = ref("");
const loading = ref(false);
const error = ref(null);
const contacts = ref([
    { value: "+553434238524", fixed: false },
    { value: "+5534999742311", fixed: false },
]);

watch(
    () => props.show,
    (val) => {
        isVisible.value = val;
        if (val) {
            name.value = "";
            contacts.value = [
                { value: "+553434238524", fixed: false },
                { value: "+5534999742311", fixed: false },
            ];
            error.value = null;
            document.body.classList.add("modal-open");
        } else {
            document.body.classList.remove("modal-open");
        }
    }
);

const close = () => {
    emit("update:show", false);
};

const addContact = () => {
    contacts.value.push({ value: "", fixed: false });
};

const removeContact = (index) => {
    contacts.value.splice(index, 1);
};

const create = async () => {
    if (!name.value || name.value.trim() === "") {
        toast.error("Informe o nome do grupo!");
        return;
    }
    loading.value = true;
    error.value = null;
    try {
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrf = tokenMeta ? tokenMeta.getAttribute("content") : null;
        const headers = { "Content-Type": "application/json" };

        if (csrf) headers["X-CSRF-TOKEN"] = csrf;

        headers["X-Requested-With"] = "XMLHttpRequest";

        const payloadContacts = contacts.value
            .map((c) => (c.value || "").trim())
            .filter(Boolean);

        if (payloadContacts.length === 0) {
            toast.error("Adicione no mínimo um contato!");
            loading.value = false;
            return;
        }

        const res = await fetch("/whatsapp/groups", {
            method: "POST",
            headers,
            body: JSON.stringify({
                name: name.value.trim(),
                contacts: payloadContacts,
            }),
        });

        if (!res.ok) {
            const txt = await res.text();
            toast.error(txt || `Erro: ${res.status}`);
            loading.value = false;
            return;
        }

        const data = await res.json();
        emit("created", data);
        toast.success("Grupo criado com sucesso!");
        close();
    } catch (e) {
        toast.error(e.message || String(e));
    } finally {
        loading.value = false;
    }
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
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Criar Grupo</h5>
                            <button
                                type="button"
                                class="btn-close"
                                @click="close"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nome do Grupo</label>
                                <input
                                    v-model="name"
                                    type="text"
                                    class="form-control"
                                />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Contatos</label>
                                <div class="mb-2 text-muted">
                                    Adicione números no formato internacional
                                    (ex: +5534999999999)
                                </div>
                                <div
                                    v-for="(c, idx) in contacts"
                                    :key="idx"
                                    class="input-group mb-2"
                                >
                                    <input
                                        v-model="contacts[idx].value"
                                        type="text"
                                        class="form-control"
                                    />
                                    <button
                                        v-if="!contacts[idx].fixed"
                                        class="btn btn-outline-secondary"
                                        type="button"
                                        @click="removeContact(idx)"
                                    >
                                        Remover
                                    </button>
                                    <span v-else class="input-group-text"
                                        >Pré-fixado</span
                                    >
                                </div>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    @click="addContact"
                                >
                                    Adicionar Contato
                                </button>
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
                                type="button"
                                class="btn btn-primary"
                                :disabled="loading"
                                @click="create"
                            >
                                <span
                                    v-if="loading"
                                    class="spinner-border spinner-border-sm me-1"
                                    role="status"
                                    aria-hidden="true"
                                ></span>
                                Criar
                            </button>
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
