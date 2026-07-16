<script setup>
import { nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
import { X } from 'lucide-vue-next';
import ModelPhoto from '@/components/photos/ModelPhoto.vue';

const props = defineProps({
  src: { type: String, required: true },
  alt: { type: String, default: '' },
});
const emit = defineEmits(['close']);
const closeButton = ref(null);
let previouslyFocused = null;

function close() {
  emit('close');
}

function onKeydown(event) {
  if (event.key === 'Escape') close();
  if (event.key === 'Tab') {
    event.preventDefault();
    closeButton.value?.focus();
  }
}

onMounted(async () => {
  previouslyFocused = document.activeElement;
  document.body.style.overflow = 'hidden';
  document.addEventListener('keydown', onKeydown);
  await nextTick();
  closeButton.value?.focus();
});

onBeforeUnmount(() => {
  document.body.style.overflow = '';
  document.removeEventListener('keydown', onKeydown);
  previouslyFocused?.focus?.();
});
</script>

<template>
  <div
    class="fixed inset-0 z-[70] flex items-center justify-center bg-black/85 p-4"
    role="dialog"
    aria-modal="true"
    aria-label="Expanded photo"
    @click.self="close"
  >
    <ModelPhoto :src="props.src" :alt="props.alt" family="expanded" />
    <button
      ref="closeButton"
      type="button"
      class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded bg-black/70 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-brass"
      aria-label="Close expanded photo"
      @click="close"
    >
      <X class="h-5 w-5" />
    </button>
  </div>
</template>
