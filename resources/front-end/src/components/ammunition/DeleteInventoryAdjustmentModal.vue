<template>
  <Teleport to="body">
    <div class="modal-scrim z-50 px-4 pb-6 pt-8 sm:px-6 sm:pt-14" @click.self="close">
      <section
        class="modal-shell w-[440px] max-w-full"
        role="dialog"
        aria-modal="true"
        aria-labelledby="delete-adjustment-title"
      >
        <header class="border-b border-line px-[18px] py-4">
          <h2 id="delete-adjustment-title" class="font-display text-[19px] font-semibold">
            Delete adjustment?
          </h2>
        </header>

        <div class="flex flex-col gap-3 p-[18px]">
          <p class="text-[14px] text-ink-700">
            Delete the adjustment from <span class="font-medium">{{ adjustmentDate }}</span> for
            <span class="font-mono font-medium">{{ signedRounds }} rounds</span>?
          </p>
          <p class="text-[13px] text-muted">
            On-hand inventory and later ledger balances will recalculate.
          </p>
          <p class="text-[13px] text-muted">This cannot be undone.</p>
          <FormError v-if="error" :error="error" />
        </div>

        <footer class="flex gap-2.5 border-t border-line bg-[#fafbfb] px-[18px] py-[14px]">
          <button
            type="button"
            class="inline-flex items-center gap-1.5 rounded border border-[#b4452f] bg-[#b4452f] px-[15px] py-2 text-[14px] font-semibold text-white transition-colors hover:bg-[#933827] disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="saving"
            @click="remove"
          >
            <LoaderCircle v-if="saving" class="h-4 w-4 animate-spin" />
            <Trash2 v-else class="h-4 w-4" />
            {{ saving ? 'Deleting…' : 'Delete adjustment' }}
          </button>
          <button type="button" class="detail-action" :disabled="saving" @click="close">
            Cancel
          </button>
        </footer>
      </section>
    </div>
  </Teleport>
</template>

<script setup>
import { computed, ref } from 'vue';
import { LoaderCircle, Trash2 } from 'lucide-vue-next';
import dayjs from 'dayjs';
import { useInventoriesStore } from '@/stores/inventories';
import FormError from '@/components/FormError.vue';

const props = defineProps({ entry: { type: Object, required: true } });
const emit = defineEmits(['close', 'deleted']);
const inventoriesStore = useInventoriesStore();
const saving = ref(false);
const error = ref(null);

const adjustmentDate = computed(() => dayjs(props.entry.inventory_date).format('MMM D, YYYY'));
const signedRounds = computed(
  () => `${props.entry.rounds >= 0 ? '+' : ''}${props.entry.rounds.toLocaleString()}`
);

function close() {
  if (!saving.value) emit('close');
}

async function remove() {
  if (saving.value || props.entry.type !== 'ADJUST') return;

  saving.value = true;
  error.value = null;
  try {
    await inventoriesStore.remove(props.entry.id);
    emit('deleted', props.entry.id);
  } catch (exception) {
    error.value = exception;
  } finally {
    saving.value = false;
  }
}
</script>
