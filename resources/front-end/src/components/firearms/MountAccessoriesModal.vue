<script setup>
import { computed, onMounted, ref } from 'vue';
import { LoaderCircle, Plus, Search, X } from 'lucide-vue-next';
import { useFirearmsStore } from '@/stores/firearms';

const props = defineProps({
  firearmId: { type: Number, required: true },
});

const emit = defineEmits(['mounted', 'add-new', 'close']);
const firearmsStore = useFirearmsStore();
const items = ref([]);
const selected = ref([]);
const search = ref('');
const loading = ref(true);
const saving = ref(false);
const error = ref(null);

const filtered = computed(() => {
  const term = search.value.trim().toLowerCase();
  return items.value.filter(
    (item) => !term || `${item.label} ${item.subtitle}`.toLowerCase().includes(term)
  );
});

const groups = computed(() => {
  return ['Suppressor', 'Optic', 'Light', 'Misc']
    .map((type) => ({ type, items: filtered.value.filter((item) => item.type === type) }))
    .filter((group) => group.items.length);
});

function key(item) {
  return `${item.type}:${item.id}`;
}

function isSelected(item) {
  return selected.value.some((selectedItem) => key(selectedItem) === key(item));
}

function toggle(item) {
  selected.value = isSelected(item)
    ? selected.value.filter((selectedItem) => key(selectedItem) !== key(item))
    : [...selected.value, { type: item.type, id: item.id }];
}

async function load() {
  loading.value = true;
  error.value = null;
  try {
    const { data } = await firearmsStore.fetchMountableAccessories(props.firearmId);
    items.value = data ?? [];
  } catch {
    error.value = 'Unable to load mountable accessories.';
  } finally {
    loading.value = false;
  }
}

async function mount() {
  saving.value = true;
  error.value = null;
  try {
    await firearmsStore.mountAccessories(props.firearmId, selected.value);
    emit('mounted');
  } catch (exception) {
    error.value = exception.response?.data?.message ?? 'Unable to mount the selected accessories.';
  } finally {
    saving.value = false;
  }
}

onMounted(load);
</script>

<template>
  <div class="modal-scrim z-40 p-4 sm:p-12" @click.self="emit('close')">
    <div class="modal-shell w-[620px] max-w-full">
      <div class="flex items-center justify-between gap-3 border-b border-[#eef0f1] px-[18px] py-4">
        <div>
          <div class="font-display text-[18px] font-semibold">Mount accessories</div>
          <p class="mt-0.5 text-[13px] text-muted">
            Choose unmounted accessories to attach to this firearm.
          </p>
        </div>
        <button
          class="p-0.5 text-[#8a9098] transition-colors hover:text-[#1a1c1f]"
          @click="emit('close')"
        >
          <X class="h-[18px] w-[18px]" />
        </button>
      </div>

      <div class="flex max-h-[60vh] flex-col gap-4 overflow-y-auto px-[18px] py-4">
        <div class="flex items-center gap-2 rounded border border-[#c2c6ca] bg-white px-3 py-2">
          <Search class="h-4 w-4 flex-none text-muted" />
          <input
            v-model="search"
            class="min-w-0 flex-1 text-[14px] outline-none"
            placeholder="Search accessories"
          />
        </div>

        <div v-if="loading" class="flex items-center justify-center py-10 text-[14px] text-muted">
          <LoaderCircle class="mr-2 h-4 w-4 animate-spin" /> Loading accessories…
        </div>
        <div
          v-else-if="error"
          class="rounded border border-[#e3b5aa] bg-[#fdf1ef] px-3 py-2 text-[13px] text-[#a33f2c]"
        >
          {{ error }}
          <button class="ml-2 font-semibold underline" @click="load">Try again</button>
        </div>
        <template v-else-if="groups.length">
          <section v-for="group in groups" :key="group.type">
            <h3 class="mb-2 font-mono text-[10px] tracking-[0.06em] text-muted">
              {{ group.type.toUpperCase() }}S
            </h3>
            <div class="flex flex-col gap-1">
              <label
                v-for="item in group.items"
                :key="key(item)"
                class="flex cursor-pointer items-center gap-3 rounded border border-[#e2e4e6] px-3 py-2.5 hover:bg-[#fafbfb]"
              >
                <input
                  type="checkbox"
                  :checked="isSelected(item)"
                  class="h-4 w-4 accent-[#7d6320]"
                  @change="toggle(item)"
                />
                <span class="min-w-0 flex-1">
                  <span class="block truncate text-[14px] font-medium text-ink-900">{{
                    item.label
                  }}</span>
                  <span class="block truncate text-[12px] text-muted">{{ item.subtitle }}</span>
                </span>
              </label>
            </div>
          </section>
        </template>
        <div v-else class="py-7 text-center">
          <p class="text-[14px] font-medium text-ink-700">No unmounted accessories available</p>
          <p class="mt-1 text-[13px] text-muted">Add an accessory, then return here to mount it.</p>
        </div>
      </div>

      <div
        class="flex flex-wrap items-center justify-between gap-3 border-t border-[#eef0f1] bg-[#fafbfb] px-[18px] py-3.5"
      >
        <button
          class="inline-flex items-center gap-1.5 text-[14px] font-semibold text-[#7d6320] hover:text-[#5f4b18]"
          @click="emit('add-new')"
        >
          <Plus class="h-4 w-4" /> Add new accessory
        </button>
        <div class="flex items-center gap-2.5">
          <button
            class="rounded border border-[#c2c6ca] bg-white px-4 py-2 text-[14px] font-semibold text-[#3a3e44] transition-colors hover:bg-[#f5f6f7]"
            @click="emit('close')"
          >
            Cancel
          </button>
          <button
            :disabled="saving || !selected.length"
            class="inline-flex items-center gap-2 rounded border border-[#b08a2e] bg-brass px-4 py-2 text-[14px] font-semibold text-[#1a1c1f] transition-colors hover:bg-[#b8902f] disabled:opacity-50"
            @click="mount"
          >
            <LoaderCircle v-if="saving" class="h-4 w-4 animate-spin" />
            Mount selected{{ selected.length ? ` (${selected.length})` : '' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
