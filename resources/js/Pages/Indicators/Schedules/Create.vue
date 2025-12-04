<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Form, Head, Link } from "@inertiajs/vue3";
import { ArrowLeft, Save } from "lucide-vue-next";
</script>

<template>
    <Head title="Novo Agendamento" />
    <AppLayout>
        <div class="d-flex justify-content-between mb-3">
            <h1 class="h4">Novo Agendamento</h1>
            <Link
                :href="route('indicators.schedules.index')"
                class="btn btn-secondary"
            >
                <ArrowLeft :size="18" class="me-1" />
                Voltar
            </Link>
        </div>

        <div class="card">
            <div class="card-header">Novo Agendamento</div>
            <div class="card-body">
                <Form
                    :action="route('indicators.schedules.store')"
                    method="post"
                    #default="{ errors, processing }"
                >
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="time" class="form-label">
                                    Horário
                                </label>
                                <input
                                    type="time"
                                    class="form-control"
                                    :class="{ 'is-invalid': errors.time }"
                                    id="time"
                                    name="time"
                                />
                                <div
                                    v-if="errors.time"
                                    class="invalid-feedback"
                                >
                                    {{ errors.time }}
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
