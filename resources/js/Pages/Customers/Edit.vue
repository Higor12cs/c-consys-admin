<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Form, Head, Link, router } from "@inertiajs/vue3";
import { ArrowLeft, Copy, RotateCcw, RotateCw, Save } from "lucide-vue-next";
import { ref } from "vue";
import { toast } from "vue3-toastify";

const props = defineProps({
    customer: {
        type: Object,
        required: true,
    },
});

const copyToken = () => {
    navigator.clipboard.writeText(props.customer.api_token).then(() => {
        toast.info("Token copiado para a área de transferência!");
    });
};

const processing = ref(false);

const regenerateToken = () => {
    processing.value = true;

    router.post(
        route("customers.regenerate-token", { customer: props.customer.id }),
        {},
        {
            onFinish: () => {
                processing.value = false;
            },
        }
    );
};
</script>

<template>
    <Head title="Editar Cliente" />
    <AppLayout>
        <div class="d-flex justify-content-between mb-3">
            <h1 class="h4">Editar Cliente</h1>
            <Link :href="route('customers.index')" class="btn btn-secondary">
                <ArrowLeft :size="18" class="me-1" />
                Voltar
            </Link>
        </div>

        <div class="card">
            <div class="card-header">Editar Cliente</div>
            <div class="card-body">
                <Form
                    :action="`/customers/${customer.id}`"
                    method="put"
                    disableWhileProcessing
                    #default="{ errors, processing }"
                >
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="name" class="form-label"
                                    >Nome</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': errors.name }"
                                    id="name"
                                    name="name"
                                    :value="customer.name"
                                />
                                <div
                                    v-if="errors.name"
                                    class="invalid-feedback"
                                >
                                    {{ errors.name }}
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-3">
                                <label for="external_id" class="form-label"
                                    >Código Externo</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': errors.external_id,
                                    }"
                                    id="external_id"
                                    name="external_id"
                                    :value="customer.external_id"
                                />
                                <div
                                    v-if="errors.external_id"
                                    class="invalid-feedback"
                                >
                                    {{ errors.external_id }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
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
                            Atualizar
                        </button>
                    </div>
                </Form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">API Token</div>
            <div class="card-body">
                <label class="form-label">Token de API</label>
                <div class="input-group mb-2">
                    <input
                        type="text"
                        class="form-control"
                        :value="customer.api_token"
                        :disabled="processing"
                        readonly
                    />
                    <button
                        class="btn btn-primary"
                        @click="copyToken"
                        :disabled="processing"
                    >
                        <Copy :size="18" class="me-1" />
                        Copiar
                    </button>
                </div>

                <div class="d-flex justify-content-start mt-3">
                    <button
                        class="btn btn-primary"
                        @click="regenerateToken"
                        :disabled="processing"
                    >
                        <span
                            v-if="processing"
                            class="spinner-border spinner-border-sm me-1"
                            role="status"
                            aria-hidden="true"
                        ></span>
                        <RotateCw :size="18" class="me-1" v-else />
                        {{ processing ? "Regerando..." : "Regerar Token" }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
