<script setup>
import { computed } from "vue";

const props = defineProps({
    modelValue: { type: String, default: "" },
    label: { type: String, default: null },
    error: { type: String, default: null },
    rows: { type: Number, default: 3 },
    placeholder: { type: String, default: "" },
});

const emit = defineEmits(["update:modelValue"]);

const value = computed({
    get: () => props.modelValue,
    set: (val) => emit("update:modelValue", val),
});
</script>

<template>
    <div class="mb-3">
        <label v-if="label" class="form-label">{{ label }}</label>
        <textarea
            v-model="value"
            :rows="rows"
            :placeholder="placeholder"
            class="form-control"
            :class="{ 'is-invalid': error }"
        ></textarea>
        <div v-if="error" class="invalid-feedback">{{ error }}</div>
    </div>
</template>
