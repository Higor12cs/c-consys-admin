<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Form, Head, Link } from "@inertiajs/vue3";
import { ArrowLeft, Save } from "lucide-vue-next";

const props = defineProps({
    schedule: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Editar Agendamento" />
    <AppLayout>
        <div class="d-flex justify-content-between mb-3">
            <h1 class="h4">Editar Agendamento</h1>
            <Link
                :href="route('indicators.schedules.index')"
                class="btn btn-secondary"
            >
                <ArrowLeft :size="18" class="me-1" />
                Voltar
            </Link>
        </div>

        <div class="card">
            <div class="card-header">Editar Agendamento</div>
            <div class="card-body">
                <Form
                    :action="
                        route('indicators.schedules.update', {
                            schedule: props.schedule.id,
                        })
                    "
                    method="put"
                    disableWhileProcessing
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
                                    name="time"
                                    :value="schedule.time"
                                />
                                <div
                                    v-if="errors.time"
                                    class="invalid-feedback"
                                >
                                    {{ errors.time }}
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label" for="is_active">
                                    Ativo
                                </label>
                                <select
                                    class="form-select"
                                    :class="{ 'is-invalid': errors.is_active }"
                                    name="is_active"
                                >
                                    <option value="1">Sim</option>
                                    <option value="0">Não</option>
                                </select>
                                <div
                                    v-if="errors.is_active"
                                    class="invalid-feedback d-block"
                                >
                                    {{ errors.is_active }}
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
    </AppLayout>
</template>
