<script setup>
import { reactive, ref, onMounted } from 'vue';
import { LoaderCircle, Plus } from 'lucide-vue-next';
import { useFirearmsStore } from '@/stores/firearms';
import { useLocationsStore } from '@/stores/locations';
import { useSuppressorsStore } from '@/stores/suppressors';
import { useOpticsStore } from '@/stores/optics';
import { useLightsStore } from '@/stores/lights';
import { useMiscAccessoriesStore } from '@/stores/miscAccessories';
import { useQuickAdd } from '@/components/reference/useQuickAdd';
import { axiosInstance } from '@/plugins/axios';
import FormError from '@/components/FormError.vue';
import ReferenceItemModal from '@/components/reference/ReferenceItemModal.vue';

const props = defineProps({
  type: { type: String, required: true }, // suppressor | optic | light | misc
  item: { type: Object, default: null },
});

const emit = defineEmits(['complete', 'cancel']);

const suppressorsStore = useSuppressorsStore();
const opticsStore = useOpticsStore();
const lightsStore = useLightsStore();
const miscStore = useMiscAccessoriesStore();
const firearmsStore = useFirearmsStore();
const locationsStore = useLocationsStore();
const { quickAddType, openQuickAdd, closeQuickAdd } = useQuickAdd();

const firearms = ref([]);
const locations = ref([]);
const calibers = ref([]);
const loading = ref(true);
const saving = ref(false);
const submitError = ref(null);

const OPTIC_TYPES = [
  { value: 'red_dot', label: 'Red dot' },
  { value: 'prism', label: 'Prism' },
  { value: 'lpvo', label: 'LPVO' },
  { value: 'variable', label: 'Variable' },
];

const form = reactive({
  manufacturer: props.item?.manufacturer ?? '',
  label: props.item?.label ?? '',
  serial: props.item?.serial ?? '',
  firearm_id: props.item?.firearm_id ?? null,
  location_id: props.item?.location_id ?? null,
  purchase_date: props.item?.purchase_date ?? '',
  purchase_price: props.item?.purchase_price ?? '',
  purchase_store_id: props.item?.purchase_store_id ?? null,
  // suppressor
  caliber_id: props.item?.caliber_id ?? null,
  is_nfa: props.item?.is_nfa ?? true,
  mount_type: props.item?.mount_type ?? '',
  length: props.item?.length ?? '',
  weight: props.item?.weight ?? '',
  nfa_form_type: props.item?.nfa_form_type ?? '',
  nfa_approved_date: props.item?.nfa_approved_date ?? '',
  nfa_trust: props.item?.nfa_trust ?? '',
  // optic
  optic_type: props.item?.optic_type ?? null,
  battery_type: props.item?.battery_type ?? '',
  // light
  lumens: props.item?.lumens ?? '',
  // misc
  sub_type: props.item?.sub_type ?? '',
});

onMounted(async () => {
  const fetches = [
    firearmsStore.fetchAll().then((d) => (firearms.value = d.data)),
    locationsStore.fetchAll().then((d) => (locations.value = d.data)),
  ];
  if (props.type === 'suppressor') {
    fetches.push(axiosInstance.get('/calibers').then(({ data }) => (calibers.value = data.data)));
  }
  await Promise.all(fetches);
  loading.value = false;
});

function onQuickAddSaved(item) {
  if (quickAddType.value === 'caliber') {
    calibers.value.push(item);
    form.caliber_id = item.id;
  } else if (quickAddType.value === 'location') {
    locations.value.push(item);
    form.location_id = item.id;
  }
  closeQuickAdd();
}

async function submit() {
  saving.value = true;
  submitError.value = null;
  try {
    const payload = buildPayload();
    let result;
    if (props.item) {
      result = await getStore().update(props.item.id, payload);
    } else {
      result = await getStore().create(payload);
    }
    emit('complete', result.data);
  } catch (e) {
    submitError.value = e.response?.data?.message ?? 'Something went wrong.';
  } finally {
    saving.value = false;
  }
}

function getStore() {
  if (props.type === 'suppressor') return suppressorsStore;
  if (props.type === 'optic') return opticsStore;
  if (props.type === 'light') return lightsStore;
  return miscStore;
}

function buildPayload() {
  const base = {
    manufacturer: form.manufacturer,
    label: form.label,
    serial: form.serial || null,
    firearm_id: form.firearm_id || null,
    location_id: form.location_id || null,
    purchase_date: form.purchase_date || null,
    purchase_price: form.purchase_price || null,
    purchase_store_id: form.purchase_store_id || null,
  };
  if (props.type === 'suppressor') {
    return {
      ...base,
      caliber_id: form.caliber_id || null,
      is_nfa: form.is_nfa,
      mount_type: form.mount_type || null,
      length: form.length || null,
      weight: form.weight || null,
      nfa_form_type: form.nfa_form_type || null,
      nfa_approved_date: form.nfa_approved_date || null,
      nfa_trust: form.nfa_trust || null,
    };
  }
  if (props.type === 'optic') {
    return {
      ...base,
      optic_type: form.optic_type || null,
      battery_type: form.battery_type || null,
    };
  }
  if (props.type === 'light') {
    return { ...base, lumens: form.lumens || null, battery_type: form.battery_type || null };
  }
  return { ...base, sub_type: form.sub_type || null };
}
</script>

<template>
  <div class="flex flex-col gap-5">
    <FormError v-if="submitError" :error="submitError" />

    <div v-if="loading" class="text-sm text-muted py-6 text-center">Loading…</div>

    <template v-else>
      <!-- Manufacturer + Label -->
      <div class="grid grid-cols-2 gap-4">
        <div class="flex flex-col gap-1.5">
          <label class="text-[14px] font-medium"
            >Manufacturer <span class="text-[#b4452f]">*</span></label
          >
          <input
            v-model="form.manufacturer"
            type="text"
            class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
            placeholder="e.g. SilencerCo"
          />
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-[14px] font-medium"
            >Label / Model <span class="text-[#b4452f]">*</span></label
          >
          <input
            v-model="form.label"
            type="text"
            class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
            placeholder="e.g. Omega 9K"
          />
        </div>
      </div>

      <!-- Serial -->
      <div class="flex flex-col gap-1.5">
        <label class="text-[14px] font-medium">Serial #</label>
        <input
          v-model="form.serial"
          type="text"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
          placeholder="optional"
        />
      </div>

      <!-- Suppressor-specific -->
      <template v-if="type === 'suppressor'">
        <div class="grid grid-cols-2 gap-4">
          <div class="flex flex-col gap-1.5">
            <div class="flex items-center justify-between">
              <label class="text-[14px] font-medium">Caliber</label>
              <button
                type="button"
                class="inline-flex items-center gap-1 text-[13px] font-semibold text-brass-800 transition-colors hover:text-brass-600"
                @click="openQuickAdd('caliber')"
              >
                <Plus class="h-3.5 w-3.5" /> Add caliber
              </button>
            </div>
            <select
              v-model="form.caliber_id"
              class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
            >
              <option :value="null">— optional —</option>
              <option v-for="c in calibers" :key="c.id" :value="c.id">{{ c.label }}</option>
            </select>
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-[14px] font-medium">Mount type</label>
            <input
              v-model="form.mount_type"
              type="text"
              class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
              placeholder="e.g. 1/2×28, tri-lug"
            />
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="text-[14px] font-medium">Length (in)</label>
            <input
              v-model.number="form.length"
              type="number"
              step="0.01"
              min="0"
              class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
              placeholder="e.g. 4.7"
            />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-[14px] font-medium">Weight (oz)</label>
            <input
              v-model.number="form.weight"
              type="number"
              step="0.01"
              min="0"
              class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
              placeholder="e.g. 9.6"
            />
          </div>
        </div>
        <div class="flex items-center gap-2.5">
          <input
            id="is_nfa"
            v-model="form.is_nfa"
            type="checkbox"
            class="h-4 w-4 rounded border-[#c2c6ca] accent-brass"
          />
          <label for="is_nfa" class="text-[14px] font-medium">NFA item</label>
        </div>
        <template v-if="form.is_nfa">
          <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-1.5">
              <label class="text-[14px] font-medium">NFA form type</label>
              <input
                v-model="form.nfa_form_type"
                type="text"
                class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
                placeholder="e.g. Form 4"
              />
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="text-[14px] font-medium">Approved date</label>
              <input
                v-model="form.nfa_approved_date"
                type="date"
                class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
              />
            </div>
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-[14px] font-medium">Trust name</label>
            <input
              v-model="form.nfa_trust"
              type="text"
              class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
              placeholder="e.g. Harvey Family Trust"
            />
          </div>
        </template>
      </template>

      <!-- Optic-specific -->
      <template v-else-if="type === 'optic'">
        <div class="grid grid-cols-2 gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="text-[14px] font-medium">Optic type</label>
            <select
              v-model="form.optic_type"
              class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
            >
              <option :value="null">— optional —</option>
              <option v-for="o in OPTIC_TYPES" :key="o.value" :value="o.value">
                {{ o.label }}
              </option>
            </select>
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-[14px] font-medium">Battery type</label>
            <input
              v-model="form.battery_type"
              type="text"
              class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
              placeholder="e.g. CR2032"
            />
          </div>
        </div>
      </template>

      <!-- Light-specific -->
      <template v-else-if="type === 'light'">
        <div class="grid grid-cols-2 gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="text-[14px] font-medium">Lumens</label>
            <input
              v-model.number="form.lumens"
              type="number"
              min="0"
              class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
              placeholder="e.g. 500"
            />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-[14px] font-medium">Battery type</label>
            <input
              v-model="form.battery_type"
              type="text"
              class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
              placeholder="e.g. CR123"
            />
          </div>
        </div>
      </template>

      <!-- Misc-specific -->
      <template v-else-if="type === 'misc'">
        <div class="flex flex-col gap-1.5">
          <label class="text-[14px] font-medium">Sub-type</label>
          <input
            v-model="form.sub_type"
            type="text"
            class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
            placeholder="e.g. sling, holster, stock"
          />
        </div>
      </template>

      <!-- Mounted on firearm -->
      <div class="flex flex-col gap-1.5">
        <label class="text-[14px] font-medium">Mounted on</label>
        <select
          v-model="form.firearm_id"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
        >
          <option :value="null">— unmounted —</option>
          <option v-for="f in firearms" :key="f.id" :value="f.id">
            {{ f.manufacturer }} {{ f.label }}
          </option>
        </select>
      </div>

      <!-- Storage location (when not mounted) -->
      <div v-if="!form.firearm_id" class="flex flex-col gap-1.5">
        <div class="flex items-center justify-between">
          <label class="text-[14px] font-medium">Storage location</label>
          <button
            type="button"
            class="inline-flex items-center gap-1 text-[13px] font-semibold text-brass-800 transition-colors hover:text-brass-600"
            @click="openQuickAdd('location')"
          >
            <Plus class="h-3.5 w-3.5" /> Add location
          </button>
        </div>
        <select
          v-model="form.location_id"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
        >
          <option :value="null">— optional —</option>
          <option v-for="l in locations" :key="l.id" :value="l.id">{{ l.label }}</option>
        </select>
      </div>

      <!-- Purchase info -->
      <div class="grid grid-cols-2 gap-4">
        <div class="flex flex-col gap-1.5">
          <label class="text-[14px] font-medium">Purchase date</label>
          <input
            v-model="form.purchase_date"
            type="date"
            class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
          />
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-[14px] font-medium">Purchase price ($)</label>
          <input
            v-model.number="form.purchase_price"
            type="number"
            min="0"
            step="0.01"
            class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
            placeholder="0.00"
          />
        </div>
      </div>

      <!-- Actions -->
      <div class="flex items-center gap-3 pt-2">
        <button
          type="button"
          :disabled="saving"
          class="flex-1 flex items-center justify-center gap-2 bg-brass text-[#1a1c1f] font-semibold text-[14px] px-5 py-[10px] rounded border border-[#b08a2e] hover:bg-[#b8902f] disabled:opacity-60 transition-colors"
          @click="submit"
        >
          <LoaderCircle v-if="saving" class="h-4 w-4 animate-spin" />
          {{ saving ? 'Saving…' : item ? 'Save changes' : 'Add accessory' }}
        </button>
        <button
          type="button"
          class="px-5 py-[10px] text-[14px] text-[#5b6066] hover:text-[#1a1c1f] transition-colors"
          @click="emit('cancel')"
        >
          Cancel
        </button>
      </div>
    </template>

    <ReferenceItemModal
      v-if="quickAddType"
      :type="quickAddType"
      mode="add"
      @close="closeQuickAdd"
      @saved="onQuickAddSaved"
    />
  </div>
</template>
