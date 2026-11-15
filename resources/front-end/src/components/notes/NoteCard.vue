<script setup>
import { computed, nextTick, ref } from 'vue';
import { LoaderCircle, Pencil, Trash2 } from 'lucide-vue-next';
import { useDateTimes } from '@/composables/useDateTimes';

const props = defineProps({
  note: { type: Object, required: true },
  updateNote: { type: Function, required: true },
  deleteNote: { type: Function, required: true },
});
const { ago } = useDateTimes();
const editing = ref(false);
const confirmingDelete = ref(false);
const draft = ref('');
const busy = ref(false);
const error = ref('');
const input = ref(null);
const editButton = ref(null);
const deleteButton = ref(null);
const deleteCancelButton = ref(null);
const wasUpdated = computed(
  () =>
    props.note.updated_at &&
    new Date(props.note.updated_at).getTime() !== new Date(props.note.created_at).getTime()
);
const canSave = computed(() => draft.value.trim().length > 0 && !busy.value);
const exactDate = (value) => new Date(value).toLocaleString();

async function startEditing() {
  draft.value = props.note.note;
  error.value = '';
  editing.value = true;
  await nextTick();
  input.value?.focus();
}

async function startDeleting() {
  confirmingDelete.value = true;
  error.value = '';
  await nextTick();
  deleteCancelButton.value?.focus();
}

async function cancel() {
  if (busy.value) return;
  const wasEditing = editing.value;
  editing.value = false;
  confirmingDelete.value = false;
  error.value = '';
  await nextTick();
  (wasEditing ? editButton.value : deleteButton.value)?.focus();
}

async function save() {
  if (!canSave.value) return;
  busy.value = true;
  error.value = '';
  try {
    await props.updateNote(props.note.id, draft.value.trim());
    editing.value = false;
  } catch (failure) {
    error.value = failure.response?.data?.message ?? 'The note could not be saved.';
  } finally {
    busy.value = false;
  }
}

async function remove() {
  if (busy.value) return;
  busy.value = true;
  error.value = '';
  try {
    await props.deleteNote(props.note.id);
    confirmingDelete.value = false;
  } catch (failure) {
    error.value = failure.response?.data?.message ?? 'The note could not be deleted.';
  } finally {
    busy.value = false;
  }
}
</script>

<template>
  <article class="border-b border-[#eef0f1] px-4 py-3.5 last:border-b-0" :aria-busy="busy">
    <div v-if="confirmingDelete" class="rounded border border-line bg-[#fafbfb] p-3">
      <p class="text-[14px] text-[#3a3e44]">Delete this note? This cannot be undone.</p>
      <div class="mt-3 flex items-center gap-3">
        <button
          type="button"
          class="rounded bg-[#b4452f] px-3 py-1.5 text-[13px] font-semibold text-white disabled:opacity-50"
          :disabled="busy"
          @click="remove"
        >
          <LoaderCircle v-if="busy" class="inline h-3.5 w-3.5 animate-spin" />
          Delete note
        </button>
        <button
          ref="deleteCancelButton"
          type="button"
          class="text-[13px] text-muted"
          :disabled="busy"
          @click="cancel"
        >
          Cancel
        </button>
      </div>
    </div>
    <div v-else-if="editing">
      <textarea
        ref="input"
        v-model="draft"
        aria-label="Edit note text"
        rows="4"
        maxlength="10000"
        :disabled="busy"
        class="w-full resize-y rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-[14px] leading-relaxed outline-none focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
        @keydown.ctrl.enter="save"
        @keydown.meta.enter="save"
        @keydown.esc="cancel"
      />
      <div class="mt-2 flex items-center gap-3">
        <button
          type="button"
          class="inline-flex items-center gap-1.5 rounded bg-ink-900 px-3 py-1.5 text-[13px] font-semibold text-white disabled:opacity-50"
          :disabled="!canSave"
          @click="save"
        >
          <LoaderCircle v-if="busy" class="h-3.5 w-3.5 animate-spin" />
          Save
        </button>
        <button type="button" class="text-[13px] text-muted" :disabled="busy" @click="cancel">
          Cancel
        </button>
      </div>
    </div>
    <div v-else class="whitespace-pre-wrap break-words text-[14px] leading-[1.6] text-[#3a3e44]">
      {{ note.note }}
    </div>
    <p v-if="error" role="alert" class="mt-2 text-[12px] text-[#b4452f]">{{ error }}</p>
    <div class="mt-2 flex items-center justify-between gap-3">
      <div class="min-w-0 font-mono text-[10px] tracking-[0.03em] text-muted">
        Created
        <time :datetime="note.created_at" :title="exactDate(note.created_at)">{{
          ago(note.created_at)
        }}</time>
        <span v-if="wasUpdated">
          · Updated at
          <time :datetime="note.updated_at" :title="exactDate(note.updated_at)">{{
            ago(note.updated_at)
          }}</time></span
        >
      </div>
      <div v-if="!editing && !confirmingDelete" class="flex shrink-0 items-center gap-2">
        <button
          type="button"
          ref="editButton"
          aria-label="Edit note"
          title="Edit note"
          class="rounded p-1 text-muted hover:text-ink-900"
          @click="startEditing"
        >
          <Pencil class="h-3.5 w-3.5" />
        </button>
        <button
          type="button"
          ref="deleteButton"
          aria-label="Delete note"
          title="Delete note"
          class="rounded p-1 text-muted hover:text-[#b4452f]"
          @click="startDeleting"
        >
          <Trash2 class="h-3.5 w-3.5" />
        </button>
      </div>
    </div>
  </article>
</template>
