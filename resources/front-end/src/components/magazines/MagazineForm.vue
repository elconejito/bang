<script setup>
import { reactive, ref, onMounted } from 'vue';
import { LoaderCircle, Plus } from 'lucide-vue-next';
import { useCalibersStore } from '@/stores/calibers';
import { useColorsStore } from '@/stores/colors';
import { useFirearmsStore } from '@/stores/firearms';
import { useMagazinesStore } from '@/stores/magazines';
import { useQuickAdd } from '@/components/reference/useQuickAdd';
import FormError from '@/components/FormError.vue';
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

    <template v-else>
      <!-- Manufacturer + Model name -->
      <div class="grid grid-cols-2 gap-4">
        <div class="flex flex-col gap-1.5">
          <label class="text-[14px] font-medium"
            >Manufacturer <span class="text-[#b4452f]">*</span></label
          >
          <input
            v-model="form.manufacturer"
            type="text"
            class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
            placeholder="e.g. Magpul"
          />
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-[14px] font-medium">Model name</label>
          <input
            v-model="form.model_name"
            type="text"
            class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
            placeholder="e.g. PMAG GL9"
          />
        </div>
      </div>

      <div class="flex flex-col gap-1.5">
        <label class="text-[14px] font-medium"
          >Capacity <span class="text-[#b4452f]">*</span></label
        >
        <input
          v-model.number="form.capacity"
          type="number"
          min="1"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
          placeholder="e.g. 21"
        />
      </div>

      <!-- ID marking + Serial # -->
      <div class="grid grid-cols-2 gap-4">
        <div class="flex flex-col gap-1.5">
          <label class="text-[14px] font-medium">ID marking</label>
          <input
            v-model="form.id_marking"
            type="text"
            class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
            placeholder="e.g. GL9-01"
          />
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-[14px] font-medium">Serial #</label>
          <input
            v-model="form.serial_number"
            type="text"
            class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
            placeholder="optional"
          />
        </div>
      </div>

      <!-- Label (nickname) -->
      <div class="flex flex-col gap-1.5">
        <label class="text-[14px] font-medium">Nickname / label</label>
        <input
          v-model="form.label"
          type="text"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
          placeholder="optional custom display name"
        />
      </div>

      <!-- Color -->
      <div class="flex flex-col gap-1.5">
        <div class="flex items-center justify-between">
          <label class="text-[14px] font-medium"
            >Color <span class="font-normal text-ink-400">· optional</span></label
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
          v-model="form.color_id"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
        >
          <option :value="null">No color selected</option>
          <option v-for="color in colors" :key="color.id" :value="color.id">
            {{ color.label }}
          </option>
        </select>
      </div>

      <!-- Calibers -->
      <div class="flex flex-col gap-2">
        <div class="flex items-center justify-between">
          <label class="text-[14px] font-medium">Calibers</label>
          <button
            type="button"
            class="inline-flex items-center gap-1 text-[13px] font-semibold text-brass-800 transition-colors hover:text-brass-600"
            @click="openQuickAdd('caliber')"
          >
            <Plus class="h-3.5 w-3.5" /> Add caliber
          </button>
        </div>
        <div v-if="calibers.length" class="grid grid-cols-2 gap-1.5">
          <label
            v-for="c in calibers"
            :key="c.id"
            :for="`cal-${c.id}`"
            class="flex items-center gap-2 text-[14px] cursor-pointer"
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
      </div>

      <!-- Used By (firearms) -->
      <div v-if="firearms.length" class="flex flex-col gap-2">
        <label class="text-[14px] font-medium">Compatible with</label>
        <div class="grid grid-cols-2 gap-1.5">
          <label
            v-for="f in firearms"
            :key="f.id"
            :for="`fir-${f.id}`"
            class="flex items-center gap-2 text-[14px] cursor-pointer"
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
          {{ saving ? 'Saving…' : item ? 'Save changes' : 'Add magazine' }}
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
