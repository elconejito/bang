<script setup>
import { ref, onMounted } from 'vue';
import { useTrainingStore } from '@/stores/training';
import { useLocationsStore } from '@/stores/locations';
import { useFirearmsStore } from '@/stores/firearms';
import { useAmmunitionStore } from '@/stores/ammunition';
import { useSuppressorsStore } from '@/stores/suppressors';
import ActionButton from '@/components/ActionButton.vue';
import FormError from '@/components/FormError.vue';

const emit = defineEmits(['complete']);

const trainingStore = useTrainingStore();
const locationsStore = useLocationsStore();
const firearmsStore = useFirearmsStore();
const ammunitionStore = useAmmunitionStore();
const suppressorsStore = useSuppressorsStore();

const loading = ref(false);
const loadingData = ref(true);
const error = ref(null);

const locations = ref([]);
const firearms = ref([]);
const ammunition = ref([]);
const suppressors = ref([]);

const session = ref({
  label: '',
  session_date: new Date().toISOString().substring(0, 10),
  location_id: '',
  description: '',
});

function newLine() {
  return {
    firearm_id: '',
    ammunition_id: '',
    rounds: '',
    deduct_ammo: true,
    add_firearm_count: true,
    add_suppressor_count: false,
    suppressor_id: '',
  };
}

const lines = ref([newLine()]);

function addLine() {
  lines.value.push(newLine());
}

function removeLine(index) {
  lines.value.splice(index, 1);
}

function onFirearmChange(line) {
  const firearm = firearms.value.find((f) => f.id === Number(line.firearm_id));
  if (firearm?.mounted_suppressor_id) {
    line.suppressor_id = firearm.mounted_suppressor_id;
    line.add_suppressor_count = true;
  } else {
    line.suppressor_id = '';
    line.add_suppressor_count = false;
  }
}

onMounted(async () => {
  const [loc, fa, ammo, sup] = await Promise.all([
    locationsStore.fetchAll(),
    firearmsStore.fetchAll(),
    ammunitionStore.fetchAll(),
    suppressorsStore.fetchAll(),
  ]);
  locations.value = loc.data;
  firearms.value = fa.data;
  ammunition.value = ammo.data;
  suppressors.value = sup.data;
  loadingData.value = false;
});

async function submit() {
  error.value = null;
  loading.value = true;
  try {
    const payload = {
      ...session.value,
      location_id: session.value.location_id || null,
      lines: lines.value
        .filter((l) => l.firearm_id && l.ammunition_id && l.rounds)
        .map((l) => ({
          firearm_id: Number(l.firearm_id),
          ammunition_id: Number(l.ammunition_id),
          rounds: Number(l.rounds),
          deduct_ammo: l.deduct_ammo,
          add_firearm_count: l.add_firearm_count,
          add_suppressor_count: l.add_suppressor_count,
          suppressor_id: l.suppressor_id ? Number(l.suppressor_id) : null,
        })),
    };
    const { data } = await trainingStore.create(payload);
    emit('complete', data);
  } catch (err) {
    if (err.response?.data?.errors) err.errorBag = err.response.data.errors;
    error.value = err;
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <div v-if="loadingData" class="text-sm text-muted py-8 text-center">Loading…</div>

  <form v-else @submit.prevent="submit">
    <!-- Session meta -->
    <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden mb-5">
      <div class="px-4 py-3 border-b border-[#eef0f1] font-display font-semibold text-[16px]">Session</div>
      <div class="px-4 pt-4 pb-5 grid grid-cols-2 gap-4">
        <div class="col-span-2">
          <label class="block text-[13px] font-medium text-[#3a3e44] mb-1">Label <span class="text-red-500">*</span></label>
          <input
            v-model="session.label"
            type="text"
            required
            placeholder="e.g. Sunday range day"
            class="w-full rounded-sm border border-[#c2c6ca] px-3 py-2 text-[14px] focus:outline-none focus:border-brass"
          />
        </div>
        <div>
          <label class="block text-[13px] font-medium text-[#3a3e44] mb-1">Date <span class="text-red-500">*</span></label>
          <input
            v-model="session.session_date"
            type="date"
            required
            class="w-full rounded-sm border border-[#c2c6ca] px-3 py-2 text-[14px] focus:outline-none focus:border-brass"
          />
        </div>
        <div>
          <label class="block text-[13px] font-medium text-[#3a3e44] mb-1">Location</label>
          <select
            v-model="session.location_id"
            class="w-full rounded-sm border border-[#c2c6ca] px-3 py-2 text-[14px] focus:outline-none focus:border-brass"
          >
            <option value="">— None —</option>
            <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.label }}</option>
          </select>
        </div>
        <div class="col-span-2">
          <label class="block text-[13px] font-medium text-[#3a3e44] mb-1">Notes</label>
          <textarea
            v-model="session.description"
            rows="3"
            placeholder="Optional session notes…"
            class="w-full rounded-sm border border-[#c2c6ca] px-3 py-2 text-[14px] resize-y focus:outline-none focus:border-brass"
          />
        </div>
      </div>
    </div>

    <!-- Shooting lines -->
    <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden mb-4">
      <div class="px-4 py-3 border-b border-[#eef0f1] font-display font-semibold text-[16px]">Shooting lines</div>

      <div
        v-for="(line, i) in lines"
        :key="i"
        class="px-4 pt-4 pb-5 border-b border-[#eef0f1] last:border-b-0"
      >
        <div class="flex items-center justify-between mb-3">
          <span class="font-mono text-[11px] text-muted tracking-[0.06em]">LINE {{ i + 1 }}</span>
          <button
            v-if="lines.length > 1"
            type="button"
            class="text-[13px] text-[#c0392b] hover:underline"
            @click="removeLine(i)"
          >Remove</button>
        </div>

        <div class="grid grid-cols-3 gap-3 mb-4">
          <div>
            <label class="block text-[13px] font-medium text-[#3a3e44] mb-1">Firearm <span class="text-red-500">*</span></label>
            <select
              v-model="line.firearm_id"
              class="w-full rounded-sm border border-[#c2c6ca] px-3 py-2 text-[14px] focus:outline-none focus:border-brass"
              @change="onFirearmChange(line)"
            >
              <option value="">— Select —</option>
              <option v-for="fa in firearms" :key="fa.id" :value="fa.id">{{ fa.label }}</option>
            </select>
          </div>
          <div>
            <label class="block text-[13px] font-medium text-[#3a3e44] mb-1">Ammunition <span class="text-red-500">*</span></label>
            <select
              v-model="line.ammunition_id"
              class="w-full rounded-sm border border-[#c2c6ca] px-3 py-2 text-[14px] focus:outline-none focus:border-brass"
            >
              <option value="">— Select —</option>
              <option v-for="ammo in ammunition" :key="ammo.id" :value="ammo.id">{{ ammo.label }}</option>
            </select>
          </div>
          <div>
            <label class="block text-[13px] font-medium text-[#3a3e44] mb-1">Rounds <span class="text-red-500">*</span></label>
            <input
              v-model="line.rounds"
              type="number"
              min="1"
              placeholder="0"
              class="w-full rounded-sm border border-[#c2c6ca] px-3 py-2 text-[14px] focus:outline-none focus:border-brass"
            />
          </div>
        </div>

        <!-- Toggles -->
        <div class="flex flex-col gap-2.5">
          <label class="flex items-center gap-2.5 cursor-pointer select-none">
            <input v-model="line.deduct_ammo" type="checkbox" class="rounded border-[#c2c6ca] text-brass focus:ring-brass" />
            <span class="text-[13px] text-[#3a3e44]">Deduct from ammo inventory</span>
          </label>
          <label class="flex items-center gap-2.5 cursor-pointer select-none">
            <input v-model="line.add_firearm_count" type="checkbox" class="rounded border-[#c2c6ca] text-brass focus:ring-brass" />
            <span class="text-[13px] text-[#3a3e44]">Add to firearm round count</span>
          </label>
          <label class="flex items-center gap-2.5 cursor-pointer select-none">
            <input v-model="line.add_suppressor_count" type="checkbox" class="rounded border-[#c2c6ca] text-brass focus:ring-brass" />
            <span class="text-[13px] text-[#3a3e44]">Add to suppressor round count</span>
          </label>
          <div v-if="line.add_suppressor_count" class="ml-6">
            <select
              v-model="line.suppressor_id"
              class="rounded-sm border border-[#c2c6ca] px-3 py-1.5 text-[13px] focus:outline-none focus:border-brass"
            >
              <option value="">— Select suppressor —</option>
              <option v-for="sup in suppressors" :key="sup.id" :value="sup.id">{{ sup.label }}</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <button
      type="button"
      class="w-full flex items-center justify-center gap-1.5 border border-dashed border-[#c2c6ca] rounded-sm py-2.5 text-[14px] text-muted hover:border-brass hover:text-brass transition-colors mb-5"
      @click="addLine"
    >
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
      Add another firearm / load
    </button>

    <FormError v-if="error" :error="error" />

    <div class="flex items-center gap-3 mt-2">
      <ActionButton text="Save session" :is-loading="loading" variant="primary" type="submit" />
      <router-link :to="{ name: 'TrainingIndex' }" class="text-[14px] text-muted hover:text-ink-700">Cancel</router-link>
    </div>
  </form>
</template>
