<script setup>
import { Head, useForm, usePage, Link } from "@inertiajs/vue3";
import { Lock } from "lucide-vue-next";

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const page = usePage();

const submit = () => {
    form.post(route("login.attempt"));
};
</script>

<template>
    <Head title="Login" />
    <div class="min-vh-100 d-flex align-items-stretch">
        <div
            class="col-12 col-md-6 d-flex align-items-center justify-content-center py-5"
            style="background-color: #f3f3f3"
        >
            <div class="w-100 px-4" style="max-width: 500px">
                <div class="text-center mb-4">
                    <div
                        class="d-inline-block bg-white border rounded-circle p-3"
                    >
                        <Lock size="22" class="text-primary" />
                    </div>
                    <h3 class="mt-3">Entrar na Conta</h3>
                    <p class="text-muted small">Acesse a sua conta</p>
                </div>

                <form @submit.prevent="submit" class="card">
                    <div class="card-body">
                        <div
                            v-if="page.props.flash && page.props.flash.error"
                            class="alert alert-danger small"
                        >
                            {{ page.props.flash.error }}
                        </div>
                        <div
                            v-else-if="Object.keys(form.errors).length"
                            class="alert alert-danger small"
                        >
                            Ocorreu um erro ao fazer login. Verifique os dados e
                            tente novamente.
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input
                                v-model="form.email"
                                id="email"
                                type="email"
                                :class="[
                                    'form-control',
                                    form.errors.email ? 'is-invalid' : '',
                                ]"
                                autofocus
                            />
                            <div
                                v-if="form.errors.email"
                                class="invalid-feedback d-block"
                            >
                                {{ form.errors.email }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                Senha
                            </label>
                            <input
                                v-model="form.password"
                                id="password"
                                type="password"
                                :class="[
                                    'form-control',
                                    form.errors.password ? 'is-invalid' : '',
                                ]"
                            />
                            <div
                                v-if="form.errors.password"
                                class="invalid-feedback d-block"
                            >
                                {{ form.errors.password }}
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input
                                v-model="form.remember"
                                id="remember"
                                type="checkbox"
                                class="form-check-input"
                            />
                            <label class="form-check-label" for="remember">
                                Permanecer Conectado
                            </label>
                        </div>

                        <div class="d-grid mt-2">
                            <button
                                class="btn btn-primary"
                                type="submit"
                                :disabled="form.processing"
                            >
                                Entrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div
            class="d-none d-md-flex col-md-6 text-white align-items-center justify-content-center py-5"
            style="background-color: #1a1a1a"
        >
            <div class="px-4 text-center">
                <h1 class="h-2 fw-bold">Consys</h1>
                <p class="lead">Painel Administrativo</p>
            </div>
        </div>
    </div>
</template>
