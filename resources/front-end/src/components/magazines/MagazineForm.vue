<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { Plus } from 'lucide-vue-next';
import { useCalibersStore } from '@/stores/calibers';
import { useColorsStore } from '@/stores/colors';
import { useFirearmsStore } from '@/stores/firearms';
import { useMagazinesStore } from '@/stores/magazines';
import { useQuickAdd } from '@/components/reference/useQuickAdd';
import FormError from '@/components/FormError.vue';
import MagazineFormPanel from '@/components/magazines/MagazineFormPanel.vue';
import ReferenceItemModal from '@/components/reference/ReferenceItemModal.vue';

const props = defineProps({
  item: { type: Object, default: null },
  defaults: { type: Object, default: null },
});

const emit = defineEmits(['complete', 'cancel']);

const calibersStore = useCalibersStore();
const colorsStore = useColorsStore();
const firearmsStore = useFirearmsStore();
const magazinesStore = useMagazinesStore();
const { quickAddType, openQuickAdd, closeQuickAdd } = useQuickAdd();

const calibers = ref([]);
const colors = ref([]);
const firearms = ref([]);
const loading = ref(true);
const saving = ref(false);
const submitError = ref(null);

const form = reactive({
  manufacturer: props.item?.manufacturer ?? props.defaults?.manufacturer ?? '',
  model_name: props.item?.model_name ?? props.defaults?.model_name ?? '',
  model_number: props.item?.model_number ?? props.defaults?.model_number ?? '',
  label: props.item?.label ?? props.defaults?.label ?? '',
  capacity: props.item?.capacity ?? props.defaults?.capacity ?? '',
  serial_number: props.item?.serial_number ?? props.defaults?.serial_number ?? '',
  id_marking: props.item?.id_marking ?? props.defaults?.id_marking ?? '',
  color_id: props.item?.color_id ?? props.defaults?.color_id ?? null,
  calibers:
    props.item?.calibers?.map((caliber) => caliber.id) ??
    props.defaults?.calibers?.map((caliber) => caliber.id) ??
    [],
  firearms:
    props.item?.firearms?.map((firearm) => firearm.id) ??
    props.defaults?.firearms?.map((firearm) => firearm.id) ??
    [],
});

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

onMounted(async () => {
  const [calibersRes, firearmsRes, colorsRes] = await Promise.all([
    calibersStore.fetchAll(),
    firearmsStore.fetchAll(),
    colorsStore.fetchAll(),
  ]);
  calibers.value = calibersRes.data;
  firearms.value = firearmsRes.data;
  colors.value = colorsRes.data;
  loading.value = false;
});

async function submit() {
  saving.value = true;
  submitError.value = null;
  try {
    const payload = {
      manufacturer: form.manufacturer,
      model_name: form.model_name || null,
      model_number: form.model_number || null,
      label: form.label || null,
      capacity: Number(form.capacity),
      serial_number: form.serial_number || null,
      id_marking: form.id_marking || null,
      color_id: form.color_id || null,
      calibers: form.calibers,
      firearms: form.firearms,
    };
    let result;
    if (props.item) {
      result = await magazinesStore.update(props.item.id, payload);
    } else {
      result = await magazinesStore.create(payload);
    }
    emit('complete', result.data);
  } catch (e) {
    submitError.value = e.response?.data?.message ?? 'Something went wrong.';
  } finally {
    saving.value = false;
  }
}

function onQuickAddSaved(item) {
  if (quickAddType.value === 'color') {
    colors.value.push(item);
    form.color_id = item.id;
  } else {
    calibers.value.push(item);
    if (!form.calibers.includes(item.id)) {
      form.calibers.push(item.id);
    }
  }
  closeQuickAdd();
}
</script>

<template>
  <div class="flex flex-col gap-5">
    <FormError v-if="submitError" :error="submitError" />

    <LoadingState v-if="loading" message="Loading magazine options…" />

    <MagazineFormPanel
      v-else
      :submit-label="item ? 'Save changes' : 'Add magazine'"
      :saving="saving"
      @submit="submit"
      @cancel="emit('cancel')"
    >
      <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
        <span>Manufacturer <span class="text-[#b4452f]">*</span></span>
        <input
          v-model="form.manufacturer"
          type="text"
          required
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 font-normal outline-none placeholder:text-muted focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
          placeholder="e.g. Magpul"
        />
      </label>

      <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
        <span>Model name</span>
        <input
          v-model="form.model_name"
          type="text"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 font-normal outline-none placeholder:text-muted focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
          placeholder="e.g. PMAG GL9"
        />
      </label>

      <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
        <span class="inline-flex items-baseline gap-1">
          Model # <span class="font-normal text-ink-400">Optional</span>
        </span>
        <input
          v-model="form.model_number"
          type="text"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 font-normal outline-none placeholder:text-muted focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
          placeholder="Manufacturer model number"
        />
      </label>

      <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
        <span>Capacity <span class="text-[#b4452f]">*</span></span>
        <input
          v-model.number="form.capacity"
          type="number"
          min="1"
          required
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 font-normal outline-none placeholder:text-muted focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
          placeholder="e.g. 21"
        />
      </label>

      <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
        <span class="inline-flex items-baseline gap-1">
          Nickname / label <span class="font-normal text-ink-400">Optional</span>
        </span>
        <input
          v-model="form.label"
          type="text"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 font-normal outline-none placeholder:text-muted focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
          placeholder="Custom display name"
        />
      </label>

      <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
        <span class="inline-flex items-baseline gap-1">
          ID marking <span class="font-normal text-ink-400">Optional</span>
        </span>
        <input
          v-model="form.id_marking"
          type="text"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 font-normal outline-none placeholder:text-muted focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
          placeholder="e.g. GL9-01"
        />
      </label>

      <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
        <span class="inline-flex items-baseline gap-1">
          Serial number <span class="font-normal text-ink-400">Optional</span>
        </span>
        <input
          v-model="form.serial_number"
          type="text"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 font-normal outline-none placeholder:text-muted focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
          placeholder="Manufacturer serial number"
        />
      </label>

      <div class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700 sm:col-span-2">
        <div class="flex items-center justify-between">
          <label for="magazine-color"
            >Color <span class="font-normal text-ink-400">Optional</span></label
          >
          <button
            type="button"
            class="text-[13px] font-semibold text-brass-800 transition-colors hover:text-brass-600"
            @click="openQuickAdd('color')"
          >
            + Add color
          </button>
        </div>
        <select
          id="magazine-color"
          v-model="form.color_id"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 font-normal outline-none focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
        >
          <option :value="null">No color selected</option>
          <option v-for="color in colors" :key="color.id" :value="color.id">
            {{ color.label }}
          </option>
        </select>
      </div>

      <fieldset class="sm:col-span-2">
        <legend class="sr-only">Calibers</legend>
        <div class="flex items-center justify-between">
          <span class="text-sm font-semibold text-ink-700">Calibers</span>
          <button
            type="button"
            class="inline-flex items-center gap-1 text-[13px] font-semibold text-brass-800 transition-colors hover:text-brass-600"
            @click="openQuickAdd('caliber')"
          >
            <Plus class="h-3.5 w-3.5" /> Add caliber
          </button>
        </div>
        <div v-if="calibers.length" class="mt-2 grid gap-2 sm:grid-cols-2 md:grid-cols-3">
          <label
            v-for="c in calibers"
            :key="c.id"
            :for="`cal-${c.id}`"
            class="flex cursor-pointer items-center gap-2 rounded border border-line px-3 py-2 text-sm"
          >
            <input
              :id="`cal-${c.id}`"
              v-model="form.calibers"
              type="checkbox"
              :value="c.id"
              class="h-4 w-4 rounded border-[#c2c6ca] accent-brass"
            />
            {{ c.label }}
          </label>
        </div>
        <p v-else class="mt-2 text-sm text-muted">No calibers available.</p>
      </fieldset>

      <fieldset class="sm:col-span-2">
        <legend class="mb-2 text-sm font-semibold text-ink-700">Compatible firearms</legend>
        <div
          v-if="availableFirearms.length"
          class="grid max-h-80 gap-2 overflow-y-auto sm:grid-cols-2"
        >
          <label
            v-for="f in availableFirearms"
            :key="f.id"
            :for="`fir-${f.id}`"
            class="flex cursor-pointer items-center gap-2 rounded border border-line px-3 py-2 text-sm"
          >
            <input
              :id="`fir-${f.id}`"
              v-model="form.firearms"
              type="checkbox"
              :value="f.id"
              class="h-4 w-4 rounded border-[#c2c6ca] accent-brass"
            />
            {{ f.manufacturer }} {{ f.label }}
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
    </MagazineFormPanel>

    <ReferenceItemModal
      v-if="quickAddType"
      :type="quickAddType"
      mode="add"
      @close="closeQuickAdd"
      @saved="onQuickAddSaved"
    />
  </div>
</template>
