<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Form, Head } from "@inertiajs/vue3";
import { Save } from "lucide-vue-next";
const props = defineProps({
    user: { type: Object, required: true },
});
</script>

<template>
    <Head title="Perfil" />
    <AppLayout>
        <div class="d-flex justify-content-between mb-3">
            <h1 class="h4">Perfil</h1>
        </div>

        <div class="card">
            <div class="card-header">Atualizar Perfil</div>
            <div class="card-body">
                <Form
                    :action="route('profile.update')"
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
                                    id="name"
                                    name="name"
                                    class="form-control"
                                    :class="{ 'is-invalid': errors.name }"
                                    :value="user.name"
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
                                <label for="email" class="form-label"
                                    >E-mail</label
                                >
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    class="form-control"
                                    :class="{ 'is-invalid': errors.email }"
                                    :value="user.email"
                                />
                                <div
                                    v-if="errors.email"
                                    class="invalid-feedback"
                                >
                                    {{ errors.email }}
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-3">
                                <label for="password" class="form-label"
                                    >Nova Senha</label
                                >
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    class="form-control"
                                    :class="{ 'is-invalid': errors.password }"
                                    autocomplete="new-password"
                                />
                                <div
                                    v-if="errors.password"
                                    class="invalid-feedback"
                                >
                                    {{ errors.password }}
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-3">
                                <label
                                    for="password_confirmation"
                                    class="form-label"
                                    >Confirmar Senha</label
                                >
                                <input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    class="form-control"
                                    :class="{
                                        'is-invalid':
                                            errors.password_confirmation,
                                    }"
                                    autocomplete="new-password"
                                />
                                <div
                                    v-if="errors.password_confirmation"
                                    class="invalid-feedback"
                                >
                                    {{ errors.password_confirmation }}
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
                            <Save v-else :size="18" class="me-1" />
                            Atualizar
                        </button>
                    </div>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>
