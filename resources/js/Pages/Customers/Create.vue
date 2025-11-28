<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Form, Head, Link } from "@inertiajs/vue3";
import { ArrowLeft, Save } from "lucide-vue-next";
</script>

<template>
    <Head title="Novo Cliente" />
    <AppLayout>
        <div class="d-flex justify-content-between mb-3">
            <h1 class="h4">Novo Cliente</h1>
            <Link :href="route('customers.index')" class="btn btn-secondary">
                <ArrowLeft :size="18" class="me-1" />
                Voltar
            </Link>
        </div>

        <div class="card">
            <div class="card-header">Novo Cliente</div>
            <div class="card-body">
                <Form
                    :action="route('customers.store')"
                    method="post"
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
                            Salvar
                        </button>
                    </div>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>
