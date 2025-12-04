<script setup>
import { computed } from "vue";

const props = defineProps({
    modelValue: { type: [String, Number], default: "" },
    label: { type: String, default: null },
    error: { type: String, default: null },
    options: { type: Array, required: true },
    placeholder: { type: String, default: "Selecione" },
    optionValue: { type: String, default: "id" },
    optionLabel: { type: String, default: "name" },
    class: { type: String, default: "" },
});

const emit = defineEmits(["update:modelValue"]);

const value = computed({
    get: () => props.modelValue,
    set: (val) => emit("update:modelValue", val),
});
</script>

<template>
    <div class="mb-3" :class="class">
        <label v-if="label" class="form-label">{{ label }}</label>
        <select
            v-model="value"
            class="form-select"
            :class="{ 'is-invalid': error }"
        >
            <option value="">{{ placeholder }}</option>
            <option
                v-for="option in options"
                :key="option[optionValue]"
                :value="option[optionValue]"
            >
                {{ option[optionLabel] }}
            </option>
        </select>
        <div v-if="error" class="invalid-feedback">{{ error }}</div>
    </div>
</template>
