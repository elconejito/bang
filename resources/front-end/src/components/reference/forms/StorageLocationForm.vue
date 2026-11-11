<template>
  <ReferenceModalShell
    :icon="meta.icon"
    :title="title"
    :kind-subline="meta.kindSubline"
    :mode="mode"
    :saving="saving"
    :can-save="canSave && !loadingOptions && !loadError"
    :save-label="saveLabel"
    :can-delete="canDelete"
    :delete-blocked="deleteBlocked"
    :usage-note="usageNote"
    :error="error"
    @close="$emit('close')"
    @save="submit"
    @delete="destroy"
  >
    <LoadingState v-if="loadingOptions" message="Loading location options…" />

    <ErrorCard
      v-else-if="loadError"
      :error="loadError"
      title="Could not load location options"
      retry-label="Try again"
      @retry="loadOptions"
    />

    <div v-else class="flex flex-col gap-4">
      <div class="flex flex-col gap-1.5">
        <label for="ref-label" class="text-[13px] font-semibold text-ink-700">
          {{ meta.field.label }}
          <span v-if="meta.field.labelSub" class="font-normal text-muted">{{
            meta.field.labelSub
          }}</span>
        </label>
        <input
          id="ref-label"
          ref="labelInput"
          v-model="form.label"
          type="text"
          :placeholder="meta.field.placeholder"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-brass-200"
          @keydown.enter.prevent="canSave && submit()"
        />
        <p class="text-[12px] text-muted">{{ meta.field.hint }}</p>
      </div>

      <div class="flex flex-col gap-1.5">
        <label for="ref-location-description" class="text-[13px] font-semibold text-ink-700">
          Description
          <span class="font-normal text-muted">· optional</span>
        </label>
        <textarea
          id="ref-location-description"
          v-model="form.description"
          rows="3"
          placeholder="Notes about this location…"
          class="w-full resize-none rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-brass-200"
        />
      </div>

      <div class="flex flex-col gap-1.5">
        <label for="ref-parent-location" class="text-[13px] font-semibold text-ink-700">
          Inside location
          <span class="font-normal text-muted">· optional</span>
        </label>
        <select
          id="ref-parent-location"
          v-model="form.parent_location_id"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-brass-200"
        >
          <option value="">No parent location</option>
          <option v-for="location in parentOptions" :key="location.id" :value="location.id">
            {{ location.full_label ?? location.label }}
          </option>
        </select>
        <p class="text-[12px] text-muted">
          Choose where this location sits, such as Gun Safe › Top Shelf.
        </p>
      </div>
    </div>
  </ReferenceModalShell>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useLocationsStore } from '@/stores/locations';
import { useReferenceForm } from '@/components/reference/useReferenceForm';
import ReferenceModalShell from '@/components/reference/ReferenceModalShell.vue';
import ErrorCard from '@/components/status/ErrorCard.vue';

const props = defineProps({
  /** @type {'add' | 'edit'} */
  mode: { type: String, default: 'add' },
  item: { type: Object, default: null },
});

const emit = defineEmits(['close', 'saved', 'deleted']);

const store = useLocationsStore();
const locations = ref([]);
const loadingOptions = ref(true);
const loadError = ref(null);
const parentOptions = computed(() => {
  if (!props.item) {
    return locations.value;
  }

  const excludedIds = new Set([props.item.id]);
  let foundDescendant = true;

  while (foundDescendant) {
    foundDescendant = false;
    for (const location of locations.value) {
      if (
        location.parent_location_id &&
        excludedIds.has(location.parent_location_id) &&
        !excludedIds.has(location.id)
      ) {
        excludedIds.add(location.id);
        foundDescendant = true;
      }
    }
  }

  return locations.value.filter((location) => !excludedIds.has(location.id));
});
const {
  meta,
  form,
  labelInput,
  saving,
  error,
  title,
  canSave,
  saveLabel,
  usageNote,
  canDelete,
  deleteBlocked,
  submit: submitReferenceForm,
  destroy,
} = useReferenceForm({
  type: 'location',
  props,
  emit,
  create: store.create,
  update: store.update,
  remove: store.remove,
  additionalFields: ['parent_location_id', 'description'],
});

async function loadOptions() {
  loadingOptions.value = true;
  loadError.value = null;

  try {
    const { data } = await store.fetchAll();
    locations.value = data ?? [];
  } catch (exception) {
    loadError.value = exception;
  } finally {
    loadingOptions.value = false;
  }
}

async function submit() {
  if (loadingOptions.value || loadError.value) return;

  await submitReferenceForm();
}

onMounted(loadOptions);
</script>
