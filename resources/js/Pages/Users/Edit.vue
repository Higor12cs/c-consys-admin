<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Form, Head, Link } from "@inertiajs/vue3";
import { ArrowLeft, Save } from "lucide-vue-next";

const props = defineProps({ user: { type: Object, required: true } });
</script>

<template>
    <Head title="Editar Usuário" />
    <AppLayout>
        <div class="d-flex justify-content-between mb-3">
            <h1 class="h4">Editar Usuário</h1>
            <Link :href="route('users.index')" class="btn btn-secondary">
                <ArrowLeft :size="18" class="me-1" />
                Voltar
            </Link>
        </div>

        <div class="card">
            <div class="card-header">Editar Usuário</div>
            <div class="card-body">
                <Form
                    :action="`/users/${user.id}`"
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
                                <label for="whatsapp" class="form-label"
                                    >WhatsApp</label
                                >
                                <input
                                    id="whatsapp"
                                    name="whatsapp"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': errors.whatsapp }"
                                    :value="user.whatsapp"
                                />
                                <div
                                    v-if="errors.whatsapp"
                                    class="invalid-feedback"
                                >
                                    {{ errors.whatsapp }}
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
                            <Save :size="18" class="me-1" v-else />
                            Atualizar
                        </button>
                    </div>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>
