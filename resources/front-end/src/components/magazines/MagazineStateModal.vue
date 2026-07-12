<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { LoaderCircle, X } from 'lucide-vue-next';
import { useAmmunitionStore } from '@/stores/ammunition';
import { useLocationsStore } from '@/stores/locations';
import { useMagazinesStore } from '@/stores/magazines';
import { useMagazineGroupsStore } from '@/stores/magazineGroups';

const props = defineProps({ magazine: { type: Object, required: true } });
const emit = defineEmits(['close', 'saved']);
const ammunitionStore = useAmmunitionStore();
const locationsStore = useLocationsStore();
const magazinesStore = useMagazinesStore();
const groupsStore = useMagazineGroupsStore();

const details = ref(null);
const ammunition = ref([]);
const locations = ref([]);
const loading = ref(true);
const saving = ref(false);
const loadError = ref(false);
const errors = ref({});
const placement = ref('unassigned');
const locationId = ref('');
const firearmId = ref('');
const ammunitionId = ref('');
const loadedRounds = ref(0);

const compatibleAmmunition = computed(() => {
  const caliberIds = new Set(details.value?.calibers?.map((caliber) => Number(caliber.id)) ?? []);
  return ammunition.value.filter((item) => caliberIds.has(Number(item.caliber_id)));
});

function fieldError(field) {
  return errors.value[field]?.[0] ?? null;
}

function setInitialState(magazine) {
  placement.value = magazine.current_firearm
    ? 'firearm'
    : magazine.location
      ? 'location'
      : 'unassigned';
  firearmId.value = magazine.current_firearm?.id ? String(magazine.current_firearm.id) : '';
  locationId.value = magazine.location?.id ? String(magazine.location.id) : '';
  ammunitionId.value = magazine.loaded_ammunition_id ? String(magazine.loaded_ammunition_id) : '';
  loadedRounds.value = Number(magazine.loaded_rounds ?? 0);
}

watch(loadedRounds, (rounds) => {
  if (Number(rounds) === 0) ammunitionId.value = '';
});

async function load() {
  try {
    const [magazineResponse, ammunitionResponse, locationResponse] = await Promise.all([
      magazinesStore.fetchOne(props.magazine.id),
      ammunitionStore.fetchAll(),
      locationsStore.fetchAll(),
    ]);
    details.value = magazineResponse.data;
    ammunition.value = ammunitionResponse.data ?? [];
    locations.value = locationResponse.data ?? [];
    setInitialState(details.value);
  } catch {
    loadError.value = true;
  } finally {
    loading.value = false;
  }
}

async function submit() {
  saving.value = true;
  errors.value = {};
  try {
    await groupsStore.changeMagazineState(props.magazine.id, {
      location_id:
        placement.value === 'location' && locationId.value ? Number(locationId.value) : null,
      current_firearm_id:
        placement.value === 'firearm' && firearmId.value ? Number(firearmId.value) : null,
      loaded_ammunition_id:
        Number(loadedRounds.value) > 0 && ammunitionId.value ? Number(ammunitionId.value) : null,
      loaded_rounds: Number(loadedRounds.value),
    });
    emit('saved');
  } catch (error) {
    errors.value = error.response?.data?.errors ?? {
      general: [error.response?.data?.message ?? 'The magazine could not be updated.'],
    };
  } finally {
    saving.value = false;
  }
}

onMounted(load);
</script>

<template>
  <div
    class="fixed inset-0 z-50 flex items-start justify-center overflow-auto bg-black/50 p-4 sm:p-12"
    @click.self="emit('close')"
  >
    <section class="w-full max-w-xl overflow-hidden rounded border border-line bg-white shadow-xl">
      <header class="flex items-center justify-between gap-4 border-b border-line px-5 py-4">
        <div>
          <h2 class="font-display text-lg font-semibold text-ink-900">Manage magazine</h2>
          <p class="font-mono text-xs text-muted">{{ magazine.id_marking || 'No marking' }}</p>
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

      <div class="flex flex-col gap-5 p-5">
        <div v-if="loading" class="py-10 text-center text-sm text-muted">Loading magazine...</div>
        <p
          v-else-if="loadError"
          class="rounded border border-red-200 bg-red-50 p-3 text-sm text-red-800"
        >
          The magazine details could not be loaded. Close this dialog and try again.
        </p>
        <template v-else>
          <fieldset class="flex flex-col gap-3">
            <legend class="mb-2 text-sm font-semibold text-ink-900">Where is it?</legend>
            <div class="grid gap-2 sm:grid-cols-3">
              <label
                v-for="option in [
                  { value: 'unassigned', label: 'Unassigned' },
                  { value: 'location', label: 'Stored' },
                  { value: 'firearm', label: 'In firearm' },
                ]"
                :key="option.value"
                class="flex cursor-pointer items-center gap-2 rounded border border-line px-3 py-2.5 text-sm text-ink-700 has-checked:border-brass-700 has-checked:bg-[#f4ecd6]"
              >
                <input v-model="placement" type="radio" name="placement" :value="option.value" />
                {{ option.label }}
              </label>
            </div>

            <div v-if="placement === 'location'">
              <label for="magazine-location" class="mb-1.5 block text-sm font-semibold text-ink-700"
                >Storage location</label
              >
              <select
                id="magazine-location"
                v-model="locationId"
                class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
              >
                <option value="">Select a location</option>
                <option
                  v-for="location in locations"
                  :key="location.id"
                  :value="String(location.id)"
                >
                  {{ location.label }}
                </option>
              </select>
              <p v-if="fieldError('location_id')" class="mt-1 text-sm text-red-700">
                {{ fieldError('location_id') }}
              </p>
            </div>

            <div v-if="placement === 'firearm'">
              <label for="magazine-firearm" class="mb-1.5 block text-sm font-semibold text-ink-700"
                >Compatible firearm</label
              >
              <select
                id="magazine-firearm"
                v-model="firearmId"
                class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
              >
                <option value="">Select a firearm</option>
                <option
                  v-for="firearm in details.firearms"
                  :key="firearm.id"
                  :value="String(firearm.id)"
                >
                  {{ [firearm.manufacturer, firearm.label].filter(Boolean).join(' ') }}
                </option>
              </select>
              <p v-if="fieldError('current_firearm_id')" class="mt-1 text-sm text-red-700">
                {{ fieldError('current_firearm_id') }}
              </p>
            </div>
          </fieldset>

          <fieldset class="flex flex-col gap-3 border-t border-line pt-5">
            <legend class="text-sm font-semibold text-ink-900">Magazine contents</legend>
            <div class="grid gap-3 sm:grid-cols-[1fr_140px]">
              <div>
                <label
                  for="magazine-ammunition"
                  class="mb-1.5 block text-sm font-semibold text-ink-700"
                  >Loaded with</label
                >
                <select
                  id="magazine-ammunition"
                  v-model="ammunitionId"
                  class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
                >
                  <option value="">Select ammunition</option>
                  <option
                    v-for="item in compatibleAmmunition"
                    :key="item.id"
                    :value="String(item.id)"
                  >
                    {{ [item.manufacturer, item.label].filter(Boolean).join(' ') }}
                  </option>
                </select>
                <p v-if="fieldError('loaded_ammunition_id')" class="mt-1 text-sm text-red-700">
                  {{ fieldError('loaded_ammunition_id') }}
                </p>
              </div>
              <div>
                <label for="magazine-rounds" class="mb-1.5 block text-sm font-semibold text-ink-700"
                  >Rounds loaded</label
                >
                <input
                  id="magazine-rounds"
                  v-model.number="loadedRounds"
                  type="number"
                  min="0"
                  :max="details.capacity"
                  class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-sm outline-none focus:border-brass-700"
                />
                <p v-if="fieldError('loaded_rounds')" class="mt-1 text-sm text-red-700">
                  {{ fieldError('loaded_rounds') }}
                </p>
              </div>
            </div>
            <p
              class="rounded border border-[#e3d3a3] bg-[#faf6e9] px-3 py-2 text-xs text-[#6c571e]"
            >
              Tracking magazine contents does not change ammunition inventory.
            </p>
          </fieldset>

          <p
            v-if="fieldError('general')"
            class="rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800"
          >
            {{ fieldError('general') }}
          </p>
        </template>
      </div>

      <footer class="flex items-center justify-end gap-2 border-t border-line bg-ink-50 px-5 py-4">
        <button
          type="button"
          class="rounded border border-line bg-white px-4 py-2 text-sm font-semibold text-ink-700 hover:bg-ink-50"
          @click="emit('close')"
        >
          Cancel
        </button>
        <button
          type="button"
          :disabled="loading || loadError || saving"
          class="inline-flex items-center gap-2 rounded border border-[#b08a2e] bg-brass px-4 py-2 text-sm font-semibold text-ink-900 hover:bg-[#b8902f] disabled:opacity-50"
          @click="submit"
        >
          <LoaderCircle v-if="saving" class="h-4 w-4 animate-spin" />
          {{ saving ? 'Saving...' : 'Save state' }}
        </button>
      </footer>
    </section>
  </div>
</template>
