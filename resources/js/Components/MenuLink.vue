<script setup>
import { Link } from "@inertiajs/vue3";
import { ChevronRight } from "lucide-vue-next";
const props = defineProps({
    href: { type: [String, Object], required: true },
    title: { type: String, required: true },
    description: { type: String, required: true },
    icon: { type: Object, required: true },
    ariaLabel: { type: String, default: null },
});
</script>

<template>
    <Link
        :href="href"
        class="card h-100 text-decoration-none text-dark menu-card"
        role="button"
        :aria-label="ariaLabel || title"
    >
        <div class="card-body d-flex align-items-start gap-3">
            <div
                class="icon bg-dark text-white rounded-1 d-flex align-items-center justify-content-center"
                style="min-width: 64px; min-height: 64px"
            >
                <component :is="icon" size="24" />
            </div>

            <div class="flex-grow-1">
                <h5 class="card-title mb-1">{{ title }}</h5>
                <p class="card-text small text-muted mb-0">{{ description }}</p>
            </div>

            <div class="ms-auto align-self-center">
                <ChevronRight size="18" />
            </div>
        </div>
    </Link>
</template>

<style scoped>
.menu-card {
    transition: transform 0.15s ease;
}
.menu-card:hover {
    transform: translateY(-4px);
}
</style>
