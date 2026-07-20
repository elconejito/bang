<script setup>
import { ref, computed, onMounted } from 'vue';
import { X } from 'lucide-vue-next';
import { useSessionLinesStore } from '@/stores/sessionLines';
import { useFirearmsStore } from '@/stores/firearms';
import { useAmmunitionStore } from '@/stores/ammunition';
import { useSuppressorsStore } from '@/stores/suppressors';
import ActionButton from '@/components/ActionButton.vue';
import FormError from '@/components/FormError.vue';

const props = defineProps({
  trainingId: { type: Number, required: true },
});

const emit = defineEmits(['close', 'created']);

const sessionLinesStore = useSessionLinesStore();
const firearmsStore = useFirearmsStore();
const ammunitionStore = useAmmunitionStore();
const suppressorsStore = useSuppressorsStore();

const loadingData = ref(true);
const saving = ref(false);
const saveError = ref(null);

const firearms = ref([]);
const ammunition = ref([]);
const suppressors = ref([]);

const form = ref({
  firearm_id: '',
  ammunition_id: '',
  suppressor_id: '',
  rounds: '',
  deduct_ammo: true,
  add_firearm_count: true,
  add_suppressor_count: false,
});

const showSuppressor = computed(() => form.value.add_suppressor_count);

function onFirearmChange() {
  const firearm = firearms.value.find((f) => f.id === Number(form.value.firearm_id));
  if (firearm?.mounted_suppressor_id) {
    form.value.suppressor_id = firearm.mounted_suppressor_id;
    form.value.add_suppressor_count = true;
  } else {
    form.value.suppressor_id = '';
    form.value.add_suppressor_count = false;
  }
}

onMounted(async () => {
  const [fa, ammo, sup] = await Promise.all([
    firearmsStore.fetchAll(),
    ammunitionStore.fetchAll(),
    suppressorsStore.fetchAll(),
  ]);
  firearms.value = fa.data;
  ammunition.value = ammo.data;
  suppressors.value = sup.data;
  loadingData.value = false;
});

async function submit() {
  saving.value = true;
  saveError.value = null;
  try {
    const { data } = await sessionLinesStore.create(props.trainingId, {
      firearm_id: Number(form.value.firearm_id),
      ammunition_id: Number(form.value.ammunition_id),
      suppressor_id: form.value.suppressor_id ? Number(form.value.suppressor_id) : null,
      rounds: Number(form.value.rounds),
      deduct_ammo: form.value.deduct_ammo,
      add_firearm_count: form.value.add_firearm_count,
      add_suppressor_count: form.value.add_suppressor_count,
    });
    emit('created', data);
  } catch (e) {
    saveError.value = e;
  } finally {
    saving.value = false;
  }
}
</script>

<template>
  <Teleport to="body">
    <div
      class="modal-scrim z-50 px-4 pb-4 pt-10 sm:px-6 sm:pb-6 sm:pt-14"
      @click.self="$emit('close')"
    >
      <div class="modal-shell w-[520px] max-w-full">
        <!-- Header -->
        <div
          class="flex items-center justify-between gap-3 border-b border-[#eef0f1] px-[18px] py-4"
        >
          <span class="font-display text-[19px] font-semibold">Add shooting line</span>
          <button class="p-0.5 text-muted hover:text-ink-900" @click="$emit('close')">
            <X class="h-[18px] w-[18px]" />
          </button>
        </div>

        <LoadingState v-if="loadingData" message="Loading session options…" />

        <form v-else @submit.prevent="submit">
          <div class="flex flex-col gap-4 p-[18px]">
            <!-- Firearm -->
            <div>
              <label class="block text-[13px] font-medium text-[#3a3e44] mb-1"
                >Firearm <span class="text-red-500">*</span></label
              >
              <select
                v-model="form.firearm_id"
                required
                class="w-full rounded-sm border border-[#c2c6ca] px-3 py-2 text-[14px] focus:outline-none focus:border-brass"
                @change="onFirearmChange"
              >
                <option value="">— Select —</option>
                <option v-for="f in firearms" :key="f.id" :value="f.id">
                  {{ f.manufacturer }} {{ f.label }}
                </option>
              </select>
            </div>

            <!-- Ammo + Rounds -->
            <div class="grid grid-cols-[1fr_120px] gap-3">
              <div>
                <label class="block text-[13px] font-medium text-[#3a3e44] mb-1"
                  >Ammunition <span class="text-red-500">*</span></label
                >
                <select
                  v-model="form.ammunition_id"
                  required
                  class="w-full rounded-sm border border-[#c2c6ca] px-3 py-2 text-[14px] focus:outline-none focus:border-brass"
                >
                  <option value="">— Select —</option>
                  <option v-for="a in ammunition" :key="a.id" :value="a.id">
                    {{ a.manufacturer }} {{ a.label }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-[13px] font-medium text-[#3a3e44] mb-1"
                  >Rounds <span class="text-red-500">*</span></label
                >
                <input
                  v-model.number="form.rounds"
                  type="number"
                  min="1"
                  required
                  class="w-full rounded-sm border border-[#c2c6ca] px-3 py-2 text-[14px] font-mono focus:outline-none focus:border-brass"
                />
              </div>
            </div>

            <!-- Checkboxes -->
            <div class="flex flex-col gap-2 pt-1">
              <label class="flex items-center gap-2.5 cursor-pointer select-none">
                <input v-model="form.deduct_ammo" type="checkbox" class="w-4 h-4 accent-brass" />
                <span class="text-[14px]">Deduct from ammo inventory</span>
              </label>
              <label class="flex items-center gap-2.5 cursor-pointer select-none">
                <input
                  v-model="form.add_firearm_count"
                  type="checkbox"
                  class="w-4 h-4 accent-brass"
                />
                <span class="text-[14px]">Add to firearm round count</span>
              </label>
              <label class="flex items-center gap-2.5 cursor-pointer select-none">
                <input
                  v-model="form.add_suppressor_count"
                  type="checkbox"
                  class="w-4 h-4 accent-brass"
                />
                <span class="text-[14px]">Add to suppressor round count</span>
              </label>
            </div>

            <!-- Suppressor (conditional) -->
            <div v-if="showSuppressor">
              <label class="block text-[13px] font-medium text-[#3a3e44] mb-1">Suppressor</label>
              <select
                v-model="form.suppressor_id"
                class="w-full rounded-sm border border-[#c2c6ca] px-3 py-2 text-[14px] focus:outline-none focus:border-brass"
              >
                <option value="">— None —</option>
                <option v-for="s in suppressors" :key="s.id" :value="s.id">{{ s.label }}</option>
              </select>
            </div>
          </div>

          <FormError v-if="saveError" :error="saveError" class="mx-[18px] mb-4" />

          <!-- Footer -->
          <div
            class="flex items-center gap-2.5 border-t border-[#eef0f1] bg-[#fafbfb] px-[18px] py-[14px]"
          >
            <ActionButton text="Add line" :is-loading="saving" variant="primary" type="submit" />
            <button
              type="button"
              class="rounded border border-[#c2c6ca] bg-white px-[14px] py-[8px] text-[14px] font-semibold text-ink-700 hover:bg-[#f5f6f7] transition-colors"
              @click="$emit('close')"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>
