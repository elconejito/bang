<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { ChevronLeft, ChevronRight, LoaderCircle, Plus, Search, X } from 'lucide-vue-next';
import NotesList from '@/components/notes/NotesList.vue';
import { useNotesStore } from '@/stores/notes';

const props = defineProps({
  entityType: { type: String, required: true },
  entityId: { type: Number, required: true },
  compact: { type: Boolean, default: false },
});

const notesStore = useNotesStore();
const notes = ref([]);
const meta = ref({ current_page: 1, last_page: 1, per_page: 10, total: 0 });
const search = ref('');
const page = ref(1);
const loading = ref(true);
const failed = ref(false);
const composing = ref(false);
const draft = ref('');
const saving = ref(false);
const saveError = ref('');
let searchTimer;
let fetchSequence = 0;

const canSave = computed(() => draft.value.trim().length > 0 && !saving.value);

async function loadNotes() {
  const sequence = ++fetchSequence;
  loading.value = true;
  failed.value = false;

  try {
    const response = await notesStore.fetchAll(props.entityType, props.entityId, {
      page: page.value,
      per_page: 10,
      search: search.value.trim() || undefined,
    });

    if (sequence === fetchSequence) {
      notes.value = response.data;
      meta.value = response.meta;
    }
  } catch {
    if (sequence === fetchSequence) failed.value = true;
  } finally {
    if (sequence === fetchSequence) loading.value = false;
  }
}

function openComposer() {
  composing.value = true;
  saveError.value = '';
}

function closeComposer() {
  composing.value = false;
  draft.value = '';
  saveError.value = '';
}

async function addNote() {
  if (!canSave.value) return;

  saving.value = true;
  saveError.value = '';

  try {
    await notesStore.create(props.entityType, props.entityId, { note: draft.value.trim() });
    closeComposer();
    page.value = 1;

    if (search.value) {
      search.value = '';
    } else {
      await loadNotes();
    }
  } catch (error) {
    saveError.value = error.response?.data?.message ?? 'The note could not be saved.';
  } finally {
    saving.value = false;
  }
}

function goToPage(nextPage) {
  if (nextPage < 1 || nextPage > meta.value.last_page || nextPage === page.value) return;
  page.value = nextPage;
  loadNotes();
}

watch(search, () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    page.value = 1;
    loadNotes();
  }, 250);
});

watch(
  () => [props.entityType, props.entityId],
  () => {
    page.value = 1;
    search.value = '';
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
        v-if="!composing"
        type="button"
        class="inline-flex items-center gap-1.5 rounded border border-[#b08a2e] bg-brass px-3 py-1.5 text-[13px] font-semibold text-[#1a1c1f] transition-colors hover:bg-[#b8902f]"
        @click="openComposer"
      >
        <Plus class="h-3.5 w-3.5" />
        Add Note
      </button>
    </div>

    <div v-if="composing" class="border-b border-[#eef0f1] bg-[#fafbfb] p-4">
      <div class="mb-2 flex items-center justify-between gap-3">
        <label
          class="text-[13px] font-semibold text-[#3a3e44]"
          :for="`note-${entityType}-${entityId}`"
        >
          New note
        </label>
        <button
          type="button"
          class="text-muted hover:text-ink-900"
          aria-label="Cancel note"
          @click="closeComposer"
        >
          <X class="h-4 w-4" />
        </button>
      </div>
      <textarea
        :id="`note-${entityType}-${entityId}`"
        v-model="draft"
        rows="4"
        maxlength="10000"
        placeholder="Add something you want to remember…"
        class="w-full resize-y rounded border border-[#c2c6ca] bg-white px-3 py-2.5 text-[14px] leading-relaxed outline-none placeholder:text-muted focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
        @keydown.ctrl.enter="addNote"
        @keydown.meta.enter="addNote"
      />
      <div class="mt-2 flex flex-wrap items-center justify-between gap-3">
        <span class="text-[12px] text-muted">Ctrl/⌘ + Enter to save</span>
        <div class="flex items-center gap-3">
          <span v-if="saveError" class="text-[12px] text-[#b4452f]">{{ saveError }}</span>
          <button
            type="button"
            class="inline-flex items-center gap-1.5 rounded bg-ink-900 px-3 py-1.5 text-[13px] font-semibold text-white transition-colors hover:bg-black disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="!canSave"
            @click="addNote"
          >
            <LoaderCircle v-if="saving" class="h-3.5 w-3.5 animate-spin" />
            Save Note
          </button>
        </div>
      </div>
    </div>

    <div class="border-b border-[#eef0f1] px-4 py-3">
      <label class="relative block">
        <Search
          class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted"
        />
        <input
          v-model="search"
          type="search"
          placeholder="Search notes…"
          class="h-9 w-full rounded border border-[#c2c6ca] bg-white pl-9 pr-3 text-[13px] outline-none placeholder:text-muted focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
        />
      </label>
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
