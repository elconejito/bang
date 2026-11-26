<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import LoadingState from '@/components/status/LoadingState.vue';
import ErrorCard from '@/components/status/ErrorCard.vue';
import MagazineModelFields from '@/components/magazines/MagazineModelFields.vue';
import MagazineFormPanel from '@/components/magazines/MagazineFormPanel.vue';
import MagazinePurchaseFields from '@/components/magazines/MagazinePurchaseFields.vue';
import MagazineStorageLocationField from '@/components/magazines/MagazineStorageLocationField.vue';
import { useCalibersStore } from '@/stores/calibers';
import { useColorsStore } from '@/stores/colors';
import { useFirearmsStore } from '@/stores/firearms';
import { useLocationsStore } from '@/stores/locations';
import { useMagazineGroupsStore } from '@/stores/magazineGroups';
import { useOrdersStore } from '@/stores/orders';

const router = useRouter();
const route = useRoute();
const groupsStore = useMagazineGroupsStore();
const calibersStore = useCalibersStore();
const colorsStore = useColorsStore();
const firearmsStore = useFirearmsStore();
const locationsStore = useLocationsStore();
const ordersStore = useOrdersStore();

const calibers = ref([]);
const colors = ref([]);
const firearms = ref([]);
const locations = ref([]);
const orders = ref([]);
const loadingOptions = ref(true);
const optionsError = ref(null);
const saving = ref(false);
const errors = ref({});
const individualDetailsOpen = ref(false);

const form = reactive({
  manufacturer: '',
  model_name: '',
  model_number: '',
  label: '',
  color_id: null,
  capacity: '',
  quantity: 5,
  marking_prefix: '',
  marking_start: '',
  marking_width: '',
  calibers: [],
  firearms: [],
  location_id: null,
  order_id: null,
  cost: '',
  items: [],
});

const firstMarking = computed(() => marking(form.marking_start));
const lastMarking = computed(() => marking(Number(form.marking_start) + Number(form.quantity) - 1));
const rowCount = computed(() => Math.min(100, Math.max(0, Math.trunc(Number(form.quantity) || 0))));
const itemRows = computed(() => form.items.slice(0, rowCount.value));

watch(
  rowCount,
  (quantity) => {
    while (form.items.length < quantity) {
      form.items.push({ serial_number: '', id_marking: '' });
    }
  },
  { immediate: true }
);

const availableFirearms = computed(() => {
  if (form.calibers.length === 0) return firearms.value;

  const selectedCaliberIds = new Set(form.calibers.map(Number));

  return firearms.value.filter((firearm) =>
    firearm.calibers?.some((caliber) => selectedCaliberIds.has(Number(caliber.id)))
  );
});

watch(availableFirearms, (compatibleFirearms) => {
  const compatibleIds = new Set(compatibleFirearms.map((firearm) => Number(firearm.id)));
  form.firearms = form.firearms.filter((firearmId) => compatibleIds.has(Number(firearmId)));
});

function marking(number) {
  if (!form.marking_prefix) return null;
  return `${form.marking_prefix}${String(Math.max(0, Number(number) || 0)).padStart(Number(form.marking_width) || 1, '0')}`;
}

function fieldError(field) {
  return errors.value[field]?.[0] ?? null;
}

const otherErrors = computed(() =>
  Object.entries(errors.value)
    .filter(
      ([field]) =>
        !['manufacturer', 'quantity'].includes(field) &&
        !/^items\.\d+\.(serial_number|id_marking)$/.test(field)
    )
    .flatMap(([, messages]) => messages)
);

async function submit() {
  if (loadingOptions.value || optionsError.value) return;
  saving.value = true;
  errors.value = {};
  try {
    const usesGeneratedMarkings = form.marking_prefix.trim() !== '';

    await groupsStore.createBatch({
      manufacturer: form.manufacturer,
      model_name: form.model_name || null,
      model_number: form.model_number || null,
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
      order_id: form.order_id || null,
      cost: form.cost === '' ? null : Number(form.cost),
      ...(itemRows.value.some((item) => item.serial_number || item.id_marking)
        ? {
            items: itemRows.value.map((item) => ({
              serial_number: item.serial_number || null,
              id_marking: item.id_marking || null,
            })),
          }
        : {}),
    });
    router.push({ name: 'MagazinesIndex' });
  } catch (error) {
    errors.value = error.response?.data?.errors ?? {
      general: [error.response?.data?.message ?? 'The magazines could not be created.'],
    };
    if (Object.keys(errors.value).some((field) => field.startsWith('items.'))) {
      individualDetailsOpen.value = true;
    }
  } finally {
    saving.value = false;
  }
}

async function loadOptions() {
  loadingOptions.value = true;
  optionsError.value = null;
  try {
    const groupResponse = route.query.group
      ? groupsStore
          .fetchGroupMagazines(String(route.query.group), { per_page: 1 })
          .catch(() => null)
      : Promise.resolve(null);
    const [
      caliberResponse,
      colorResponse,
      firearmResponse,
      locationResponse,
      orderResponse,
      sourceGroupResponse,
    ] = await Promise.all([
      calibersStore.fetchAll(),
      colorsStore.fetchAll(),
      firearmsStore.fetchAll(),
      locationsStore.fetchAll(),
      ordersStore.fetchAll(),
      groupResponse,
    ]);
    calibers.value = caliberResponse.data ?? [];
    colors.value = colorResponse.data ?? [];
    firearms.value = firearmResponse.data ?? [];
    locations.value = locationResponse.data ?? [];
    orders.value = orderResponse.data ?? [];

    if (sourceGroupResponse?.group) {
      form.manufacturer = sourceGroupResponse.group.manufacturer ?? '';
      form.model_name = sourceGroupResponse.group.model_name ?? '';
      form.model_number = sourceGroupResponse.group.model_number ?? '';
      form.capacity = sourceGroupResponse.group.capacity ?? '';
      form.calibers = sourceGroupResponse.group.calibers?.map((caliber) => caliber.id) ?? [];
    }
  } catch (error) {
    optionsError.value = error;
  } finally {
    loadingOptions.value = false;
  }
}

onMounted(loadOptions);
</script>

<template>
  <div class="mx-auto max-w-[760px] px-4 py-6 pb-16 sm:px-8">
    <AppBreadcrumb
      :crumbs="[
        { label: 'Home', to: '/' },
        { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
        { label: 'Magazines', to: { name: 'MagazinesIndex' } },
        { label: 'Add Several Magazines' },
      ]"
      class="mb-4"
    />
    <div class="mb-[22px]">
      <h1 class="mb-1 font-display text-[28px] font-bold tracking-[-0.02em] text-ink-900">
        Add Several Magazines
      </h1>
      <p class="text-[15px] text-[#6b7077]">
        Create individually tracked magazines with shared specifications.
      </p>
    </div>

    <LoadingState v-if="loadingOptions" message="Loading magazine options…" />
    <ErrorCard
      v-else-if="optionsError"
      :error="optionsError"
      title="Could not load magazine options"
      retry-label="Try again"
      @retry="loadOptions"
    />
    <MagazineFormPanel
      v-else
      :submit-label="`Create ${form.quantity} magazines`"
      saving-label="Creating…"
      :saving="saving"
      :disabled="loadingOptions"
      @submit="submit"
      @cancel="router.back()"
    >
      <MagazineModelFields
        v-model:manufacturer="form.manufacturer"
        v-model:model-name="form.model_name"
        v-model:model-number="form.model_number"
        v-model:capacity="form.capacity"
        :manufacturer-error="fieldError('manufacturer')"
      />
      <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
        <span>Quantity <span class="text-[#b4452f]">*</span></span>
        <input
          v-model.number="form.quantity"
          type="number"
          min="1"
          max="100"
          required
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 font-normal outline-none placeholder:text-muted focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
        />
        <span v-if="fieldError('quantity')" class="font-normal text-red-700">{{
          fieldError('quantity')
        }}</span>
      </label>
      <MagazineStorageLocationField
        v-model="form.location_id"
        :locations="locations"
        :disabled="loadingOptions"
      />

      <fieldset class="sm:col-span-2">
        <legend class="text-sm font-semibold text-ink-700">Individual records</legend>
        <div class="flex items-center justify-between gap-4">
          <p class="mt-1 text-sm font-normal text-muted">
            Enter a serial number or ID marking for each magazine.
          </p>
          <button
            type="button"
            class="text-[13px] font-semibold text-brass-800 transition-colors hover:text-brass-600"
            :aria-expanded="individualDetailsOpen"
            @click="individualDetailsOpen = !individualDetailsOpen"
          >
            {{ individualDetailsOpen ? 'Hide details' : 'Add details' }}
          </button>
        </div>
        <div v-if="individualDetailsOpen" class="mt-3 overflow-x-auto rounded border border-line">
          <table class="min-w-full text-left text-sm">
            <thead
              class="bg-ink-50 text-[11px] font-semibold uppercase tracking-[0.04em] text-muted"
            >
              <tr>
                <th scope="col" class="px-3 py-2">Magazine</th>
                <th scope="col" class="px-3 py-2">Serial number</th>
                <th scope="col" class="px-3 py-2">ID marking</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in itemRows" :key="index" class="border-t border-line">
                <th scope="row" class="px-3 py-2 font-mono font-normal text-muted">
                  {{ index + 1 }}
                </th>
                <td class="px-3 py-2">
                  <input
                    v-model="item.serial_number"
                    :aria-label="`Serial number for magazine ${index + 1}`"
                    class="w-full rounded border border-[#c2c6ca] bg-white px-2.5 py-2 font-normal outline-none placeholder:text-muted focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
                    placeholder="Optional"
                  />
                  <p
                    v-if="fieldError(`items.${index}.serial_number`)"
                    class="mt-1 text-xs text-red-700"
                  >
                    {{ fieldError(`items.${index}.serial_number`) }}
                  </p>
                </td>
                <td class="px-3 py-2">
                  <input
                    v-model="item.id_marking"
                    :aria-label="`ID marking for magazine ${index + 1}`"
                    class="w-full rounded border border-[#c2c6ca] bg-white px-2.5 py-2 font-normal outline-none placeholder:text-muted focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
                    :placeholder="form.marking_prefix ? 'Generated when blank' : 'Optional'"
                  />
                  <p
                    v-if="fieldError(`items.${index}.id_marking`)"
                    class="mt-1 text-xs text-red-700"
                  >
                    {{ fieldError(`items.${index}.id_marking`) }}
                  </p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </fieldset>

      <fieldset class="sm:col-span-2">
        <legend class="mb-2 text-sm font-semibold text-ink-700">
          Generated markings <span class="font-normal text-ink-400">Optional</span>
        </legend>
        <div class="grid gap-3 sm:grid-cols-[1fr_130px_130px]">
          <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
            Prefix
            <input
              v-model="form.marking_prefix"
              placeholder="e.g. GL9-"
              class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 font-normal outline-none placeholder:text-muted focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
            />
          </label>
          <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
            Start
            <input
              v-model.number="form.marking_start"
              type="number"
              min="0"
              placeholder="e.g. 1"
              class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 font-normal outline-none placeholder:text-muted focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
            />
          </label>
          <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
            Width
            <input
              v-model.number="form.marking_width"
              type="number"
              min="1"
              max="10"
              placeholder="e.g. 3"
              class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 font-normal outline-none placeholder:text-muted focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
            />
          </label>
        </div>
        <p v-if="firstMarking" class="mt-2 font-mono text-xs text-muted">
          Preview: {{ firstMarking }} … {{ lastMarking }}
        </p>
      </fieldset>

      <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
        <span class="inline-flex items-baseline gap-1">
          Nickname / label <span class="font-normal text-ink-400">Optional</span>
        </span>
        <input
          v-model="form.label"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 font-normal outline-none placeholder:text-muted focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
          placeholder="Custom display name"
        />
      </label>
      <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
        <span class="inline-flex items-baseline gap-1">
          Color <span class="font-normal text-ink-400">Optional</span>
        </span>
        <select
          v-model="form.color_id"
          :disabled="loadingOptions"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 font-normal outline-none focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
        >
          <option :value="null">No color selected</option>
          <option v-for="color in colors" :key="color.id" :value="color.id">
            {{ color.label }}
          </option>
        </select>
      </label>

      <MagazinePurchaseFields
        v-model:order-id="form.order_id"
        v-model:cost="form.cost"
        :orders="orders"
      />

      <fieldset class="sm:col-span-2">
        <legend class="mb-2 text-sm font-semibold text-ink-700">Calibers</legend>
        <div class="grid gap-2 sm:grid-cols-2 md:grid-cols-3">
          <label
            v-for="caliber in calibers"
            :key="caliber.id"
            class="flex cursor-pointer items-center gap-2 rounded border border-line px-3 py-2 text-sm"
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

      <fieldset class="sm:col-span-2">
        <legend class="mb-2 text-sm font-semibold text-ink-700">Compatible firearms</legend>
        <div
          v-if="availableFirearms.length"
          class="grid max-h-80 gap-2 overflow-y-auto sm:grid-cols-2"
        >
          <label
            v-for="firearm in availableFirearms"
            :key="firearm.id"
            class="flex cursor-pointer items-center gap-2 rounded border border-line px-3 py-2 text-sm"
          >
            <input
              v-model="form.firearms"
              type="checkbox"
              :value="firearm.id"
              class="h-4 w-4 rounded border-[#c2c6ca] accent-brass"
            />
            {{ [firearm.manufacturer, firearm.label].filter(Boolean).join(' ') }}
          </label>
        </div>
        <p v-else class="text-sm text-muted">
          {{
            form.calibers.length
              ? 'No firearms use the selected caliber.'
              : 'No firearms available.'
          }}
        </p>
      </fieldset>

      <ul v-if="otherErrors.length" role="alert" class="text-sm text-red-700 sm:col-span-2">
        <li v-for="(message, index) in otherErrors" :key="index">{{ message }}</li>
      </ul>
    </MagazineFormPanel>
  </div>
</template>
