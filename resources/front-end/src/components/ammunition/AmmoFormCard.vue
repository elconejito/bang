<template>
  <div class="flex flex-col gap-5">
    <FormError v-if="submitError" :error="submitError" />

    <!-- Caliber -->
    <div class="flex flex-col gap-1.5">
      <label class="text-[14px] font-medium">Caliber <span class="text-[#b4452f]">*</span></label>
      <select
        v-model="form.caliber_id"
        class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
        :disabled="!!ammo"
      >
        <option :value="null">Select caliber…</option>
        <option v-for="c in calibers" :key="c.id" :value="c.id">{{ c.label }}</option>
      </select>
    </div>

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
          placeholder="e.g. Federal"
        />
      </div>
      <div class="flex flex-col gap-1.5">
        <label class="text-[14px] font-medium"
          >Load name <span class="text-[#b4452f]">*</span></label
        >
        <input
          v-model="form.label"
          type="text"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
          placeholder="e.g. American Eagle 115gr FMJ"
        />
      </div>
    </div>

    <!-- Purpose + Weight -->
    <div class="grid grid-cols-2 gap-4">
      <div class="flex flex-col gap-1.5">
        <label class="text-[14px] font-medium">Purpose</label>
        <select
          v-model="form.purpose_id"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
        >
          <option :value="null">— optional —</option>
          <option v-for="p in purposes" :key="p.id" :value="p.id">{{ p.label }}</option>
        </select>
      </div>
      <div class="flex flex-col gap-1.5">
        <label class="text-[14px] font-medium">Weight (gr)</label>
        <input
          v-model.number="form.weight"
          type="number"
          min="1"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
          placeholder="e.g. 115"
        />
      </div>
    </div>

    <!-- Reorder thresholds -->
    <div class="grid grid-cols-2 gap-4">
      <div class="flex flex-col gap-1.5">
        <label class="text-[14px] font-medium">Reorder at (rounds)</label>
        <input
          v-model.number="form.reorder_min"
          type="number"
          min="0"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
          placeholder="e.g. 500"
        />
        <p class="text-[12px] text-muted">LOW badge on card when at or below.</p>
      </div>
      <div class="flex flex-col gap-1.5">
        <label class="text-[14px] font-medium">Target (rounds)</label>
        <input
          v-model.number="form.reorder_target"
          type="number"
          min="0"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
          placeholder="e.g. 1,000"
        />
        <p class="text-[12px] text-muted">Enables the stock progress bar.</p>
      </div>
    </div>

    <!-- Bullet / Casing -->
    <div class="grid grid-cols-2 gap-4">
      <div class="flex flex-col gap-1.5">
        <label class="text-[14px] font-medium">Bullet type</label>
        <select
          v-model="form.bullet_type_id"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
        >
          <option :value="null">— optional —</option>
          <option v-for="b in bulletTypes" :key="b.id" :value="b.id">{{ b.label }}</option>
        </select>
      </div>
      <div class="flex flex-col gap-1.5">
        <label class="text-[14px] font-medium">Casing</label>
        <select
          v-model="form.ammunition_casing_id"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
        >
          <option :value="null">— optional —</option>
          <option v-for="c in casings" :key="c.id" :value="c.id">{{ c.label }}</option>
        </select>
      </div>
    </div>

    <!-- Primer / Condition -->
    <div class="grid grid-cols-2 gap-4">
      <div class="flex flex-col gap-1.5">
        <label class="text-[14px] font-medium">Primer</label>
        <select
          v-model="form.primer_type_id"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
        >
          <option :value="null">— optional —</option>
          <option v-for="p in primerTypes" :key="p.id" :value="p.id">{{ p.label }}</option>
        </select>
      </div>
      <div class="flex flex-col gap-1.5">
        <label class="text-[14px] font-medium">Condition</label>
        <select
          v-model="form.ammunition_condition_id"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
        >
          <option :value="null">— optional —</option>
          <option v-for="c in conditions" :key="c.id" :value="c.id">{{ c.label }}</option>
        </select>
      </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center gap-3 border-t border-line pt-5">
      <button
        class="inline-flex items-center gap-[7px] rounded border border-[#b08a2e] bg-brass px-[15px] py-2 text-[14px] font-semibold text-ink-900 transition-colors hover:bg-brass-600 disabled:opacity-50"
        :disabled="saving"
        @click="handleSubmit"
      >
        <LoaderCircle v-if="saving" class="h-4 w-4 animate-spin" />
        {{ ammo ? 'Save changes' : 'Add load' }}
      </button>
      <button
        class="rounded border border-[#c2c6ca] bg-white px-[15px] py-2 text-[14px] font-semibold text-ink-700 transition-colors hover:bg-[#f5f6f7]"
        @click="$emit('cancel')"
      >
        Cancel
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { LoaderCircle } from 'lucide-vue-next';
import { axiosInstance } from '@/plugins/axios';
import { useAmmunitionStore } from '@/stores/ammunition';
import FormError from '@/components/FormError.vue';

const props = defineProps({
  ammo: { type: Object, default: null },
  preselectedCaliberId: { type: Number, default: null },
});

const emit = defineEmits(['complete', 'cancel']);

const ammunitionStore = useAmmunitionStore();

const saving = ref(false);
const submitError = ref(null);

const calibers = ref([]);
const purposes = ref([]);
const bulletTypes = ref([]);
const casings = ref([]);
const primerTypes = ref([]);
const conditions = ref([]);

const form = ref({
  caliber_id: props.ammo?.caliber_id ?? props.preselectedCaliberId ?? null,
  manufacturer: props.ammo?.manufacturer ?? '',
  label: props.ammo?.label ?? '',
  purpose_id: props.ammo?.purpose_id ?? null,
  weight: props.ammo?.weight ?? null,
  reorder_min: props.ammo?.reorder_min ?? null,
  reorder_target: props.ammo?.reorder_target ?? null,
  bullet_type_id: props.ammo?.bullet_type_id ?? null,
  ammunition_casing_id: props.ammo?.ammunition_casing_id ?? null,
  primer_type_id: props.ammo?.primer_type_id ?? null,
  ammunition_condition_id: props.ammo?.ammunition_condition_id ?? null,
});

onMounted(async () => {
  const [cal, pur, bul, cas, pri, con] = await Promise.all([
    axiosInstance.get('/calibers'),
    axiosInstance.get('/purpose'),
    axiosInstance.get('/bullet-type'),
    axiosInstance.get('/ammunition-casing'),
    axiosInstance.get('/primer-type'),
    axiosInstance.get('/ammunition-condition'),
  ]);
  calibers.value = cal.data.data ?? [];
  purposes.value = pur.data.data ?? [];
  bulletTypes.value = bul.data.data ?? [];
  casings.value = cas.data.data ?? [];
  primerTypes.value = pri.data.data ?? [];
  conditions.value = con.data.data ?? [];
});

async function handleSubmit() {
  saving.value = true;
  submitError.value = null;
  try {
    let result;
    const payload = {
      caliber_id: form.value.caliber_id,
      manufacturer: form.value.manufacturer,
      label: form.value.label,
      purpose_id: form.value.purpose_id || null,
      weight: form.value.weight || null,
      reorder_min: form.value.reorder_min || null,
      reorder_target: form.value.reorder_target || null,
      bullet_type_id: form.value.bullet_type_id || null,
      ammunition_casing_id: form.value.ammunition_casing_id || null,
      primer_type_id: form.value.primer_type_id || null,
      ammunition_condition_id: form.value.ammunition_condition_id || null,
    };
    if (props.ammo) {
      result = await ammunitionStore.update(props.ammo.id, payload);
    } else {
      result = await ammunitionStore.create(payload);
    }
    emit('complete', result.data);
  } catch (e) {
    submitError.value = e;
  } finally {
    saving.value = false;
  }
}
</script>
