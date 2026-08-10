<script setup>
import { LoaderCircle } from 'lucide-vue-next';

defineProps({
  submitLabel: { type: String, required: true },
  savingLabel: { type: String, default: 'Saving…' },
  saving: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
});

const emit = defineEmits(['submit', 'cancel']);
</script>

<template>
  <form
    class="overflow-hidden rounded border border-line bg-white"
    data-testid="magazine-form-panel"
    @submit.prevent="emit('submit')"
  >
    <div class="grid gap-5 p-5 sm:grid-cols-2">
      <slot />
    </div>

    <footer class="flex items-center justify-end gap-3 border-t border-line bg-ink-50 px-5 py-4">
      <button
        type="submit"
        :disabled="disabled || saving"
        class="inline-flex items-center justify-center gap-2 rounded border border-[#b08a2e] bg-brass px-5 py-[10px] text-[14px] font-semibold text-ink-900 transition-colors hover:bg-[#b8902f] disabled:cursor-not-allowed disabled:opacity-50"
      >
        <LoaderCircle v-if="saving" class="h-4 w-4 animate-spin" />
        {{ saving ? savingLabel : submitLabel }}
      </button>
      <button
        type="button"
        :disabled="saving"
        class="px-5 py-[10px] text-[14px] text-[#5b6066] transition-colors hover:text-[#1a1c1f] disabled:cursor-not-allowed disabled:opacity-50"
        @click="emit('cancel')"
      >
        Cancel
      </button>
    </footer>
  </form>
</template>
