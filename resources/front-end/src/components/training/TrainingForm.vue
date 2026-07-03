<script setup>
import { computed, ref, onMounted } from 'vue';
import {
  Calendar,
  Check,
  ChevronDown,
  Home,
  Info,
  LoaderCircle,
  MapPin,
  Package,
  Plus,
  X,
} from 'lucide-vue-next';
import { useTrainingStore } from '@/stores/training';
import { useRangesStore } from '@/stores/ranges';
import { useFirearmsStore } from '@/stores/firearms';
import { useAmmunitionStore } from '@/stores/ammunition';
import { useSuppressorsStore } from '@/stores/suppressors';
import { useQuickAdd } from '@/components/reference/useQuickAdd';
import FormError from '@/components/FormError.vue';
import ReferenceItemModal from '@/components/reference/ReferenceItemModal.vue';

const emit = defineEmits(['complete']);

const trainingStore = useTrainingStore();
const rangesStore = useRangesStore();
const firearmsStore = useFirearmsStore();
const ammunitionStore = useAmmunitionStore();
const suppressorsStore = useSuppressorsStore();
const { quickAddType, openQuickAdd, closeQuickAdd } = useQuickAdd();

function onQuickAddSaved(item) {
  ranges.value.push(item);
  session.value.range_id = item.id;
  closeQuickAdd();
}

const loading = ref(false);
const loadingData = ref(true);
const error = ref(null);

const ranges = ref([]);
const firearms = ref([]);
const ammunition = ref([]);
const suppressors = ref([]);

const session = ref({
  label: '',
  session_date: new Date().toISOString().substring(0, 10),
  range_id: '',
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

const totalRounds = computed(() =>
  lines.value.reduce((sum, line) => sum + (Number(line.rounds) || 0), 0)
);

const firearmCount = computed(() => lines.value.filter((line) => line.firearm_id).length);

const deductedRounds = computed(() =>
  lines.value.reduce((sum, line) => sum + (line.deduct_ammo ? Number(line.rounds) || 0 : 0), 0)
);

function selectedFirearm(line) {
  return firearms.value.find((firearm) => firearm.id === Number(line.firearm_id));
}

function selectedAmmo(line) {
  return ammunition.value.find((ammo) => ammo.id === Number(line.ammunition_id));
}

function selectedSuppressor(line) {
  return suppressors.value.find((suppressor) => suppressor.id === Number(line.suppressor_id));
}

function optionLabel(item, fallback = 'Select') {
  return item?.label || fallback;
}

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
  const [rng, fa, ammo, sup] = await Promise.all([
    rangesStore.fetchAll(),
    firearmsStore.fetchAll(),
    ammunitionStore.fetchAll(),
    suppressorsStore.fetchAll(),
  ]);
  ranges.value = rng.data;
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
      range_id: session.value.range_id || null,
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

  <form v-else class="space-y-[18px]" @submit.prevent="submit">
    <!-- Session meta -->
    <div class="rounded border border-line bg-white p-5">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="col-span-2">
          <label class="mb-1.5 block text-[14px] font-medium text-[#3a3e44]"
            >Session label <span class="text-red-500">*</span></label
          >
          <input
            v-model="session.label"
            type="text"
            required
            placeholder="Steel plate drills"
            class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] outline-none focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
          />
        </div>
        <div>
          <label class="mb-1.5 block text-[14px] font-medium text-[#3a3e44]"
            >Date <span class="text-red-500">*</span></label
          >
          <div class="relative">
            <input
              v-model="session.session_date"
              type="date"
              required
              class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] pr-9 font-mono text-[14px] outline-none focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
            />
            <Calendar
              class="pointer-events-none absolute right-3 top-1/2 h-[15px] w-[15px] -translate-y-1/2 text-muted"
            />
          </div>
        </div>
        <div>
          <div class="mb-1.5 flex items-center justify-between">
            <label class="block text-[14px] font-medium text-[#3a3e44]">Location</label>
            <button
              type="button"
              class="inline-flex items-center gap-1 text-[13px] font-semibold text-brass-800 transition-colors hover:text-brass-600"
              @click="openQuickAdd('range')"
            >
              <Plus class="h-3.5 w-3.5" /> Add range
            </button>
          </div>
          <div class="relative">
            <MapPin
              class="pointer-events-none absolute left-3 top-1/2 h-[15px] w-[15px] -translate-y-1/2 text-muted"
            />
            <select
              v-model="session.range_id"
              class="w-full appearance-none rounded border border-[#c2c6ca] bg-white py-[9px] pl-9 pr-9 text-[15px] outline-none focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
            >
              <option value="">No range selected</option>
              <option v-for="range in ranges" :key="range.id" :value="range.id">
                {{ range.label }}
              </option>
            </select>
            <ChevronDown
              class="pointer-events-none absolute right-3 top-1/2 h-[15px] w-[15px] -translate-y-1/2 text-muted"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Shooting lines -->
    <div class="font-mono text-[11px] tracking-[0.1em] text-muted">FIREARM LINES</div>

    <div class="space-y-[14px]">
      <div
        v-for="(line, i) in lines"
        :key="i"
        class="overflow-hidden rounded border border-line bg-white"
      >
        <div
          class="flex flex-wrap items-center gap-3 border-b border-[#eef0f1] bg-[#fafbfb] px-4 py-[14px]"
        >
          <div
            class="flex h-[34px] w-[34px] shrink-0 items-center justify-center rounded border border-line bg-white text-[#6b7077]"
          >
            <Home class="h-[18px] w-[18px]" />
          </div>
          <div class="min-w-[180px] flex-1">
            <div class="font-display text-[16px] font-semibold leading-tight">
              {{ optionLabel(selectedFirearm(line), `Firearm line ${i + 1}`) }}
            </div>
            <div class="mt-0.5 font-mono text-[11px] tracking-[0.06em] text-muted">
              LINE {{ i + 1 }}
            </div>
          </div>
          <button
            v-if="lines.length > 1"
            type="button"
            class="flex h-7 w-7 items-center justify-center rounded text-muted transition-colors hover:bg-[#f7e9e4] hover:text-[#b4452f]"
            title="Remove line"
            @click="removeLine(i)"
          >
            <X class="h-[17px] w-[17px]" />
          </button>
        </div>

        <div class="border-b border-[#eef0f1] p-4">
          <label class="mb-1.5 block text-[14px] font-medium text-[#3a3e44]"
            >Firearm <span class="text-red-500">*</span></label
          >
          <div class="relative">
            <select
              v-model="line.firearm_id"
              class="w-full appearance-none rounded border border-[#c2c6ca] bg-white px-3 py-[9px] pr-9 text-[15px] outline-none focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
              @change="onFirearmChange(line)"
            >
              <option value="">Select firearm</option>
              <option v-for="fa in firearms" :key="fa.id" :value="fa.id">{{ fa.label }}</option>
            </select>
            <ChevronDown
              class="pointer-events-none absolute right-3 top-1/2 h-[15px] w-[15px] -translate-y-1/2 text-muted"
            />
          </div>
        </div>

        <div
          class="grid grid-cols-1 gap-[14px] border-b border-[#eef0f1] p-4 sm:grid-cols-[130px_1fr]"
        >
          <div>
            <label class="mb-1.5 block text-[14px] font-medium text-[#3a3e44]"
              >Rounds <span class="text-red-500">*</span></label
            >
            <input
              v-model="line.rounds"
              type="number"
              min="1"
              placeholder="0"
              class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] font-mono text-[18px] outline-none focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
            />
          </div>
          <div>
            <label class="mb-1.5 block text-[14px] font-medium text-[#3a3e44]"
              >Ammo used <span class="text-red-500">*</span></label
            >
            <div class="relative">
              <Package
                class="pointer-events-none absolute left-3 top-1/2 h-[15px] w-[15px] -translate-y-1/2 text-[#7d6320]"
              />
              <select
                v-model="line.ammunition_id"
                class="w-full appearance-none rounded border border-[#c2c6ca] bg-white py-[9px] pl-9 pr-9 text-[15px] outline-none focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
              >
                <option value="">Select ammunition</option>
                <option v-for="ammo in ammunition" :key="ammo.id" :value="ammo.id">
                  {{ ammo.label }}
                </option>
              </select>
              <ChevronDown
                class="pointer-events-none absolute right-3 top-1/2 h-[15px] w-[15px] -translate-y-1/2 text-muted"
              />
            </div>
            <div v-if="selectedAmmo(line)" class="mt-1 font-mono text-[12px] text-muted">
              {{
                selectedAmmo(line).inventory?.toLocaleString?.() ??
                selectedAmmo(line).inventory ??
                0
              }}
              left
            </div>
          </div>
        </div>

        <!-- Toggles -->
        <div class="px-4 pb-[14px] pt-1">
          <div class="mb-1.5 mt-2 font-mono text-[10px] tracking-[0.06em] text-muted">
            APPLY TO INVENTORY
          </div>
          <label
            class="flex cursor-pointer select-none items-center gap-3 border-b border-[#f1f2f3] py-[9px]"
          >
            <input v-model="line.deduct_ammo" type="checkbox" class="peer sr-only" />
            <span
              class="relative h-[23px] w-10 shrink-0 rounded-full border border-[#c2c6ca] bg-[#d6d9dc] transition-colors peer-checked:border-[#b08a2e] peer-checked:bg-brass"
            >
              <span
                class="absolute left-1 top-[3px] h-[15px] w-[15px] rounded-full bg-white transition-transform peer-checked:translate-x-[17px]"
              ></span>
            </span>
            <span class="min-w-0 flex-1">
              <span class="block text-[14px] font-medium text-[#3a3e44]"
                >Deduct from ammo inventory</span
              >
              <span class="block text-[12px] text-muted">
                <template v-if="line.rounds && selectedAmmo(line)"
                  >−{{ Number(line.rounds).toLocaleString() }} from
                  {{ selectedAmmo(line).label }}</template
                >
                <template v-else>Subtract fired rounds from the selected load.</template>
              </span>
            </span>
          </label>
          <label
            class="flex cursor-pointer select-none items-center gap-3 border-b border-[#f1f2f3] py-[9px]"
          >
            <input v-model="line.add_firearm_count" type="checkbox" class="peer sr-only" />
            <span
              class="relative h-[23px] w-10 shrink-0 rounded-full border border-[#c2c6ca] bg-[#d6d9dc] transition-colors peer-checked:border-[#b08a2e] peer-checked:bg-brass"
            >
              <span
                class="absolute left-1 top-[3px] h-[15px] w-[15px] rounded-full bg-white transition-transform peer-checked:translate-x-[17px]"
              ></span>
            </span>
            <span class="min-w-0 flex-1">
              <span class="block text-[14px] font-medium text-[#3a3e44]"
                >Add to {{ optionLabel(selectedFirearm(line), 'firearm') }} round count</span
              >
              <span class="block text-[12px] text-muted">
                <template v-if="line.rounds"
                  >+{{ Number(line.rounds).toLocaleString() }} rounds</template
                >
                <template v-else>Keep firearm lifetime totals in sync.</template>
              </span>
            </span>
          </label>
          <label class="flex cursor-pointer select-none items-start gap-3 py-[9px]">
            <input v-model="line.add_suppressor_count" type="checkbox" class="peer sr-only" />
            <span
              class="relative mt-0.5 h-[23px] w-10 shrink-0 rounded-full border border-[#c2c6ca] bg-[#d6d9dc] transition-colors peer-checked:border-[#b08a2e] peer-checked:bg-brass"
            >
              <span
                class="absolute left-1 top-[3px] h-[15px] w-[15px] rounded-full bg-white transition-transform peer-checked:translate-x-[17px]"
              ></span>
            </span>
            <span class="min-w-0 flex-1">
              <span class="block text-[14px] font-medium text-[#3a3e44]"
                >Add to suppressor round count</span
              >
              <span class="block text-[12px] text-muted"
                >Use when a suppressor was mounted or used for this session.</span
              >
            </span>
          </label>
          <div v-if="line.add_suppressor_count" class="ml-[52px] mt-1">
            <div class="flex flex-wrap items-center gap-2">
              <div class="relative min-w-[220px]">
                <select
                  v-model="line.suppressor_id"
                  class="w-full appearance-none rounded border border-[#ddd4ea] bg-[#f7f4fa] px-3 py-2 pr-9 text-[13px] outline-none focus:border-brass"
                >
                  <option value="">Select suppressor</option>
                  <option v-for="sup in suppressors" :key="sup.id" :value="sup.id">
                    {{ sup.label }}
                  </option>
                </select>
                <ChevronDown
                  class="pointer-events-none absolute right-3 top-1/2 h-[14px] w-[14px] -translate-y-1/2 text-muted"
                />
              </div>
              <span
                v-if="selectedSuppressor(line)?.is_nfa"
                class="rounded-sm bg-[#1a1c1f] px-1 font-mono text-[9px] text-white"
                >NFA</span
              >
            </div>
            <div
              class="mt-2 inline-flex items-start gap-1.5 rounded border border-[#ecdcb4] bg-[#fbf7ec] px-2.5 py-1.5 text-[12px] text-[#6b7077]"
            >
              <Info class="mt-0.5 h-[13px] w-[13px] shrink-0 text-[#a8842f]" />
              <span>Counted for this session only. This does not change mounted status.</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <button
      type="button"
      class="flex w-full items-center justify-center gap-2 rounded border border-dashed border-[#c2c6ca] bg-[#fafbfb] py-[14px] text-[15px] font-semibold text-[#7d6320] transition-colors hover:border-[#a9aeb3] hover:bg-[#f3f4f5]"
      @click="addLine"
    >
      <Plus class="h-[17px] w-[17px]" />
      Add another firearm
    </button>

    <!-- Notes -->
    <div class="rounded border border-line bg-white p-4">
      <label class="mb-1.5 block text-[14px] font-medium text-[#3a3e44]"
        >Notes <span class="font-normal text-muted">· optional</span></label
      >
      <textarea
        v-model="session.description"
        rows="3"
        placeholder="How'd it go? Drills, zero, anything to remember…"
        class="min-h-16 w-full resize-y rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-[14px] outline-none placeholder:text-muted focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
      />
    </div>

    <FormError v-if="error" :error="error" />

    <!-- Sticky footer -->
    <div
      class="sticky bottom-0 z-10 flex flex-wrap items-center gap-4 rounded border border-line bg-white px-[18px] py-[14px] shadow-[0_-2px_8px_rgba(20,22,26,0.06)]"
    >
      <div class="flex flex-wrap gap-[18px]">
        <div>
          <div class="font-mono text-[18px] font-medium leading-none">
            {{ totalRounds.toLocaleString() }}
          </div>
          <div class="font-mono text-[9px] tracking-[0.05em] text-muted">ROUNDS</div>
        </div>
        <div>
          <div class="font-mono text-[18px] font-medium leading-none">{{ firearmCount }}</div>
          <div class="font-mono text-[9px] tracking-[0.05em] text-muted">FIREARMS</div>
        </div>
        <div>
          <div class="font-mono text-[18px] font-medium leading-none text-[#b4452f]">
            −{{ deductedRounds.toLocaleString() }}
          </div>
          <div class="font-mono text-[9px] tracking-[0.05em] text-muted">AMMO</div>
        </div>
      </div>
      <div class="ml-auto flex items-center gap-2.5">
        <router-link
          :to="{ name: 'TrainingIndex' }"
          class="rounded border border-[#c2c6ca] bg-white px-[18px] py-[9px] text-[15px] font-semibold text-[#3a3e44] transition-colors hover:bg-[#f5f6f7]"
          >Cancel</router-link
        >
        <button
          type="submit"
          :disabled="loading"
          class="inline-flex items-center gap-[7px] rounded border border-[#b08a2e] bg-brass px-5 py-[9px] text-[15px] font-semibold text-ink-900 transition-colors hover:bg-brass-600 disabled:cursor-not-allowed disabled:opacity-60"
        >
          <LoaderCircle v-if="loading" class="h-4 w-4 animate-spin" />
          <Check v-else class="h-4 w-4" />
          Save session
        </button>
      </div>
    </div>

    <ReferenceItemModal
      v-if="quickAddType"
      :type="quickAddType"
      mode="add"
      @close="closeQuickAdd"
      @saved="onQuickAddSaved"
    />
  </form>
</template>
