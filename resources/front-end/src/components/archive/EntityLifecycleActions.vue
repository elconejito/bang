<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { Archive, RotateCcw, Trash2 } from 'lucide-vue-next';
import ArchiveEntityModal from '@/components/archive/ArchiveEntityModal.vue';

const props = defineProps({
  entityId: { type: Number, required: true },
  entityLabel: { type: String, required: true },
  status: { type: String, required: true },
  archiveAction: { type: Function, required: true },
  unarchiveAction: { type: Function, required: true },
  destroyAction: { type: Function, required: true },
  returnRoute: { type: [String, Object], required: true },
  effectText: { type: String, default: undefined },
});

const emit = defineEmits(['updated', 'activity-changed']);
const router = useRouter();
const showModal = ref(false);
const saving = ref(false);
const archiveError = ref(null);
const error = ref(null);

async function archiveEntity(payload) {
  saving.value = true;
  archiveError.value = null;
  try {
    const response = await props.archiveAction(props.entityId, payload);
    emit('updated', response.data);
    emit('activity-changed');
    showModal.value = false;
  } catch (exception) {
    archiveError.value = exception.response?.data?.message ?? 'This item could not be archived.';
  } finally {
    saving.value = false;
  }
}

async function unarchiveEntity() {
  saving.value = true;
  error.value = null;
  try {
    const response = await props.unarchiveAction(props.entityId);
    emit('updated', response.data);
    emit('activity-changed');
  } catch (exception) {
    error.value = exception.response?.data?.message ?? 'This item could not be unarchived.';
  } finally {
    saving.value = false;
  }
}

async function deleteEntity() {
  if (!window.confirm(`Permanently delete ${props.entityLabel}? This cannot be undone.`)) return;
  saving.value = true;
  error.value = null;
  try {
    await props.destroyAction(props.entityId);
    await router.push(props.returnRoute);
  } catch (exception) {
    const blockers = exception.response?.data?.blockers ?? [];
    error.value = blockers.length
      ? blockers.map((blocker) => blocker.message).join(' ')
      : (exception.response?.data?.message ?? 'This item could not be deleted.');
  } finally {
    saving.value = false;
  }
}
</script>

<template>
  <div class="flex flex-col items-end gap-2">
    <div class="flex flex-wrap items-center justify-end gap-2.5">
      <button
        v-if="status !== 'archived'"
        type="button"
        class="detail-action"
        @click="showModal = true"
      >
        <Archive class="h-[15px] w-[15px]" /> Archive
      </button>
      <button
        v-else
        type="button"
        :disabled="saving"
        class="detail-action"
        @click="unarchiveEntity"
      >
        <RotateCcw class="h-[15px] w-[15px]" /> {{ saving ? 'Restoring…' : 'Unarchive' }}
      </button>
      <button
        v-if="status === 'archived'"
        type="button"
        :disabled="saving"
        class="detail-action border-[#d08a7b] text-[#a33d29] hover:bg-[#fff3f0]"
        @click="deleteEntity"
      >
        <Trash2 class="h-[15px] w-[15px]" /> Delete permanently
      </button>
    </div>
    <p v-if="error" class="max-w-[420px] text-right text-[12px] text-[#a33d29]" role="alert">
      {{ error }}
    </p>
  </div>

  <ArchiveEntityModal
    v-if="showModal"
    :entity-label="entityLabel"
    :saving="saving"
    :error="archiveError"
    :effect-text="effectText"
    @archive="archiveEntity"
    @close="showModal = false"
  />
</template>
