<template>
  <Teleport to="body">
    <div
      class="fixed inset-0 z-50 flex items-start justify-center overflow-auto bg-[rgba(20,22,26,0.46)] px-6 pb-6 pt-[110px]"
      @click.self="$emit('close')"
    >
      <div
        class="w-[440px] max-w-full overflow-hidden rounded-[4px] border border-line bg-surface shadow-[0_10px_30px_rgba(20,22,26,0.22),0_2px_8px_rgba(20,22,26,0.12)]"
      >
        <!-- Header -->
        <div class="flex items-start gap-3 border-b border-[#eef0f1] px-[18px] py-4">
          <div
            class="flex h-8 w-8 shrink-0 items-center justify-center rounded border border-brass-300 bg-brass-200 text-brass-800"
          >
            <component :is="kindIcon" class="h-[17px] w-[17px]" />
          </div>
          <div class="min-w-0 flex-1">
            <div class="font-display text-[18px] font-bold leading-[1.15] text-ink-900">
              {{ title }}
            </div>
            <div class="mt-0.5 text-[12px] text-muted">{{ kindSubline }}</div>
          </div>
          <button
            type="button"
            class="shrink-0 rounded p-1 text-muted transition-colors hover:bg-[#eceef0] hover:text-ink-900"
            @click="$emit('close')"
          >
            <X class="h-[18px] w-[18px]" />
          </button>
        </div>

        <!-- Body -->
        <div class="flex flex-col gap-[15px] p-[18px]">
          <!-- Label (always) -->
          <div class="flex flex-col gap-1.5">
            <label :for="`ref-label`" class="text-[13px] font-semibold text-ink-700">
              {{
                isCaliber
                  ? 'Label · short name shown across Bang'
                  : 'Purpose name · shown as a tag on ammo loads'
              }}
            </label>
            <input
              id="ref-label"
              ref="labelInput"
              v-model="form.label"
              type="text"
              :placeholder="isCaliber ? 'e.g. 9mm' : 'e.g. Range / Training'"
              class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-brass-200"
              @keydown.enter.prevent="canSave && save()"
            />
            <p class="text-[12px] text-muted">
              {{
                isCaliber
                  ? 'The short, everyday name used on chips and dropdowns.'
                  : 'A short label — it will appear as a tag on ammo loads.'
              }}
            </p>
          </div>

          <!-- Caliber official name (calibers only) -->
          <template v-if="isCaliber">
            <div class="flex flex-col gap-1.5">
              <label for="ref-official" class="text-[13px] font-semibold text-ink-700"
                >Caliber · official name</label
              >
              <input
                id="ref-official"
                v-model="form.official"
                type="text"
                placeholder="e.g. 9×19mm Parabellum"
                class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] font-mono text-[14px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-brass-200"
              />
              <p class="text-[12px] text-muted">
                The full cartridge designation, usually with measurements.
              </p>
            </div>

            <!-- Caliber type select -->
            <div class="flex flex-col gap-1.5">
              <label for="ref-type" class="text-[13px] font-semibold text-ink-700">
                Type <span class="text-caution">*</span>
              </label>
              <select
                id="ref-type"
                v-model="form.caliber_type_id"
                class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-brass-200"
              >
                <option v-for="type in caliberTypes" :key="type.id" :value="type.id">
                  {{ type.label }}
                </option>
              </select>
              <p class="text-[12px] text-muted">Rimfire, centerfire, shotgun, etc.</p>
            </div>
          </template>

          <!-- In-use note (edit + in use) -->
          <div
            v-if="mode === 'edit' && usage > 0"
            class="flex items-start gap-2 rounded border border-brass-300 bg-brass-200 px-3 py-2.5 text-[12px] text-[#5a4a1e]"
          >
            <Info class="mt-px h-4 w-4 shrink-0 text-[#a8842f]" />
            <span> Used by {{ usageLabel }}. Renaming updates it everywhere it appears. </span>
          </div>

          <FormError v-if="error" :error="error" />
        </div>

        <!-- Footer -->
        <div
          class="flex items-center gap-2.5 border-t border-[#eef0f1] bg-[#fafbfb] px-[18px] py-[14px]"
        >
          <!-- Delete / in-use lock (edit only) -->
          <template v-if="mode === 'edit'">
            <button
              v-if="usage === 0"
              type="button"
              class="inline-flex items-center gap-1.5 rounded px-2.5 py-1.5 text-[14px] font-semibold text-caution transition-colors hover:bg-caution-bg disabled:opacity-50"
              :disabled="saving"
              @click="remove"
            >
              <Trash2 class="h-4 w-4" /> Delete
            </button>
            <span
              v-else
              class="inline-flex cursor-not-allowed items-center gap-1.5 rounded px-2.5 py-1.5 text-[14px] font-semibold text-faint"
              title="In use — reassign before deleting"
            >
              <Lock class="h-4 w-4" /> In use
            </span>
          </template>

          <div class="ml-auto flex items-center gap-2.5">
            <button
              type="button"
              class="rounded border border-[#c2c6ca] bg-white px-[18px] py-[9px] text-[14px] font-semibold text-ink-700 transition-colors hover:bg-[#f5f6f7]"
              @click="$emit('close')"
            >
              Cancel
            </button>
            <button
              type="button"
              class="inline-flex items-center gap-[7px] rounded border border-[#b08a2e] bg-brass px-[18px] py-[9px] text-[14px] font-semibold text-ink-900 transition-colors hover:bg-brass-600 disabled:cursor-not-allowed disabled:border-[#ddd6c2] disabled:bg-[#e7e2d2] disabled:text-[#a79f88]"
              :disabled="!canSave"
              @click="save"
            >
              <LoaderCircle v-if="saving" class="h-4 w-4 animate-spin" />
              {{ saveLabel }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import { X, Crosshair, Target, Info, Trash2, Lock, LoaderCircle } from 'lucide-vue-next';
import { useCalibersStore } from '@/stores/calibers';
import { usePurposesStore } from '@/stores/purposes';
import { useReferenceStore } from '@/stores/reference';
import FormError from '@/components/FormError.vue';

const props = defineProps({
  /** @type {'caliber' | 'purpose'} */
  type: { type: String, required: true },
  /** @type {'add' | 'edit'} */
  mode: { type: String, default: 'add' },
  /** Existing item when editing: { id, label, caliber, caliber_type_id, firearms_count, loads_count } */
  item: { type: Object, default: null },
});

const emit = defineEmits(['close', 'saved', 'deleted']);

const calibersStore = useCalibersStore();
const purposesStore = usePurposesStore();
const referenceStore = useReferenceStore();

const isCaliber = computed(() => props.type === 'caliber');
const caliberTypes = computed(() => referenceStore.caliberType);

const labelInput = ref(null);
const saving = ref(false);
const error = ref(null);

const form = ref({
  label: props.item?.label ?? '',
  // Show the official name only when it is distinct from the short label.
  official:
    props.item?.caliber && props.item.caliber !== props.item.label ? props.item.caliber : '',
  caliber_type_id: props.item?.caliber_type_id ?? null,
});

onMounted(async () => {
  if (isCaliber.value && !form.value.caliber_type_id) {
    form.value.caliber_type_id = caliberTypes.value[0]?.id ?? null;
  }
  await nextTick();
  labelInput.value?.focus();
});

const usage = computed(() => {
  if (!props.item) return 0;
  return isCaliber.value
    ? (props.item.firearms_count ?? 0) + (props.item.loads_count ?? 0)
    : (props.item.loads_count ?? 0);
});

const usageLabel = computed(() => {
  if (!props.item) return '';
  if (isCaliber.value) {
    const parts = [];
    if (props.item.firearms_count) {
      parts.push(pluralize(props.item.firearms_count, 'firearm'));
    }
    if (props.item.loads_count) {
      parts.push(pluralize(props.item.loads_count, 'load'));
    }
    return parts.join(' · ');
  }
  return pluralize(props.item.loads_count, 'load');
});

function pluralize(count, noun) {
  return `${count} ${noun}${count === 1 ? '' : 's'}`;
}

const kindIcon = computed(() => (isCaliber.value ? Crosshair : Target));
const kindSubline = computed(() =>
  isCaliber.value ? 'Caliber · reference list' : 'Purpose · reference list'
);

const title = computed(() => {
  const noun = isCaliber.value ? 'caliber' : 'purpose';
  return props.mode === 'edit' ? `Edit ${noun}` : `Add ${noun}`;
});

const saveLabel = computed(() => {
  if (saving.value) return 'Saving…';
  if (props.mode === 'edit') return 'Save changes';
  return isCaliber.value ? 'Add caliber' : 'Add purpose';
});

const canSave = computed(() => form.value.label.trim().length > 0 && !saving.value);

async function save() {
  if (!canSave.value) return;
  error.value = null;
  saving.value = true;
  try {
    const label = form.value.label.trim();
    let payload;
    let result;

    if (isCaliber.value) {
      // Backend requires a non-null `caliber` (official) — fall back to the label
      // when no distinct official name was entered.
      const official = form.value.official.trim();
      payload = {
        label,
        caliber: official || label,
        caliber_type_id: form.value.caliber_type_id,
      };
      result =
        props.mode === 'edit'
          ? await calibersStore.update(props.item.id, payload)
          : await calibersStore.create(payload);
    } else {
      payload = { label };
      result =
        props.mode === 'edit'
          ? await purposesStore.update(props.item.id, payload)
          : await purposesStore.create(payload);
    }

    emit('saved', result.data);
  } catch (err) {
    if (err.response?.data?.errors) err.errorBag = err.response.data.errors;
    error.value = err;
  } finally {
    saving.value = false;
  }
}

async function remove() {
  if (usage.value > 0) return;
  error.value = null;
  saving.value = true;
  try {
    if (isCaliber.value) {
      await calibersStore.remove(props.item.id);
    } else {
      await purposesStore.remove(props.item.id);
    }
    emit('deleted', props.item.id);
  } catch (err) {
    error.value = err;
  } finally {
    saving.value = false;
  }
}
</script>
