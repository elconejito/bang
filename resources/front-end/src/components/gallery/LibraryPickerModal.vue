<script setup>
import { ref, computed, onMounted } from 'vue';
import { Check, LoaderCircle, X } from 'lucide-vue-next';
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
const selected = ref(new Set());
const saving = ref(false);

onMounted(async () => {
  const { data } = await picturesStore.fetchLibrary();
  library.value = data;
  loading.value = false;
});

const isAttached = (id) => props.attachedIds.includes(id);
const isSelected = (id) => selected.value.has(id);

function toggle(id) {
  if (isAttached(id)) return;
  const s = new Set(selected.value);
  s.has(id) ? s.delete(id) : s.add(id);
  selected.value = s;
}

const selectedCount = computed(() => selected.value.size);

async function confirm() {
  if (!selectedCount.value) return;
  saving.value = true;
  try {
    for (const id of selected.value) {
      await picturesStore.attachToEntity(props.entityType, props.entityId, id);
    }
    emit('attach');
  } finally {
    saving.value = false;
  }
}
</script>

<template>
  <div
    class="fixed inset-0 bg-[rgba(20,22,26,0.46)] flex items-start justify-center p-12 z-40 overflow-auto"
    @click.self="emit('close')"
  >
    <div
      class="w-[720px] max-w-full bg-white border border-[#d6d9dc] rounded shadow-[0_10px_30px_rgba(20,22,26,0.22)] overflow-hidden"
    >
      <!-- Header -->
      <div class="flex items-start justify-between gap-3 px-[18px] py-4 border-b border-[#eef0f1]">
        <div>
          <div class="font-display font-semibold text-[19px] leading-tight">Add from Library</div>
          <div class="text-[13px] text-[#8a9098] mt-0.5">Select photos to attach to this item</div>
        </div>
        <button
          class="text-[#8a9098] hover:text-[#1a1c1f] transition-colors p-0.5"
          @click="emit('close')"
        >
          <X class="w-[18px] h-[18px]" />
        </button>
      </div>

      <!-- Library grid -->
      <div class="p-[18px] grid grid-cols-5 gap-2.5 max-h-[360px] overflow-y-auto">
        <div v-if="loading" class="col-span-5 py-8 text-center text-[14px] text-muted">
          Loading…
        </div>
        <div v-else-if="!library.length" class="col-span-5 py-8 text-center text-[14px] text-muted">
          No photos in your library yet.
        </div>

        <div
          v-for="pic in library"
          :key="pic.id"
          class="relative rounded overflow-hidden cursor-pointer"
          :class="
            isAttached(pic.id)
              ? 'border border-[#e2e4e6] opacity-55 cursor-default'
              : isSelected(pic.id)
                ? 'border-2 border-brass'
                : 'border border-[#e2e4e6] hover:border-[#c2c6ca]'
          "
          @click="toggle(pic.id)"
        >
          <img :src="pic.url" :alt="pic.name" class="w-full h-24 object-cover" />

          <!-- Already attached label -->
          <div
            v-if="isAttached(pic.id)"
            class="absolute inset-0 flex items-end justify-center pb-1.5"
          >
            <span
              class="font-mono text-[9px] text-white bg-[rgba(26,28,31,0.8)] rounded-sm px-1.5 py-px"
              >ATTACHED</span
            >
          </div>

          <!-- Selected checkmark -->
          <div
            v-else-if="isSelected(pic.id)"
            class="absolute top-1.5 right-1.5 w-5 h-5 rounded-full bg-brass border border-[#b08a2e] flex items-center justify-center"
          >
            <svg
              class="w-3 h-3 text-[#1a1c1f]"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="3"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M20 6 9 17l-5-5" />
            </svg>
          </div>

          <!-- Unselected circle -->
          <div
            v-else
            class="absolute top-1.5 right-1.5 w-5 h-5 rounded-full bg-[rgba(255,255,255,0.9)] border border-[#c2c6ca]"
          />
        </div>
      </div>

      <!-- Footer -->
      <div class="flex items-center gap-3 px-[18px] py-3.5 border-t border-[#eef0f1] bg-[#fafbfb]">
        <span class="text-[13px] text-[#6b7077]">
          <b class="text-[#1a1c1f]">{{ selectedCount }}</b> selected
        </span>
        <div class="ml-auto flex items-center gap-2.5">
          <button
            class="font-semibold text-[14px] bg-white text-[#3a3e44] px-4 py-2 border border-[#c2c6ca] rounded hover:bg-[#f5f6f7] transition-colors"
            @click="emit('close')"
          >
            Cancel
          </button>
          <button
            :disabled="!selectedCount || saving"
            class="inline-flex items-center gap-1.5 font-semibold text-[14px] bg-brass text-[#1a1c1f] px-4 py-2 border border-[#b08a2e] rounded hover:bg-[#b8902f] disabled:opacity-50 transition-colors"
            @click="confirm"
          >
            <LoaderCircle v-if="saving" class="h-[15px] w-[15px] animate-spin" />
            <Check v-else class="h-[15px] w-[15px]" />
            {{
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
