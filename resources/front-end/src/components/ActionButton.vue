<template>
  <button type="button" :class="classes" @click="emit('click')">
    {{ text }}
    <font-awesome-icon v-if="isLoading" icon="spinner" class="ml-1" spin />
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  text: { type: String, default: '' },
  isLoading: { type: Boolean, default: false },
  variant: { type: String, default: 'primary' },
})

const emit = defineEmits(['click'])

const variantClasses = {
  primary:         'bg-blue-600 text-white border-blue-600 hover:bg-blue-700',
  secondary:       'bg-gray-600 text-white border-gray-600 hover:bg-gray-700',
  danger:          'bg-red-600 text-white border-red-600 hover:bg-red-700',
  'outline-primary': 'bg-transparent text-blue-600 border-blue-600 hover:bg-blue-50',
  'outline-danger':  'bg-transparent text-red-600 border-red-600 hover:bg-red-50',
}

const classes = computed(() => [
  'inline-flex items-center gap-1 px-4 py-2 rounded border font-medium cursor-pointer transition-colors disabled:opacity-50',
  variantClasses[props.variant] ?? variantClasses.primary,
])
</script>
