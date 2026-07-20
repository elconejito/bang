<script setup>
import { ref } from 'vue';
import { Archive, LoaderCircle, X } from 'lucide-vue-next';

defineProps({
  entityLabel: { type: String, required: true },
  saving: { type: Boolean, default: false },
  error: { type: String, default: null },
  effectText: { type: String, default: 'Any current assignment will be cleared automatically.' },
});

const emit = defineEmits(['archive', 'close']);
const reason = ref('');
const description = ref('');
const reasons = [
  ['sold', 'Sold'],
  ['transferred', 'Transferred'],
  ['repair', 'Repair'],
  ['broken', 'Broken'],
  ['retired', 'Retired'],
  ['lost', 'Lost'],
  ['stolen', 'Stolen'],
  ['destroyed', 'Destroyed'],
  ['other', 'Other'],
];

function submit() {
  if (!reason.value) return;
  emit('archive', {
    reason: reason.value,
    description: description.value.trim() || null,
  });
}
</script>

<template>
  <div class="modal-scrim z-40 p-4 sm:p-12" @click.self="emit('close')">
    <div
      class="modal-shell w-[500px] max-w-full"
      role="dialog"
      aria-modal="true"
      aria-labelledby="archive-entity-title"
    >
      <div class="flex items-center justify-between gap-3 border-b border-[#eef0f1] px-[18px] py-4">
        <div>
          <h2 id="archive-entity-title" class="font-display text-[18px] font-semibold">
            Archive {{ entityLabel }}
          </h2>
          <p class="mt-0.5 text-[13px] text-muted">
            This item will be removed from active lists and assignments.
          </p>
        </div>
        <button
          type="button"
          class="p-0.5 text-[#8a9098] transition-colors hover:text-[#1a1c1f]"
          aria-label="Close archive dialog"
          @click="emit('close')"
        >
          <X class="h-[18px] w-[18px]" />
        </button>
      </div>

      <div class="flex flex-col gap-5 px-[18px] py-5">
        <div>
          <label
            for="entity-archive-reason"
            class="mb-1.5 block text-[13px] font-semibold text-[#3a3e44]"
            >Reason</label
          >
          <select
            id="entity-archive-reason"
            v-model="reason"
            class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[14px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
          >
            <option value="" disabled>Select a reason</option>
            <option v-for="[value, label] in reasons" :key="value" :value="value">
              {{ label }}
            </option>
          </select>
        </div>
        <div>
          <label
            for="entity-archive-description"
            class="mb-1.5 block text-[13px] font-semibold text-[#3a3e44]"
            >Details <span class="font-normal text-muted">(optional)</span></label
          >
          <textarea
            id="entity-archive-description"
            v-model="description"
            rows="3"
            maxlength="2000"
            placeholder="Add sale, transfer, repair, or other relevant details…"
            class="w-full resize-y rounded border border-[#c2c6ca] px-3 py-2.5 text-[14px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
          />
        </div>
        <p
          class="rounded border border-[#dfc98d] bg-[#fbf6e8] px-3 py-2 text-[13px] text-[#604c18]"
        >
          {{ effectText }}
        </p>
        <p
          v-if="error"
          class="rounded border border-[#e8b8ad] bg-[#fff3f0] px-3 py-2 text-[13px] text-[#a33d29]"
          role="alert"
        >
          {{ error }}
        </p>
      </div>

      <div
        class="flex items-center justify-end gap-2.5 border-t border-[#eef0f1] bg-[#fafbfb] px-[18px] py-3.5"
      >
        <button
          type="button"
          class="rounded border border-[#c2c6ca] bg-white px-4 py-2 text-[14px] font-semibold text-[#3a3e44] hover:bg-[#f5f6f7]"
          @click="emit('close')"
        >
          Cancel
        </button>
        <button
          type="button"
          :disabled="saving || !reason"
          class="inline-flex items-center gap-2 rounded border border-[#765f22] bg-[#7d6320] px-4 py-2 text-[14px] font-semibold text-white hover:bg-[#665118] disabled:opacity-50"
          @click="submit"
        >
          <LoaderCircle v-if="saving" class="h-4 w-4 animate-spin" />
          <Archive v-else class="h-4 w-4" />
          {{ saving ? 'Archiving…' : 'Archive' }}
        </button>
      </div>
    </div>
  </div>
</template>
