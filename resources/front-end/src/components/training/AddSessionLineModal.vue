<script setup>
import { ref, onMounted } from 'vue';
import { X } from 'lucide-vue-next';
import { sortAmmunitionOptions } from '@/composables/useAmmunitionHelper';
import { useSessionLinesStore } from '@/stores/sessionLines';
import { useFirearmsStore } from '@/stores/firearms';
import { useAmmunitionStore } from '@/stores/ammunition';
import { useSuppressorsStore } from '@/stores/suppressors';
import ActionButton from '@/components/ActionButton.vue';
import FormError from '@/components/FormError.vue';
import TrainingLineFields from '@/components/training/TrainingLineFields.vue';

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
  const [fa, ammo, sup] = await Promise.all([
    firearmsStore.fetchAll(),
    ammunitionStore.fetchAll(),
    suppressorsStore.fetchAll(),
  ]);
  firearms.value = fa.data;
  ammunition.value = sortAmmunitionOptions(ammo.data);
  suppressors.value = sup.data;
  loadingData.value = false;
});

async function submit() {
  saving.value = true;
  saveError.value = null;
  try {
    const { data } = await sessionLinesStore.create(props.trainingId, {
      firearm_id: form.value.firearm_id ? Number(form.value.firearm_id) : null,
      add_firearm_count: Boolean(form.value.firearm_id) && form.value.add_firearm_count,
      ammunition_id: Number(form.value.ammunition_id),
      suppressor_id: form.value.suppressor_id ? Number(form.value.suppressor_id) : null,
      rounds: Number(form.value.rounds),
      deduct_ammo: form.value.deduct_ammo,
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
          <TrainingLineFields
            v-model:line="form"
            :firearms="firearms"
            :ammunition="ammunition"
            :suppressors="suppressors"
            @firearm-change="onFirearmChange"
          />

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
