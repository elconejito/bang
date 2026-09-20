<template>
  <div class="flex flex-col gap-5">
    <FormError v-if="submitError" :error="submitError" />

    <!-- Caliber -->
    <div class="flex flex-col gap-1.5">
      <div class="flex items-center justify-between">
        <label class="text-[14px] font-medium">Caliber <span class="text-[#b4452f]">*</span></label>
        <button
          v-if="!ammo"
          type="button"
          class="inline-flex items-center gap-1 text-[13px] font-semibold text-brass-800 transition-colors hover:text-brass-600"
          @click="openQuickAdd('caliber')"
        >
          <Plus class="h-3.5 w-3.5" /> Add caliber
        </button>
      </div>
      <select
        data-testid="ammo-caliber"
        v-model="form.caliber_id"
        class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
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

    <!-- Shotgun details -->
    <div v-if="isShotgunCaliber" class="rounded border border-line bg-ink-50 p-4">
      <div class="mb-3">
        <div class="font-mono text-[10px] tracking-[0.08em] text-muted">SHOTGUN DETAILS</div>
        <p class="mt-1 text-[12px] text-muted">Optional shell and shot characteristics.</p>
      </div>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="flex flex-col gap-1.5">
          <label class="text-[14px] font-medium">Shot weight</label>
          <select
            data-testid="ammo-shot-weight"
            v-model="form.shot_weight_id"
            class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
          >
            <option :value="null">— optional —</option>
            <option v-for="weight in shotWeights" :key="weight.id" :value="weight.id">
              {{ weight.label }}
            </option>
          </select>
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-[14px] font-medium">Shell length</label>
          <select
            data-testid="ammo-shell-length"
            v-model="form.shell_length_id"
            class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
          >
            <option :value="null">— optional —</option>
            <option v-for="length in shellLengths" :key="length.id" :value="length.id">
              {{ length.label }}
            </option>
          </select>
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-[14px] font-medium">Shell type</label>
          <select
            data-testid="ammo-shell-type"
            v-model="form.shell_type_id"
            class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
          >
            <option :value="null">— optional —</option>
            <option v-for="type in shellTypes" :key="type.id" :value="type.id">
              {{ type.label }}
            </option>
          </select>
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-[14px] font-medium">Shot material</label>
          <select
            data-testid="ammo-shot-material"
            v-model="form.shot_material_id"
            class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
          >
            <option :value="null">— optional —</option>
            <option v-for="material in shotMaterials" :key="material.id" :value="material.id">
              {{ material.label }}
            </option>
          </select>
        </div>
      </div>
    </div>

    <!-- Purpose + Weight -->
    <div class="grid grid-cols-2 gap-4">
      <div class="flex flex-col gap-1.5" :class="{ 'col-span-2': isShotgunCaliber }">
        <div class="flex items-center justify-between">
          <label class="text-[14px] font-medium">Purpose</label>
          <button
            type="button"
            class="inline-flex items-center gap-1 text-[13px] font-semibold text-brass-800 transition-colors hover:text-brass-600"
            @click="openQuickAdd('purpose')"
          >
            <Plus class="h-3.5 w-3.5" /> Add purpose
          </button>
        </div>
        <select
          v-model="form.purpose_id"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
        >
          <option :value="null">— optional —</option>
          <option v-for="p in purposes" :key="p.id" :value="p.id">{{ p.label }}</option>
        </select>
      </div>
      <div v-if="!isShotgunCaliber" class="flex flex-col gap-1.5">
        <label class="text-[14px] font-medium">Weight (gr)</label>
        <input
          data-testid="ammo-bullet-weight"
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
    <div v-if="!isShotgunCaliber" class="grid grid-cols-2 gap-4">
      <div class="flex flex-col gap-1.5">
        <label class="text-[14px] font-medium">Bullet type</label>
        <select
          data-testid="ammo-bullet-type"
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
          data-testid="ammo-casing"
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

    <ReferenceItemModal
      v-if="quickAddType"
      :type="quickAddType"
      mode="add"
      @close="closeQuickAdd"
      @saved="onQuickAddSaved"
    />
  </div>
</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue';
import { LoaderCircle, Plus } from 'lucide-vue-next';
import { axiosInstance } from '@/plugins/axios';
import { useAmmunitionStore } from '@/stores/ammunition';
import { useQuickAdd } from '@/components/reference/useQuickAdd';
import FormError from '@/components/FormError.vue';
import ReferenceItemModal from '@/components/reference/ReferenceItemModal.vue';

const props = defineProps({
  ammo: { type: Object, default: null },
  preselectedCaliberId: { type: Number, default: null },
});

const emit = defineEmits(['complete', 'cancel']);

const ammunitionStore = useAmmunitionStore();
const { quickAddType, openQuickAdd, closeQuickAdd } = useQuickAdd();

const saving = ref(false);
const submitError = ref(null);

const calibers = ref([]);
const purposes = ref([]);
const bulletTypes = ref([]);
const casings = ref([]);
const primerTypes = ref([]);
const conditions = ref([]);
const caliberTypes = ref([]);
const shellLengths = ref([]);
const shellTypes = ref([]);
const shotMaterials = ref([]);
const shotWeights = ref([]);

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
  shell_length_id: props.ammo?.shell_length_id ?? null,
  shell_type_id: props.ammo?.shell_type_id ?? null,
  shot_material_id: props.ammo?.shot_material_id ?? null,
  shot_weight_id: props.ammo?.shot_weight_id ?? null,
});

const shotgunCaliberTypeId = computed(
  () => caliberTypes.value.find((type) => type.label === 'Shotgun')?.id ?? null
);
const selectedCaliber = computed(() =>
  calibers.value.find((caliber) => Number(caliber.id) === Number(form.value.caliber_id))
);
const isShotgunCaliber = computed(
  () =>
    shotgunCaliberTypeId.value !== null &&
    Number(selectedCaliber.value?.caliber_type_id) === Number(shotgunCaliberTypeId.value)
);

watch(isShotgunCaliber, (isShotgun) => {
  if (isShotgun) {
    form.value.weight = null;
    form.value.bullet_type_id = null;
    form.value.ammunition_casing_id = null;

    return;
  }

  form.value.shell_length_id = null;
  form.value.shell_type_id = null;
  form.value.shot_material_id = null;
  form.value.shot_weight_id = null;
});

onMounted(async () => {
  const [
    cal,
    pur,
    bul,
    cas,
    pri,
    con,
    caliberType,
    shellLength,
    shellType,
    shotMaterial,
    shotWeight,
  ] = await Promise.all([
    axiosInstance.get('/calibers'),
    axiosInstance.get('/purpose'),
    axiosInstance.get('/bullet-type'),
    axiosInstance.get('/ammunition-casing'),
    axiosInstance.get('/primer-type'),
    axiosInstance.get('/ammunition-condition'),
    axiosInstance.get('/caliber-type'),
    axiosInstance.get('/shell-length'),
    axiosInstance.get('/shell-type'),
    axiosInstance.get('/shot-material'),
    axiosInstance.get('/shot-weight'),
  ]);
  calibers.value = cal.data.data ?? [];
  purposes.value = pur.data.data ?? [];
  bulletTypes.value = bul.data.data ?? [];
  casings.value = cas.data.data ?? [];
  primerTypes.value = pri.data.data ?? [];
  conditions.value = con.data.data ?? [];
  caliberTypes.value = caliberType.data.data ?? [];
  shellLengths.value = shellLength.data.data ?? [];
  shellTypes.value = shellType.data.data ?? [];
  shotMaterials.value = shotMaterial.data.data ?? [];
  shotWeights.value = shotWeight.data.data ?? [];
});

function onQuickAddSaved(item) {
  if (quickAddType.value === 'caliber') {
    calibers.value.push(item);
    form.value.caliber_id = item.id;
  } else if (quickAddType.value === 'purpose') {
    purposes.value.push(item);
    form.value.purpose_id = item.id;
  }
  closeQuickAdd();
}

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
      weight: isShotgunCaliber.value ? null : form.value.weight || null,
      reorder_min: form.value.reorder_min || null,
      reorder_target: form.value.reorder_target || null,
      bullet_type_id: isShotgunCaliber.value ? null : form.value.bullet_type_id || null,
      ammunition_casing_id: isShotgunCaliber.value ? null : form.value.ammunition_casing_id || null,
      primer_type_id: form.value.primer_type_id || null,
      ammunition_condition_id: form.value.ammunition_condition_id || null,
      shell_length_id: isShotgunCaliber.value ? form.value.shell_length_id || null : null,
      shell_type_id: isShotgunCaliber.value ? form.value.shell_type_id || null : null,
      shot_material_id: isShotgunCaliber.value ? form.value.shot_material_id || null : null,
      shot_weight_id: isShotgunCaliber.value ? form.value.shot_weight_id || null : null,
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
