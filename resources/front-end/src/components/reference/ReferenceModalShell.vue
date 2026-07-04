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
            <component :is="icon" class="h-[17px] w-[17px]" />
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
          <slot />

          <!-- In-use note (edit + in use) -->
          <div
            v-if="usageNote"
            class="flex items-start gap-2 rounded border border-brass-300 bg-brass-200 px-3 py-2.5 text-[12px] text-[#5a4a1e]"
          >
            <Info class="mt-px h-4 w-4 shrink-0 text-[#a8842f]" />
            <span>{{ usageNote }}</span>
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
              v-if="canDelete"
              type="button"
              class="inline-flex items-center gap-1.5 rounded px-2.5 py-1.5 text-[14px] font-semibold text-caution transition-colors hover:bg-caution-bg disabled:opacity-50"
              :disabled="saving"
              @click="$emit('delete')"
            >
              <Trash2 class="h-4 w-4" /> Delete
            </button>
            <span
              v-else-if="deleteBlocked"
              class="inline-flex cursor-not-allowed items-center gap-1.5 rounded px-2.5 py-1.5 text-[14px] font-semibold text-faint"
              :title="blockedTitle"
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
              @click="$emit('save')"
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
import { X, Info, Trash2, Lock, LoaderCircle } from 'lucide-vue-next';
import FormError from '@/components/FormError.vue';

/**
 * Presentational chrome for every reference Add/Edit modal: scrim, panel, header,
 * footer (Cancel / Save + a Delete / in-use slot). It owns no data — the per-model
 * form components render their fields into the default slot and drive the props.
 */
defineProps({
  /** Header icon component (from referenceMeta). */
  icon: { type: [Object, Function], required: true },
  title: { type: String, required: true },
  kindSubline: { type: String, default: '' },
  /** @type {'add' | 'edit'} */
  mode: { type: String, default: 'add' },
  saving: { type: Boolean, default: false },
  canSave: { type: Boolean, default: false },
  saveLabel: { type: String, default: 'Save' },
  canDelete: { type: Boolean, default: false },
  deleteBlocked: { type: Boolean, default: false },
  blockedTitle: { type: String, default: 'In use — reassign before deleting' },
  /** In-use callout text, shown when editing an item that is referenced. */
  usageNote: { type: String, default: null },
  /** Optional error surfaced beneath the fields. */
  error: { type: [Object, Error], default: null },
});

defineEmits(['close', 'save', 'delete']);
</script>
