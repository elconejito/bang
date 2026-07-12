<template>
  <Teleport to="body">
    <div
      class="fixed inset-0 z-50 flex items-start justify-center overflow-auto bg-[rgba(20,22,26,0.46)] px-6 pb-6 pt-14"
      @click.self="$emit('close')"
    >
      <form
        class="w-[484px] max-w-full overflow-hidden rounded border border-[#d6d9dc] bg-white shadow-[0_10px_30px_rgba(20,22,26,0.22),0_2px_8px_rgba(20,22,26,0.12)]"
        @submit.prevent="save"
      >
        <div class="flex items-center justify-between border-b border-line px-[18px] py-4">
          <div>
            <h2 class="font-display text-[19px] font-semibold">Edit inventory entry</h2>
            <p class="mt-0.5 text-[13px] text-muted">
              {{ entry.type === 'BUY' ? 'Purchase' : 'Adjustment' }}
            </p>
          </div>
          <button type="button" class="text-muted hover:text-ink-900" @click="$emit('close')">
            <X class="h-[18px] w-[18px]" />
          </button>
        </div>
        <div class="flex flex-col gap-4 p-[18px]">
          <div class="grid grid-cols-2 gap-[14px]">
            <div class="flex flex-col gap-1.5">
              <label class="text-[14px] font-medium">
                Rounds {{ entry.type === 'BUY' ? 'added' : '(±)' }}
              </label>
              <input
                v-model.number="form.rounds"
                type="number"
                :min="entry.type === 'BUY' ? 1 : undefined"
                required
                class="h-10 w-full rounded border border-[#c2c6ca] bg-white px-3 font-mono text-[18px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
              />
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="text-[14px] font-medium">Date</label>
              <input
                v-model="form.inventory_date"
                type="date"
                required
                class="h-10 w-full rounded border border-[#c2c6ca] bg-white px-3 text-[14px] font-mono focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
              />
            </div>
          </div>

          <div v-if="entry.type === 'BUY'" class="border-t border-[#eef0f1] pt-[15px]">
            <div class="text-[15px] font-semibold">Purchase details</div>
            <div class="mt-[14px] flex flex-col gap-[14px]">
              <div class="flex flex-col gap-1.5">
                <label class="text-[14px] font-medium">Cost</label>
                <div class="flex items-center gap-2.5">
                  <div
                    class="flex h-10 flex-1 items-center gap-1.5 rounded border border-[#c2c6ca] bg-white px-3"
                  >
                    <span class="font-mono text-muted">$</span>
                    <input
                      v-model="form.cost"
                      type="number"
                      min="0"
                      step="0.01"
                      class="h-auto flex-1 p-0 font-mono text-[15px] focus:outline-none"
                      placeholder="0.00"
                    />
                  </div>
                  <div class="flex overflow-hidden rounded border border-[#c2c6ca] text-[13px]">
                    <button
                      type="button"
                      class="px-[11px] py-2 transition-colors"
                      :class="
                        costMode === 'total'
                          ? 'bg-ink-900 text-white'
                          : 'bg-white text-[#5b6066] hover:bg-[#f5f6f7]'
                      "
                      @click="costMode = 'total'"
                    >
                      $ total
                    </button>
                    <button
                      type="button"
                      class="border-l border-[#c2c6ca] px-[11px] py-2 transition-colors"
                      :class="
                        costMode === 'per_round'
                          ? 'bg-ink-900 text-white'
                          : 'bg-white text-[#5b6066] hover:bg-[#f5f6f7]'
                      "
                      @click="costMode = 'per_round'"
                    >
                      $ / rd
                    </button>
                  </div>
                </div>
                <span v-if="form.cost && form.rounds" class="text-[13px] text-muted">
                  = <span class="font-mono text-[#5b6066]">${{ perRoundCost }}</span> / round
                </span>
              </div>

              <div class="grid grid-cols-2 gap-[14px]">
                <div class="flex flex-col gap-1.5">
                  <div class="flex items-center justify-between">
                    <label class="text-[14px] font-medium">Store / FFL</label>
                    <button
                      type="button"
                      class="inline-flex items-center gap-1 text-[13px] font-semibold text-brass-800 transition-colors hover:text-brass-600"
                      @click="openQuickAdd('store')"
                    >
                      <Plus class="h-3.5 w-3.5" /> Add store
                    </button>
                  </div>
                  <select
                    v-model="form.store_id"
                    class="h-10 w-full rounded border border-[#c2c6ca] bg-white px-3 text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
                  >
                    <option :value="null">— optional —</option>
                    <option v-for="store in stores" :key="store.id" :value="store.id">
                      {{ store.label }}
                    </option>
                  </select>
                </div>
                <div class="flex flex-col gap-1.5">
                  <label class="text-[14px] font-medium">Order # / ref</label>
                  <input
                    v-model="form.order_ref"
                    type="text"
                    class="h-10 w-full rounded border border-[#c2c6ca] bg-white px-3 text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
                    placeholder="optional"
                  />
                </div>
              </div>
            </div>
          </div>
          <FormError v-if="error" :error="error" />
        </div>
        <div class="flex gap-2.5 border-t border-line bg-[#fafbfb] px-[18px] py-[14px]">
          <button class="detail-action detail-action-primary" :disabled="saving">
            {{ saving ? 'Saving…' : 'Save changes' }}
          </button>
          <button type="button" class="detail-action" @click="$emit('close')">Cancel</button>
        </div>
      </form>
    </div>
  </Teleport>
  <ReferenceItemModal
    v-if="quickAddType"
    :type="quickAddType"
    mode="add"
    @close="closeQuickAdd"
    @saved="onQuickAddSaved"
  />
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { Plus, X } from 'lucide-vue-next';
import { useInventoriesStore } from '@/stores/inventories';
import { axiosInstance } from '@/plugins/axios';
import { useQuickAdd } from '@/components/reference/useQuickAdd';
import FormError from '@/components/FormError.vue';
import ReferenceItemModal from '@/components/reference/ReferenceItemModal.vue';

const props = defineProps({ entry: { type: Object, required: true } });
const emit = defineEmits(['close', 'saved']);
const store = useInventoriesStore();
const saving = ref(false);
const error = ref(null);
const stores = ref([]);
const costMode = ref('total');
const { quickAddType, openQuickAdd, closeQuickAdd } = useQuickAdd();
const form = ref({
  inventory_date: props.entry.inventory_date,
  rounds: props.entry.rounds,
  cost: props.entry.cost || null,
  store_id: props.entry.store_id || null,
  order_ref: props.entry.order_ref || null,
});

const perRoundCost = computed(() => {
  if (!form.value.cost || !form.value.rounds) return null;
  const total = costMode.value === 'total' ? form.value.cost : form.value.cost * form.value.rounds;
  return (total / form.value.rounds).toFixed(2);
});

const totalCost = computed(() => {
  if (!form.value.cost) return null;
  if (costMode.value === 'total') return form.value.cost;
  return (form.value.cost * form.value.rounds).toFixed(2);
});

onMounted(async () => {
  const { data } = await axiosInstance.get('/stores');
  stores.value = data.data ?? [];
});

function onQuickAddSaved(item) {
  stores.value.push(item);
  form.value.store_id = item.id;
  closeQuickAdd();
}

async function save() {
  saving.value = true;
  error.value = null;
  try {
    await store.update(props.entry.id, {
      ...form.value,
      cost: props.entry.type === 'BUY' ? totalCost.value : null,
    });
    emit('saved');
  } catch (exception) {
    error.value = exception;
  } finally {
    saving.value = false;
  }
}
</script>
