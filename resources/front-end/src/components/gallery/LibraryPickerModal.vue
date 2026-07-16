<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
import { Check, LoaderCircle, X } from 'lucide-vue-next';
import ModelPhoto from '@/components/photos/ModelPhoto.vue';
import { usePicturesStore } from '@/stores/pictures';

const props = defineProps({
  entityType: { type: String, required: true },
  entityId: { type: Number, required: true },
  attachedIds: { type: Array, default: () => [] },
});
const emit = defineEmits(['attach', 'close']);
const picturesStore = usePicturesStore();
const library = ref([]);
const loading = ref(true);
const error = ref(null);
const selected = ref(new Set());
const saving = ref(false);
const closeButton = ref(null);
let previouslyFocused = null;

const selectedCount = computed(() => selected.value.size);
const isAttached = (id) => props.attachedIds.includes(id);
const isSelected = (id) => selected.value.has(id);

async function loadLibrary() {
  loading.value = true;
  error.value = null;
  try {
    const response = await picturesStore.fetchLibrary();
    library.value = response.data;
  } catch (exception) {
    error.value = exception?.response?.data?.message ?? 'The photo library could not be loaded.';
  } finally {
    loading.value = false;
  }
}

function toggle(id) {
  if (isAttached(id)) return;
  const next = new Set(selected.value);
  next.has(id) ? next.delete(id) : next.add(id);
  selected.value = next;
}

async function confirm() {
  if (!selectedCount.value) return;
  saving.value = true;
  error.value = null;
  try {
    for (const id of selected.value)
      await picturesStore.attachToEntity(props.entityType, props.entityId, id);
    emit('attach');
  } catch (exception) {
    error.value =
      exception?.response?.data?.message ?? 'The selected photos could not be attached.';
  } finally {
    saving.value = false;
  }
}

function onKeydown(event) {
  if (event.key === 'Escape') emit('close');
}

onMounted(async () => {
  previouslyFocused = document.activeElement;
  document.addEventListener('keydown', onKeydown);
  await loadLibrary();
  await nextTick();
  closeButton.value?.focus();
});
onBeforeUnmount(() => {
  document.removeEventListener('keydown', onKeydown);
  previouslyFocused?.focus?.();
});
</script>

<template>
  <div
    class="fixed inset-0 z-40 flex items-start justify-center overflow-auto bg-black/50 p-4 sm:p-12"
    @click.self="emit('close')"
  >
    <div
      class="w-[720px] max-w-full overflow-hidden rounded border border-line bg-white shadow-xl"
      role="dialog"
      aria-modal="true"
      aria-labelledby="library-picker-title"
    >
      <div class="flex items-start justify-between gap-3 border-b border-line px-[18px] py-4">
        <div>
          <h2 id="library-picker-title" class="font-display text-[19px] font-semibold">
            Add from Library
          </h2>
          <p class="mt-0.5 text-[13px] text-muted">Select photos to attach to this item</p>
        </div>
        <button
          ref="closeButton"
          type="button"
          class="p-1 text-muted hover:text-ink-900"
          aria-label="Close photo library"
          @click="emit('close')"
        >
          <X class="h-[18px] w-[18px]" />
        </button>
      </div>
      <div
        v-if="error"
        class="m-[18px] rounded border border-danger/30 bg-danger/5 p-3 text-sm text-danger"
        role="alert"
      >
        {{ error }}
        <button v-if="!library.length" class="ml-2 underline" @click="loadLibrary">
          Try again
        </button>
      </div>
      <div
        class="grid max-h-[60vh] grid-cols-2 gap-2.5 overflow-y-auto p-[18px] sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5"
      >
        <div v-if="loading" class="col-span-full py-8 text-center text-[14px] text-muted">
          Loading…
        </div>
        <div
          v-else-if="!library.length"
          class="col-span-full py-8 text-center text-[14px] text-muted"
        >
          No photos in your library yet.
        </div>
        <button
          v-for="picture in library"
          :key="picture.id"
          type="button"
          :disabled="isAttached(picture.id)"
          class="relative overflow-hidden rounded text-left"
          :class="
            isAttached(picture.id)
              ? 'cursor-default border border-line opacity-55'
              : isSelected(picture.id)
                ? 'border-2 border-brass'
                : 'border border-line hover:border-ink-300'
          "
          :aria-pressed="isSelected(picture.id)"
          @click="toggle(picture.id)"
        >
          <ModelPhoto
            :src="picture.thumbnail_url || picture.url_thumbnail || picture.url"
            :alt="picture.name"
            family="gallery"
          />
          <span v-if="isAttached(picture.id)" class="absolute inset-x-0 bottom-1.5 text-center"
            ><span class="rounded-sm bg-black/80 px-1.5 py-px font-mono text-[9px] text-white"
              >ATTACHED</span
            ></span
          >
          <span
            v-else-if="isSelected(picture.id)"
            class="absolute right-1.5 top-1.5 flex h-5 w-5 items-center justify-center rounded-full border border-[#b08a2e] bg-brass"
            ><Check class="h-3 w-3"
          /></span>
        </button>
      </div>
      <div
        class="flex flex-wrap items-center gap-3 border-t border-line bg-ink-50 px-[18px] py-3.5"
      >
        <span class="text-[13px] text-muted"
          ><b class="text-ink-900">{{ selectedCount }}</b> selected</span
        >
        <div class="ml-auto flex gap-2.5">
          <button
            type="button"
            class="rounded border border-[#c2c6ca] bg-white px-4 py-2 text-[14px] font-semibold"
            @click="emit('close')"
          >
            Cancel</button
          ><button
            type="button"
            :disabled="!selectedCount || saving"
            class="inline-flex items-center gap-1.5 rounded border border-[#b08a2e] bg-brass px-4 py-2 text-[14px] font-semibold disabled:opacity-50"
            @click="confirm"
          >
            <LoaderCircle v-if="saving" class="h-4 w-4 animate-spin" /><Check
              v-else
              class="h-4 w-4"
            />{{
              saving
                ? 'Attaching…'
                : `Attach ${selectedCount || ''} photo${selectedCount !== 1 ? 's' : ''}`
            }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
