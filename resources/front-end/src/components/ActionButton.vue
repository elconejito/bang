<template>
  <button :type="type" :class="classes" :disabled="disabled || isLoading" @click="emit('click')">
    <LoaderCircle v-if="isLoading" class="h-4 w-4 animate-spin" />
    {{ text }}
  </button>
</template>

<script setup>
import { computed } from 'vue'
import { LoaderCircle } from 'lucide-vue-next'

const props = defineProps({
  text: { type: String, default: '' },
  type: { type: String, default: 'button' },
  isLoading: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  variant: { type: String, default: 'primary' },
})

const emit = defineEmits(['click'])

const variantClasses = {
  primary:         'bg-brass text-ink-900 border-[#b08a2e] hover:bg-brass-600',
  secondary:       'bg-gray-600 text-white border-gray-600 hover:bg-gray-700',
  danger:          'bg-red-600 text-white border-red-600 hover:bg-red-700',
  'outline-primary': 'bg-transparent text-blue-600 border-blue-600 hover:bg-blue-50',
  'outline-danger':  'bg-transparent text-red-600 border-red-600 hover:bg-red-50',
}

const classes = computed(() => [
  'inline-flex items-center justify-center gap-2 rounded border px-4 py-2 font-medium transition-colors disabled:cursor-not-allowed disabled:opacity-50',
  variantClasses[props.variant] ?? variantClasses.primary,
])
</script>
