<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { ChevronLeft, ChevronRight, LoaderCircle, Search } from 'lucide-vue-next';
import NotesList from '@/components/notes/NotesList.vue';
import { useNotesStore } from '@/stores/notes';

const props = defineProps({
  entityType: { type: String, required: true },
  entityId: { type: Number, required: true },
  compact: { type: Boolean, default: false },
  hideEmptySearch: { type: Boolean, default: false },
});

const notesStore = useNotesStore();
const notes = ref([]);
const meta = ref({ current_page: 1, last_page: 1, per_page: 10, total: 0 });
const hasNotes = ref(false);
const search = ref('');
const searchOpen = ref(false);
const searchField = ref(null);
const searchToggle = ref(null);
const page = ref(1);
const loading = ref(true);
const failed = ref(false);
const composing = ref(false);
const draft = ref('');
const saving = ref(false);
const saveError = ref('');
let searchTimer;
let fetchSequence = 0;
let entityVersion = 0;

const canSave = computed(() => draft.value.trim().length > 0 && !saving.value);

async function loadNotes() {
  const sequence = ++fetchSequence;
  const searchTerm = search.value.trim();
  loading.value = true;
  failed.value = false;

  try {
    const response = await notesStore.fetchAll(props.entityType, props.entityId, {
      page: page.value,
      per_page: 10,
      search: searchTerm || undefined,
    });

    if (sequence === fetchSequence) {
      notes.value = response.data;
      meta.value = response.meta;
      if (!searchTerm) {
        hasNotes.value = response.meta.total > 0;
        if (props.hideEmptySearch && !hasNotes.value && !search.value) searchOpen.value = false;
      }
    }
  } catch {
    if (sequence === fetchSequence) failed.value = true;
  } finally {
    if (sequence === fetchSequence) loading.value = false;
  }
}

async function toggleSearch() {
  searchOpen.value = !searchOpen.value;
  if (!searchOpen.value && search.value) {
    resetSearch();
    loadNotes();
  }
  await nextTick();
  (searchOpen.value ? searchField.value : searchToggle.value)?.focus();
}

function resetSearch() {
  clearTimeout(searchTimer);
  searchOpen.value = false;
  search.value = '';
  page.value = 1;
}

function closeComposer() {
  composing.value = false;
  draft.value = '';
  saveError.value = '';
}

function collapseEmptyComposer(event) {
  if (!draft.value && !event.currentTarget.contains(event.relatedTarget)) composing.value = false;
}

async function addNote() {
  if (!canSave.value) return;

  const version = entityVersion;
  saving.value = true;
  saveError.value = '';

  try {
    await notesStore.create(props.entityType, props.entityId, { note: draft.value.trim() });
    if (version !== entityVersion) return;
    closeComposer();
    resetSearch();
    await loadNotes();
  } catch (error) {
    if (version === entityVersion)
      saveError.value = error.response?.data?.message ?? 'The note could not be saved.';
  } finally {
    if (version === entityVersion) saving.value = false;
  }
}

async function refreshAfterMutation() {
  await loadNotes();
  if (!failed.value && page.value > meta.value.last_page) {
    page.value = Math.max(1, meta.value.last_page);
    await loadNotes();
  }
}

async function updateNote(noteId, text) {
  const response = await notesStore.update(props.entityType, props.entityId, noteId, {
    note: text,
  });
  notes.value = notes.value.map((note) => (note.id === noteId ? response.data : note));
  if (search.value.trim()) await refreshAfterMutation();
}

async function deleteNote(noteId) {
  await notesStore.remove(props.entityType, props.entityId, noteId);
  notes.value = notes.value.filter((note) => note.id !== noteId);
  await refreshAfterMutation();
}

function goToPage(nextPage) {
  if (nextPage < 1 || nextPage > meta.value.last_page || nextPage === page.value) return;
  page.value = nextPage;
  loadNotes();
}

function scheduleSearch() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    page.value = 1;
    loadNotes();
  }, 250);
}

watch(
  () => [props.entityType, props.entityId],
  () => {
    entityVersion++;
    closeComposer();
    saving.value = false;
    hasNotes.value = false;
    resetSearch();
    loadNotes();
  }
);

onMounted(loadNotes);
onBeforeUnmount(() => clearTimeout(searchTimer));
</script>

<template>
  <section class="overflow-hidden rounded border border-line bg-white">
    <div
      class="flex flex-wrap items-center gap-3 border-b border-[#eef0f1] px-4 py-3"
      :class="compact ? 'bg-[#fafbfb]' : ''"
    >
      <div class="min-w-0 flex-1">
        <div class="flex items-baseline gap-2">
          <h2 class="font-display text-[16px] font-semibold">Notes</h2>
          <span v-if="meta.total" class="font-mono text-[10px] tracking-[0.04em] text-muted">
            {{ meta.total }} TOTAL
          </span>
        </div>
      </div>
      <button
        v-if="!hideEmptySearch || hasNotes || searchOpen"
        ref="searchToggle"
        type="button"
        aria-label="Search notes"
        title="Search notes"
        :aria-expanded="searchOpen"
        :aria-controls="`notes-search-${entityType}-${entityId}`"
        class="inline-flex h-9 w-9 items-center justify-center rounded text-muted hover:bg-ink-50 hover:text-ink-900 focus-visible:outline-brass"
        @click="toggleSearch"
      >
        <Search class="h-4 w-4" />
      </button>
    </div>

    <form
      class="border-b border-[#eef0f1] bg-[#fafbfb] p-4"
      @submit.prevent="addNote"
      @focusout="collapseEmptyComposer"
    >
      <textarea
        :id="`note-${entityType}-${entityId}`"
        v-model="draft"
        aria-label="New note"
        :rows="composing || draft ? 4 : 1"
        :readonly="saving"
        maxlength="10000"
        placeholder="Write a note…"
        class="block w-full resize-y rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-[14px] leading-relaxed outline-none placeholder:text-muted focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
        @focus="composing = true"
        @keydown.ctrl.enter.prevent="addNote"
        @keydown.meta.enter.prevent="addNote"
      />
      <div v-if="composing || draft" class="mt-2 flex flex-wrap items-center gap-3">
        <button
          type="submit"
          class="inline-flex items-center gap-1.5 rounded bg-ink-900 px-3 py-1.5 text-[13px] font-semibold text-white hover:bg-black disabled:cursor-not-allowed disabled:opacity-50"
          :disabled="!canSave"
        >
          <LoaderCircle v-if="saving" class="h-3.5 w-3.5 animate-spin" />
          Add note
        </button>
        <button
          type="button"
          :disabled="saving"
          class="text-[13px] text-muted hover:text-ink-900"
          @click="closeComposer"
        >
          Cancel
        </button>
        <span class="ml-auto text-[12px] text-muted">Ctrl/⌘ + Enter to save</span>
        <p v-if="saveError" role="alert" class="w-full text-[13px] text-[#b4452f]">
          {{ saveError }}
        </p>
      </div>
    </form>

    <div
      v-if="searchOpen"
      :id="`notes-search-${entityType}-${entityId}`"
      class="border-b border-[#eef0f1] px-4 py-3"
    >
      <input
        ref="searchField"
        v-model="search"
        @input="scheduleSearch"
        type="search"
        aria-label="Search notes"
        placeholder="Search notes…"
        class="h-9 w-full rounded border border-[#c2c6ca] bg-white px-3 text-[13px] outline-none placeholder:text-muted focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
        @keydown.esc.prevent="toggleSearch"
      />
    </div>

    <div
      v-if="loading"
      class="flex items-center justify-center gap-2 px-4 py-10 text-[13px] text-muted"
    >
      <LoaderCircle class="h-4 w-4 animate-spin" />
      Loading notes…
    </div>
    <div v-else-if="failed" class="px-4 py-8 text-center text-[13px] text-[#b4452f]">
      Notes could not be loaded.
      <button type="button" class="ml-1 font-semibold underline" @click="loadNotes">
        Try again
      </button>
    </div>
    <NotesList
      v-else
      :notes="notes"
      :update-note="updateNote"
      :delete-note="deleteNote"
      :empty-message="search ? 'No notes match your search' : 'No notes yet'"
    />

    <div
      v-if="!loading && !failed && meta.last_page > 1"
      class="flex items-center justify-between gap-3 border-t border-[#eef0f1] bg-[#fafbfb] px-4 py-3"
    >
      <span class="text-[12px] text-muted">
        Showing {{ meta.from }}–{{ meta.to }} of {{ meta.total }}
      </span>
      <div class="flex items-center gap-2">
        <button
          type="button"
          class="flex h-7 w-7 items-center justify-center rounded border border-line bg-white text-ink-700 disabled:opacity-40"
          :disabled="meta.current_page <= 1"
          aria-label="Previous notes page"
          @click="goToPage(meta.current_page - 1)"
        >
          <ChevronLeft class="h-4 w-4" />
        </button>
        <span class="font-mono text-[11px] text-muted"
          >{{ meta.current_page }} / {{ meta.last_page }}</span
        >
        <button
          type="button"
          class="flex h-7 w-7 items-center justify-center rounded border border-line bg-white text-ink-700 disabled:opacity-40"
          :disabled="meta.current_page >= meta.last_page"
          aria-label="Next notes page"
          @click="goToPage(meta.current_page + 1)"
        >
          <ChevronRight class="h-4 w-4" />
        </button>
      </div>
    </div>
  </section>
</template>
