<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { LoaderCircle, X } from 'lucide-vue-next';
import { useAmmunitionStore } from '@/stores/ammunition';
import { useCalibersStore } from '@/stores/calibers';
import { useColorsStore } from '@/stores/colors';
import { useFirearmsStore } from '@/stores/firearms';

const props = defineProps({
  magazines: { type: Array, required: true },
  locations: { type: Array, default: () => [] },
  saving: { type: Boolean, default: false },
  serverError: { type: String, default: '' },
});
const emit = defineEmits(['close', 'save']);

const ammunitionStore = useAmmunitionStore();
const calibersStore = useCalibersStore();
const colorsStore = useColorsStore();
const firearmsStore = useFirearmsStore();

const loading = ref(true);
const loadError = ref(false);
const validationError = ref('');
const ammunition = ref([]);
const calibers = ref([]);
const colors = ref([]);
const firearms = ref([]);
const placement = ref('unassigned');
const locationId = ref('');
const contents = ref('empty');
const ammunitionId = ref('');
const loadedRounds = ref(0);

const apply = reactive({
  manufacturer: false,
  model_name: false,
  label: false,
  color_id: false,
  capacity: false,
  calibers: false,
  firearms: false,
  placement: false,
  contents: false,
});
const form = reactive({
  manufacturer: '',
  model_name: '',
  label: '',
  color_id: '',
  capacity: '',
  calibers: [],
  firearms: [],
});

const selectedFirearmCount = computed(
  () => props.magazines.filter((magazine) => magazine.current_firearm).length
);
const minimumCapacity = computed(() => {
  const capacities = props.magazines.map((magazine) => Number(magazine.capacity)).filter(Boolean);
  return capacities.length ? Math.min(...capacities) : Infinity;
});
const effectiveCapacity = computed(() =>
  apply.capacity ? Number(form.capacity) : minimumCapacity.value
);
const selectedCaliberIds = computed(() => form.calibers.map((id) => Number(id)));
const selectedAmmunition = computed(() =>
  ammunition.value.find((item) => Number(item.id) === Number(ammunitionId.value))
);

function optionLabel(item) {
  return [item.manufacturer, item.label].filter(Boolean).join(' ') || item.name || `#${item.id}`;
}

function fieldId(name) {
  return `bulk-magazine-${name}`;
}

function buildChanges() {
  const changes = {};

  if (apply.manufacturer) changes.manufacturer = form.manufacturer.trim();
  if (apply.model_name) changes.model_name = form.model_name.trim() || null;
  if (apply.label) changes.label = form.label.trim() || null;
  if (apply.color_id) changes.color_id = form.color_id ? Number(form.color_id) : null;
  if (apply.capacity) changes.capacity = Number(form.capacity);
  if (apply.calibers) changes.calibers = selectedCaliberIds.value;
  if (apply.firearms) changes.firearms = form.firearms.map((id) => Number(id));
  if (apply.placement) {
    changes.location_id =
      placement.value === 'location' && locationId.value ? Number(locationId.value) : null;
  }
  if (apply.contents) {
    changes.loaded_ammunition_id = contents.value === 'loaded' ? Number(ammunitionId.value) : null;
    changes.loaded_rounds = contents.value === 'loaded' ? Number(loadedRounds.value) : 0;
  }

  return changes;
}

function validate() {
  validationError.value = '';
  if (!Object.values(apply).some(Boolean)) {
    validationError.value = 'Choose at least one field to apply.';
    return false;
  }
  if (apply.manufacturer && !form.manufacturer.trim()) {
    validationError.value = 'Manufacturer cannot be empty.';
    return false;
  }
  if (apply.capacity && (!Number.isInteger(Number(form.capacity)) || Number(form.capacity) < 1)) {
    validationError.value = 'Capacity must be a whole number greater than zero.';
    return false;
  }
  if (apply.placement && placement.value === 'location' && !locationId.value) {
    validationError.value = 'Choose a storage location.';
    return false;
  }
  if (apply.contents && contents.value === 'loaded') {
    const rounds = Number(loadedRounds.value);
    if (!ammunitionId.value || !Number.isInteger(rounds) || rounds < 1) {
      validationError.value = 'Loaded contents need ammunition and at least one round.';
      return false;
    }
    if (rounds > effectiveCapacity.value) {
      validationError.value = `Loaded rounds cannot exceed the effective capacity of ${effectiveCapacity.value} rounds.`;
      return false;
    }
    if (
      apply.calibers &&
      selectedAmmunition.value &&
      !selectedCaliberIds.value.includes(Number(selectedAmmunition.value.caliber_id))
    ) {
      validationError.value =
        'The selected ammunition caliber must be included in the applied calibers.';
      return false;
    }
  }

  return true;
}

function submit() {
  if (validate()) emit('save', buildChanges());
}

onMounted(async () => {
  try {
    const [ammunitionResponse, calibersResponse, colorsResponse, firearmsResponse] =
      await Promise.all([
        ammunitionStore.fetchAll(),
        calibersStore.fetchAll(),
        colorsStore.fetchAll(),
        firearmsStore.fetchAll(),
      ]);
    ammunition.value = ammunitionResponse.data ?? [];
    calibers.value = calibersResponse.data ?? [];
    colors.value = colorsResponse.data ?? [];
    firearms.value = firearmsResponse.data ?? [];
  } catch {
    loadError.value = true;
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <div class="modal-scrim z-50 p-4 sm:p-8" @click.self="emit('close')">
    <section class="modal-shell w-full max-w-3xl">
      <header class="flex items-start justify-between gap-4 border-b border-line px-5 py-4">
        <div>
          <h2 class="font-display text-lg font-semibold text-ink-900">Bulk edit magazines</h2>
          <p class="mt-1 text-sm text-muted">
            Applying changes to {{ magazines.length }} selected magazines.
          </p>
        </div>
        <button
          type="button"
          class="p-1 text-muted hover:text-ink-900"
          aria-label="Close"
          @click="emit('close')"
        >
          <X class="h-5 w-5" />
        </button>
      </header>

      <div class="max-h-[min(72vh,720px)] overflow-y-auto p-5">
        <div v-if="loading" class="py-10 text-center text-sm text-muted">Loading edit options…</div>
        <p
          v-else-if="loadError"
          class="rounded border border-red-200 bg-red-50 p-3 text-sm text-red-800"
          role="alert"
        >
          The edit options could not be loaded. Close this dialog and try again.
        </p>
        <form v-else class="flex flex-col gap-5" @submit.prevent="submit">
          <p class="rounded border border-[#e3d3a3] bg-[#faf6e9] px-3 py-2 text-xs text-[#6c571e]">
            Check Apply beside each field you want to change. Unchecked fields stay as they are on
            every selected magazine.
          </p>

          <div class="grid gap-4 sm:grid-cols-2">
            <fieldset class="flex flex-col gap-2">
              <label
                :for="fieldId('manufacturer-apply')"
                class="flex items-center gap-2 text-sm font-semibold text-ink-900"
              >
                <input
                  :id="fieldId('manufacturer-apply')"
                  v-model="apply.manufacturer"
                  type="checkbox"
                  class="h-4 w-4 rounded border-[#c2c6ca] accent-brass"
                />
                Apply manufacturer
              </label>
              <input
                v-if="apply.manufacturer"
                v-model="form.manufacturer"
                type="text"
                placeholder="e.g. Magpul"
                class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
              />
            </fieldset>
            <fieldset class="flex flex-col gap-2">
              <label
                :for="fieldId('model-apply')"
                class="flex items-center gap-2 text-sm font-semibold text-ink-900"
              >
                <input
                  :id="fieldId('model-apply')"
                  v-model="apply.model_name"
                  type="checkbox"
                  class="h-4 w-4 rounded border-[#c2c6ca] accent-brass"
                />
                Apply model name
              </label>
              <input
                v-if="apply.model_name"
                v-model="form.model_name"
                type="text"
                placeholder="Blank clears model name"
                class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
              />
            </fieldset>
            <fieldset class="flex flex-col gap-2">
              <label
                :for="fieldId('label-apply')"
                class="flex items-center gap-2 text-sm font-semibold text-ink-900"
              >
                <input
                  :id="fieldId('label-apply')"
                  v-model="apply.label"
                  type="checkbox"
                  class="h-4 w-4 rounded border-[#c2c6ca] accent-brass"
                />
                Apply nickname
              </label>
              <input
                v-if="apply.label"
                v-model="form.label"
                type="text"
                placeholder="Blank clears nickname"
                class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
              />
            </fieldset>
            <fieldset class="flex flex-col gap-2">
              <label
                :for="fieldId('color-apply')"
                class="flex items-center gap-2 text-sm font-semibold text-ink-900"
              >
                <input
                  :id="fieldId('color-apply')"
                  v-model="apply.color_id"
                  type="checkbox"
                  class="h-4 w-4 rounded border-[#c2c6ca] accent-brass"
                />
                Apply color
              </label>
              <select
                v-if="apply.color_id"
                v-model="form.color_id"
                class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
              >
                <option value="">No color selected</option>
                <option v-for="color in colors" :key="color.id" :value="String(color.id)">
                  {{ color.label }}
                </option>
              </select>
            </fieldset>
            <fieldset class="flex flex-col gap-2">
              <label
                :for="fieldId('capacity-apply')"
                class="flex items-center gap-2 text-sm font-semibold text-ink-900"
              >
                <input
                  :id="fieldId('capacity-apply')"
                  v-model="apply.capacity"
                  type="checkbox"
                  class="h-4 w-4 rounded border-[#c2c6ca] accent-brass"
                />
                Apply capacity
              </label>
              <input
                v-if="apply.capacity"
                v-model.number="form.capacity"
                type="number"
                min="1"
                placeholder="e.g. 17"
                class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
              />
            </fieldset>
          </div>

          <fieldset class="flex flex-col gap-3 border-t border-line pt-5">
            <legend class="text-sm font-semibold text-ink-900">
              <label :for="fieldId('calibers-apply')" class="flex items-center gap-2">
                <input
                  :id="fieldId('calibers-apply')"
                  v-model="apply.calibers"
                  type="checkbox"
                  class="h-4 w-4 rounded border-[#c2c6ca] accent-brass"
                />
                Apply calibers
              </label>
            </legend>
            <div v-if="apply.calibers" class="grid max-h-40 gap-2 overflow-y-auto sm:grid-cols-2">
              <label
                v-for="caliber in calibers"
                :key="caliber.id"
                class="flex items-center gap-2 text-sm text-ink-700"
              >
                <input
                  v-model="form.calibers"
                  type="checkbox"
                  :value="caliber.id"
                  class="h-4 w-4 rounded border-[#c2c6ca] accent-brass"
                />
                {{ caliber.label }}
              </label>
            </div>
          </fieldset>

          <fieldset class="flex flex-col gap-3 border-t border-line pt-5">
            <legend class="text-sm font-semibold text-ink-900">
              <label :for="fieldId('firearms-apply')" class="flex items-center gap-2">
                <input
                  :id="fieldId('firearms-apply')"
                  v-model="apply.firearms"
                  type="checkbox"
                  class="h-4 w-4 rounded border-[#c2c6ca] accent-brass"
                />
                Apply compatible firearms
              </label>
            </legend>
            <div v-if="apply.firearms" class="grid max-h-40 gap-2 overflow-y-auto sm:grid-cols-2">
              <label
                v-for="firearm in firearms"
                :key="firearm.id"
                class="flex items-center gap-2 text-sm text-ink-700"
              >
                <input
                  v-model="form.firearms"
                  type="checkbox"
                  :value="firearm.id"
                  class="h-4 w-4 rounded border-[#c2c6ca] accent-brass"
                />
                {{ optionLabel(firearm) }}
              </label>
            </div>
          </fieldset>

          <fieldset class="flex flex-col gap-3 border-t border-line pt-5">
            <legend class="text-sm font-semibold text-ink-900">
              <label :for="fieldId('placement-apply')" class="flex items-center gap-2">
                <input
                  :id="fieldId('placement-apply')"
                  v-model="apply.placement"
                  type="checkbox"
                  class="h-4 w-4 rounded border-[#c2c6ca] accent-brass"
                />
                Apply placement
              </label>
            </legend>
            <div v-if="apply.placement" class="grid gap-2 sm:grid-cols-2">
              <label
                class="flex cursor-pointer items-center gap-2 rounded border border-line px-3 py-2.5 text-sm has-checked:border-brass-700 has-checked:bg-[#f4ecd6]"
              >
                <input v-model="placement" name="bulk-placement" type="radio" value="unassigned" />
                Unassigned
              </label>
              <label
                class="flex cursor-pointer items-center gap-2 rounded border border-line px-3 py-2.5 text-sm has-checked:border-brass-700 has-checked:bg-[#f4ecd6]"
              >
                <input v-model="placement" name="bulk-placement" type="radio" value="location" />
                Stored at a location
              </label>
            </div>
            <select
              v-if="apply.placement && placement === 'location'"
              id="bulk-magazine-location"
              v-model="locationId"
              class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
            >
              <option value="">Select a location</option>
              <option v-for="location in locations" :key="location.id" :value="String(location.id)">
                {{ location.full_label ?? location.label }}
              </option>
            </select>
            <p
              v-if="apply.placement && selectedFirearmCount"
              class="rounded border border-[#e3d3a3] bg-[#faf6e9] px-3 py-2 text-xs text-[#6c571e]"
            >
              This will eject {{ selectedFirearmCount }} selected magazine{{
                selectedFirearmCount === 1 ? '' : 's'
              }}
              from firearms. Bulk edit never assigns magazines to a firearm.
            </p>
          </fieldset>

          <fieldset class="flex flex-col gap-3 border-t border-line pt-5">
            <legend class="text-sm font-semibold text-ink-900">
              <label :for="fieldId('contents-apply')" class="flex items-center gap-2">
                <input
                  :id="fieldId('contents-apply')"
                  v-model="apply.contents"
                  type="checkbox"
                  class="h-4 w-4 rounded border-[#c2c6ca] accent-brass"
                />
                Apply contents
              </label>
            </legend>
            <div v-if="apply.contents" class="grid gap-2 sm:grid-cols-2">
              <label
                class="flex cursor-pointer items-center gap-2 rounded border border-line px-3 py-2.5 text-sm has-checked:border-brass-700 has-checked:bg-[#f4ecd6]"
              >
                <input v-model="contents" name="bulk-contents" type="radio" value="empty" />
                Empty
              </label>
              <label
                class="flex cursor-pointer items-center gap-2 rounded border border-line px-3 py-2.5 text-sm has-checked:border-brass-700 has-checked:bg-[#f4ecd6]"
              >
                <input v-model="contents" name="bulk-contents" type="radio" value="loaded" />
                Loaded
              </label>
            </div>
            <div
              v-if="apply.contents && contents === 'loaded'"
              class="grid gap-3 sm:grid-cols-[1fr_140px]"
            >
              <select
                id="bulk-magazine-ammunition"
                v-model="ammunitionId"
                class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
              >
                <option value="">Select ammunition</option>
                <option v-for="item in ammunition" :key="item.id" :value="String(item.id)">
                  {{ optionLabel(item) }}
                </option>
              </select>
              <input
                id="bulk-magazine-rounds"
                v-model.number="loadedRounds"
                type="number"
                min="1"
                :max="effectiveCapacity"
                placeholder="Rounds"
                class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
              />
            </div>
          </fieldset>

          <p
            v-if="validationError || serverError"
            class="rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800"
            role="alert"
          >
            {{ validationError || serverError }}
          </p>
        </form>
      </div>

      <footer class="flex items-center justify-end gap-2 border-t border-line bg-ink-50 px-5 py-4">
        <button
          type="button"
          class="rounded border border-line bg-white px-4 py-2 text-sm font-semibold text-ink-700 hover:bg-ink-50"
          :disabled="saving"
          @click="emit('close')"
        >
          Cancel
        </button>
        <button
          type="button"
          data-testid="bulk-magazine-submit"
          class="inline-flex items-center gap-2 rounded border border-[#b08a2e] bg-brass px-4 py-2 text-sm font-semibold text-ink-900 hover:bg-[#b8902f] disabled:opacity-50"
          :disabled="loading || loadError || saving"
          @click="submit"
        >
          <LoaderCircle v-if="saving" class="h-4 w-4 animate-spin" />
          {{ saving ? 'Saving…' : 'Apply changes' }}
        </button>
      </footer>
    </section>
  </div>
</template>
