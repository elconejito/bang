<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import { useCalibersStore } from '@/stores/calibers';
import { useColorsStore } from '@/stores/colors';
import { useFirearmsStore } from '@/stores/firearms';
import { useLocationsStore } from '@/stores/locations';
import { useMagazineGroupsStore } from '@/stores/magazineGroups';

const router = useRouter();
const route = useRoute();
const groupsStore = useMagazineGroupsStore();
const calibersStore = useCalibersStore();
const colorsStore = useColorsStore();
const firearmsStore = useFirearmsStore();
const locationsStore = useLocationsStore();

const calibers = ref([]);
const colors = ref([]);
const firearms = ref([]);
const locations = ref([]);
const loadingOptions = ref(true);
const saving = ref(false);
const errors = ref({});

const form = reactive({
  manufacturer: '',
  model_name: '',
  label: '',
  color_id: null,
  capacity: 17,
  quantity: 5,
  marking_prefix: '',
  marking_start: '',
  marking_width: '',
  calibers: [],
  firearms: [],
  location_id: '',
});

const firstMarking = computed(() => marking(form.marking_start));
const lastMarking = computed(() => marking(Number(form.marking_start) + Number(form.quantity) - 1));

function marking(number) {
  if (!form.marking_prefix) return null;
  return `${form.marking_prefix}${String(Math.max(0, Number(number) || 0)).padStart(Number(form.marking_width) || 1, '0')}`;
}

function fieldError(field) {
  return errors.value[field]?.[0] ?? null;
}

async function submit() {
  saving.value = true;
  errors.value = {};
  try {
    const usesGeneratedMarkings = form.marking_prefix.trim() !== '';

    await groupsStore.createBatch({
      manufacturer: form.manufacturer,
      model_name: form.model_name || null,
      label: form.label || null,
      color_id: form.color_id || null,
      capacity: Number(form.capacity),
      quantity: Number(form.quantity),
      marking_prefix: form.marking_prefix || null,
      marking_start: usesGeneratedMarkings ? Number(form.marking_start) : null,
      marking_width: usesGeneratedMarkings ? Number(form.marking_width) : null,
      calibers: form.calibers,
      firearms: form.firearms,
      location_id: form.location_id ? Number(form.location_id) : null,
    });
    router.push({ name: 'MagazinesIndex' });
  } catch (error) {
    errors.value = error.response?.data?.errors ?? {
      general: [error.response?.data?.message ?? 'The magazines could not be created.'],
    };
  } finally {
    saving.value = false;
  }
}

onMounted(async () => {
  try {
    const groupResponse = route.query.group
      ? groupsStore
          .fetchGroupMagazines(String(route.query.group), { per_page: 1 })
          .catch(() => null)
      : Promise.resolve(null);
    const [caliberResponse, colorResponse, firearmResponse, locationResponse, sourceGroupResponse] =
      await Promise.all([
        calibersStore.fetchAll(),
        colorsStore.fetchAll(),
        firearmsStore.fetchAll(),
        locationsStore.fetchAll(),
        groupResponse,
      ]);
    calibers.value = caliberResponse.data ?? [];
    colors.value = colorResponse.data ?? [];
    firearms.value = firearmResponse.data ?? [];
    locations.value = locationResponse.data ?? [];

    if (sourceGroupResponse?.group) {
      form.manufacturer = sourceGroupResponse.group.manufacturer ?? '';
      form.model_name = sourceGroupResponse.group.model_name ?? '';
      form.capacity = sourceGroupResponse.group.capacity ?? 17;
      form.calibers = sourceGroupResponse.group.calibers?.map((caliber) => caliber.id) ?? [];
    }
  } finally {
    loadingOptions.value = false;
  }
});
</script>

<template>
  <div class="mx-auto max-w-[760px] px-4 py-6 pb-16 sm:px-8">
    <AppBreadcrumb
      :crumbs="[
        { label: 'Home', to: '/' },
        { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
        { label: 'Magazines', to: { name: 'MagazinesIndex' } },
        { label: 'Add several' },
      ]"
      class="mb-4"
    />
    <div class="mb-6">
      <h1 class="font-display text-[28px] font-bold tracking-[-0.02em] text-ink-900">
        Add several magazines
      </h1>
      <p class="mt-1 text-sm text-muted">
        Create individually tracked magazines with shared specifications.
      </p>
    </div>

    <form class="overflow-hidden rounded border border-line bg-white" @submit.prevent="submit">
      <div class="grid gap-5 p-5 sm:grid-cols-2">
        <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
          Manufacturer
          <input
            v-model="form.manufacturer"
            required
            class="rounded border border-[#c2c6ca] px-3 py-2.5 font-normal outline-none focus:border-brass"
          />
          <span v-if="fieldError('manufacturer')" class="font-normal text-red-700">{{
            fieldError('manufacturer')
          }}</span>
        </label>
        <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
          Model
          <input
            v-model="form.model_name"
            class="rounded border border-[#c2c6ca] px-3 py-2.5 font-normal outline-none focus:border-brass"
          />
        </label>
        <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
          Capacity
          <input
            v-model.number="form.capacity"
            type="number"
            min="1"
            required
            class="rounded border border-[#c2c6ca] px-3 py-2.5 font-normal outline-none focus:border-brass"
          />
        </label>
        <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
          Quantity
          <input
            v-model.number="form.quantity"
            type="number"
            min="1"
            max="100"
            required
            class="rounded border border-[#c2c6ca] px-3 py-2.5 font-normal outline-none focus:border-brass"
          />
          <span v-if="fieldError('quantity')" class="font-normal text-red-700">{{
            fieldError('quantity')
          }}</span>
        </label>

        <fieldset class="sm:col-span-2">
          <legend class="mb-2 text-sm font-semibold text-ink-700">
            Generated markings (optional)
          </legend>
          <div class="grid gap-3 sm:grid-cols-[1fr_130px_130px]">
            <label class="flex flex-col gap-1.5 text-xs font-semibold text-ink-700">
              Prefix
              <input
                v-model="form.marking_prefix"
                placeholder="e.g. GL9-"
                class="rounded border border-[#c2c6ca] px-3 py-2.5 text-sm font-normal outline-none focus:border-brass"
              />
            </label>
            <label class="flex flex-col gap-1.5 text-xs font-semibold text-ink-700">
              Start
              <input
                v-model.number="form.marking_start"
                type="number"
                min="0"
                placeholder="e.g. 1"
                class="rounded border border-[#c2c6ca] px-3 py-2.5 text-sm font-normal outline-none focus:border-brass"
              />
            </label>
            <label class="flex flex-col gap-1.5 text-xs font-semibold text-ink-700">
              Width
              <input
                v-model.number="form.marking_width"
                type="number"
                min="1"
                max="10"
                placeholder="e.g. 3"
                class="rounded border border-[#c2c6ca] px-3 py-2.5 text-sm font-normal outline-none focus:border-brass"
              />
            </label>
          </div>
          <p v-if="firstMarking" class="mt-2 font-mono text-xs text-muted">
            Preview: {{ firstMarking }} … {{ lastMarking }}
          </p>
        </fieldset>

        <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
          Storage location
          <select
            v-model="form.location_id"
            :disabled="loadingOptions"
            class="rounded border border-[#c2c6ca] bg-white px-3 py-2.5 font-normal outline-none focus:border-brass"
          >
            <option value="">Unassigned</option>
            <option v-for="location in locations" :key="location.id" :value="String(location.id)">
              {{ location.full_label ?? location.label }}
            </option>
          </select>
        </label>
        <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
          Nickname / label (Optional)
          <input
            v-model="form.label"
            class="rounded border border-[#c2c6ca] px-3 py-2.5 font-normal outline-none focus:border-brass"
          />
        </label>
        <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
          Color (Optional)
          <select
            v-model="form.color_id"
            :disabled="loadingOptions"
            class="rounded border border-[#c2c6ca] bg-white px-3 py-2.5 font-normal outline-none focus:border-brass"
          >
            <option :value="null">No color selected</option>
            <option v-for="color in colors" :key="color.id" :value="color.id">
              {{ color.label }}
            </option>
          </select>
        </label>

        <fieldset class="sm:col-span-2">
          <legend class="mb-2 text-sm font-semibold text-ink-700">Calibers</legend>
          <div class="grid gap-2 sm:grid-cols-2 md:grid-cols-3">
            <label
              v-for="caliber in calibers"
              :key="caliber.id"
              class="flex items-center gap-2 rounded border border-line px-3 py-2 text-sm"
            >
              <input v-model="form.calibers" type="checkbox" :value="caliber.id" />
              {{ caliber.label }}
            </label>
          </div>
        </fieldset>

        <fieldset class="sm:col-span-2">
          <legend class="mb-2 text-sm font-semibold text-ink-700">Compatible firearms</legend>
          <div class="grid max-h-48 gap-2 overflow-auto sm:grid-cols-2">
            <label
              v-for="firearm in firearms"
              :key="firearm.id"
              class="flex items-center gap-2 rounded border border-line px-3 py-2 text-sm"
            >
              <input v-model="form.firearms" type="checkbox" :value="firearm.id" />
              {{ [firearm.manufacturer, firearm.label].filter(Boolean).join(' ') }}
            </label>
          </div>
        </fieldset>

        <p v-if="fieldError('general')" class="text-sm text-red-700 sm:col-span-2">
          {{ fieldError('general') }}
        </p>
      </div>
      <footer class="flex justify-end gap-2 border-t border-line bg-ink-50 px-5 py-4">
        <button
          type="button"
          class="rounded border border-line bg-white px-4 py-2 text-sm font-semibold text-ink-700"
          @click="router.back()"
        >
          Cancel
        </button>
        <button
          type="submit"
          :disabled="saving || loadingOptions"
          class="rounded border border-[#b08a2e] bg-brass px-4 py-2 text-sm font-semibold text-ink-900 disabled:opacity-50"
        >
          {{ saving ? 'Creating…' : `Create ${form.quantity} magazines` }}
        </button>
      </footer>
    </form>
  </div>
</template>
