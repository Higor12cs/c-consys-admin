<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Form, Head, Link } from "@inertiajs/vue3";
import { ArrowLeft, Save } from "lucide-vue-next";
</script>

<template>
    <Head title="Novo Indicador" />
    <AppLayout>
        <div class="d-flex justify-content-between mb-3">
            <h1 class="h4">Novo Indicador</h1>
            <Link :href="route('indicators.indicators.index')" class="btn btn-secondary">
                <ArrowLeft :size="18" class="me-1" />
                Voltar
            </Link>
        </div>

        <div class="card">
            <div class="card-header">Novo Indicador</div>
            <div class="card-body">
                <Form
                    :action="route('indicators.indicators.store')"
                    method="post"
                    #default="{ errors, processing }"
                >
                    <div class="mb-3">
                        <label class="form-label">Código</label>
                        <input
                            name="code"
                            class="form-control"
                            :class="{ 'is-invalid': errors.code }"
                        />
                        <div v-if="errors.code" class="invalid-feedback">
                            {{ errors.code }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <input
                            name="description"
                            class="form-control"
                            :class="{ 'is-invalid': errors.description }"
                        />
                        <div v-if="errors.description" class="invalid-feedback">
                            {{ errors.description }}
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input
                            name="is_percentage"
                            type="checkbox"
                            class="form-check-input"
                            id="is_percentage"
                        />
                        <label class="form-check-label" for="is_percentage">
                            Porcentagem
                        </label>
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
                            Salvar
                        </button>
                    </div>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>
