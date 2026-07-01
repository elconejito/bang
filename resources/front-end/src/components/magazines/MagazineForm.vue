<script setup>
import { reactive, ref, onMounted } from 'vue';
import { LoaderCircle } from 'lucide-vue-next';
import { useCalibersStore } from '@/stores/calibers';
import { useFirearmsStore } from '@/stores/firearms';
import { useMagazinesStore } from '@/stores/magazines';
import { useAmmunitionStore } from '@/stores/ammunition';
import FormError from '@/components/FormError.vue';

const props = defineProps({
  item: { type: Object, default: null },
});

const emit = defineEmits(['complete', 'cancel']);

const calibersStore = useCalibersStore();
const firearmsStore = useFirearmsStore();
const magazinesStore = useMagazinesStore();
const ammunitionStore = useAmmunitionStore();

const calibers = ref([]);
const firearms = ref([]);
const ammunition = ref([]);
const loading = ref(true);
const saving = ref(false);
const submitError = ref(null);

const STATUS_OPTIONS = [
  { value: 'empty', label: 'Empty' },
  { value: 'loaded', label: 'Loaded' },
  { value: 'in_gun', label: 'In gun' },
];

const form = reactive({
  manufacturer: props.item?.manufacturer ?? '',
  model_name: props.item?.model_name ?? '',
  label: props.item?.label ?? '',
  capacity: props.item?.capacity ?? '',
  serial_number: props.item?.serial_number ?? '',
  id_marking: props.item?.id_marking ?? '',
  status: props.item?.status ?? 'empty',
  loaded_ammunition_id: props.item?.loaded_ammunition_id ?? '',
  calibers: props.item?.calibers?.map((c) => c.id) ?? [],
  firearms: props.item?.firearms?.map((f) => f.id) ?? [],
});

onMounted(async () => {
  const [calibersRes, firearmsRes, ammoRes] = await Promise.all([
    calibersStore.fetchAll(),
    firearmsStore.fetchAll(),
    ammunitionStore.fetchAll(),
  ]);
  calibers.value = calibersRes.data;
  firearms.value = firearmsRes.data;
  ammunition.value = ammoRes.data;
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
      status: form.status,
      loaded_ammunition_id: form.status === 'loaded' ? form.loaded_ammunition_id || null : null,
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
</script>

<template>
  <div class="flex flex-col gap-5">
    <FormError v-if="submitError" :error="submitError" />

    <div v-if="loading" class="py-6 text-center text-sm text-muted">Loading…</div>

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

      <!-- Capacity + Status -->
      <div class="grid grid-cols-2 gap-4">
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
        <div class="flex flex-col gap-1.5">
          <label class="text-[14px] font-medium">Status</label>
          <select
            v-model="form.status"
            class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
          >
            <option v-for="opt in STATUS_OPTIONS" :key="opt.value" :value="opt.value">
              {{ opt.label }}
            </option>
          </select>
        </div>
      </div>

      <!-- Loaded ammo (conditional) -->
      <div v-if="form.status === 'loaded'" class="flex flex-col gap-1.5">
        <label class="text-[14px] font-medium">Loaded with</label>
        <select
          v-model="form.loaded_ammunition_id"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
        >
          <option value="">— Select ammo —</option>
          <option v-for="ammo in ammunition" :key="ammo.id" :value="ammo.id">
            {{ ammo.manufacturer }} {{ ammo.label }}
          </option>
        </select>
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

      <!-- Calibers -->
      <div v-if="calibers.length" class="flex flex-col gap-2">
        <label class="text-[14px] font-medium">Calibers</label>
        <div class="grid grid-cols-2 gap-1.5">
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
  </div>
</template>
