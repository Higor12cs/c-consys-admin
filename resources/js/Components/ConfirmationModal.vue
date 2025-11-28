<script setup>
import { ref, watch } from "vue";
import { X } from "lucide-vue-next";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: "Confirmar ação",
    },
    message: {
        type: String,
        default: "Tem certeza que deseja continuar?",
    },
    confirmText: {
        type: String,
        default: "Confirmar",
    },
    cancelText: {
        type: String,
        default: "Cancelar",
    },
    confirmVariant: {
        type: String,
        default: "danger",
        validator: (value) =>
            ["primary", "secondary", "success", "danger", "warning"].includes(
                value
            ),
    },
});

const emit = defineEmits(["confirm", "cancel", "update:show"]);

const isVisible = ref(props.show);

watch(
    () => props.show,
    (newVal) => {
        isVisible.value = newVal;
        if (newVal) {
            document.body.classList.add("modal-open");
        } else {
            document.body.classList.remove("modal-open");
        }
    }
);

const handleConfirm = () => {
    emit("confirm");
    closeModal();
};

const handleCancel = () => {
    emit("cancel");
    closeModal();
};

const closeModal = () => {
    isVisible.value = false;
    emit("update:show", false);
    document.body.classList.remove("modal-open");
};
</script>

<template>
    <Teleport to="body">
        <Transition name="modal">
            <div
                v-if="isVisible"
                class="modal fade show d-block"
                tabindex="-1"
                @click.self="handleCancel"
            >
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ title }}</h5>
                            <button
                                type="button"
                                class="btn-close"
                                @click="handleCancel"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-0">{{ message }}</p>
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                @click="handleCancel"
                            >
                                {{ cancelText }}
                            </button>
                            <button
                                type="button"
                                :class="`btn btn-${confirmVariant}`"
                                @click="handleConfirm"
                            >
                                {{ confirmText }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
        <Transition name="backdrop">
            <div
                v-if="isVisible"
                class="modal-backdrop fade show"
                @click="handleCancel"
            ></div>
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
