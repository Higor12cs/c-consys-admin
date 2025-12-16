<script setup>
import { computed } from "vue";

const props = defineProps({
    modelValue: { type: [String, Number], default: "" },
    label: { type: String, default: null },
    type: { type: String, default: "text" },
    error: { type: String, default: null },
    placeholder: { type: String, default: "" },
    class: { type: String, default: "" },
    disabled: { type: Boolean, default: false },
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
        <input
            v-model="value"
            :type="type"
            :placeholder="placeholder"
            class="form-control"
            :class="{ 'is-invalid': error }"
            :disabled="disabled"
        />
        <div v-if="error" class="invalid-feedback">{{ error }}</div>
    </div>
</template>
