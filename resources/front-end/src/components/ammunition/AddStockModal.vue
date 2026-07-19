<template>
  <Teleport to="body">
    <div class="modal-scrim z-50 px-4 pb-6 pt-8 sm:px-6 sm:pt-14" @click.self="$emit('close')">
      <div class="modal-shell w-[484px] max-w-full">
        <!-- Header -->
        <div
          class="flex items-start justify-between gap-3 border-b border-[#eef0f1] px-[18px] py-4"
        >
          <div>
            <div class="font-display text-[19px] font-semibold leading-[1.1]">Add stock</div>
            <div class="mt-0.5 text-[13px] text-muted">
              {{ ammo.label }} · {{ ammo.caliber?.label }}
            </div>
          </div>
          <button class="shrink-0 p-0.5 text-muted hover:text-ink-900" @click="$emit('close')">
            <X class="h-[18px] w-[18px]" />
          </button>
        </div>

        <div class="flex flex-col gap-4 p-[18px]">
          <!-- Stock entry type -->
          <fieldset>
            <legend class="mb-2 text-[14px] font-medium text-ink-700">Entry type</legend>
            <div class="grid gap-2 sm:grid-cols-2" role="radiogroup" aria-label="Stock entry type">
              <button
                type="button"
                role="radio"
                :aria-checked="mode === 'purchase'"
                class="flex items-start gap-3 rounded border p-3 text-left transition-colors focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
                :class="
                  mode === 'purchase'
                    ? 'border-brass-600 bg-[#fbf8ef] shadow-[inset_0_0_0_1px_#b08a2e]'
                    : 'border-[#c2c6ca] bg-white hover:border-[#9ca1a6] hover:bg-[#fafbfb]'
                "
                @click="mode = 'purchase'"
              >
                <span
                  class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full"
                  :class="mode === 'purchase' ? 'bg-brass text-ink-900' : 'bg-[#eef0f1] text-muted'"
                >
                  <Package class="h-4 w-4" />
                </span>
                <span>
                  <span class="block text-[14px] font-semibold text-ink-900">Purchase</span>
                  <span class="mt-0.5 block text-[12px] leading-4 text-muted"
                    >Add rounds and optionally track cost and store.</span
                  >
                </span>
              </button>
              <button
                type="button"
                role="radio"
                :aria-checked="mode === 'adjust'"
                class="flex items-start gap-3 rounded border p-3 text-left transition-colors focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
                :class="
                  mode === 'adjust'
                    ? 'border-brass-600 bg-[#fbf8ef] shadow-[inset_0_0_0_1px_#b08a2e]'
                    : 'border-[#c2c6ca] bg-white hover:border-[#9ca1a6] hover:bg-[#fafbfb]'
                "
                @click="mode = 'adjust'"
              >
                <span
                  class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full"
                  :class="mode === 'adjust' ? 'bg-brass text-ink-900' : 'bg-[#eef0f1] text-muted'"
                >
                  <ListRestart class="h-4 w-4" />
                </span>
                <span>
                  <span class="block text-[14px] font-semibold text-ink-900"
                    >Inventory adjustment</span
                  >
                  <span class="mt-0.5 block text-[12px] leading-4 text-muted"
                    >Correct the count by adding or removing rounds.</span
                  >
                </span>
              </button>
            </div>
          </fieldset>

          <!-- Rounds + Date -->
          <div class="grid grid-cols-2 gap-[14px]">
            <div class="flex flex-col gap-1.5">
              <label class="text-[14px] font-medium"
                >Rounds {{ mode === 'adjust' ? '(±)' : 'added' }}</label
              >
              <input
                v-model.number="form.rounds"
                type="number"
                :min="mode === 'purchase' ? 1 : undefined"
                class="h-10 w-full rounded border border-[#c2c6ca] bg-white px-3 font-mono text-[18px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
                placeholder="0"
              />
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="text-[14px] font-medium">Date</label>
              <input
                v-model="form.inventory_date"
                type="date"
                class="h-10 w-full rounded border border-[#c2c6ca] bg-white px-3 text-[14px] font-mono focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
              />
            </div>
          </div>

          <!-- Purchase details (only in purchase mode) -->
          <div v-if="mode === 'purchase'" class="border-t border-[#eef0f1] pt-[15px]">
            <button
              class="flex w-full items-center justify-between"
              @click="purchaseExpanded = !purchaseExpanded"
            >
              <span class="text-[15px] font-semibold">Purchase details</span>
              <span class="flex items-center gap-1.5 text-[13px] text-muted">
                optional
                <ChevronUp
                  class="h-4 w-4 text-[#7d6320] transition-transform"
                  :class="{ 'rotate-180': !purchaseExpanded }"
                />
              </span>
            </button>

            <div v-if="purchaseExpanded" class="mt-[14px] flex flex-col gap-[14px]">
              <!-- Cost -->
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

              <!-- Store / Order ref -->
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
        </div>

        <!-- Footer -->
        <div
          class="flex items-center gap-2.5 border-t border-[#eef0f1] bg-[#fafbfb] px-[18px] py-[14px]"
        >
          <button
            class="inline-flex items-center gap-[7px] rounded border border-[#b08a2e] bg-brass px-[18px] py-[9px] text-[15px] font-semibold text-ink-900 transition-colors hover:bg-brass-600 disabled:opacity-50"
            :disabled="!form.rounds || saving"
            @click="handleSave"
          >
            <LoaderCircle v-if="saving" class="h-4 w-4 animate-spin" />
            <Plus v-else class="h-4 w-4" />
            {{
              saving
                ? 'Saving…'
                : form.rounds
                  ? `Add ${form.rounds.toLocaleString()} rounds`
                  : 'Add rounds'
            }}
          </button>
          <button
            class="rounded border border-[#c2c6ca] bg-white px-[18px] py-[9px] text-[15px] font-semibold text-ink-700 transition-colors hover:bg-[#f5f6f7]"
            @click="$emit('close')"
          >
            Cancel
          </button>
          <span v-if="form.rounds" class="ml-auto font-mono text-[12px] text-muted">
            {{ ammo.on_hand.toLocaleString() }} →
            {{ (ammo.on_hand + (form.rounds || 0)).toLocaleString() }}
          </span>
        </div>

        <FormError v-if="saveError" :error="saveError" class="m-[18px] mt-0" />
      </div>
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
import { ref, computed, onMounted } from 'vue';
import { X, Plus, Package, ListRestart, ChevronUp, LoaderCircle } from 'lucide-vue-next';
import { axiosInstance } from '@/plugins/axios';
import { useQuickAdd } from '@/components/reference/useQuickAdd';
import FormError from '@/components/FormError.vue';
import ReferenceItemModal from '@/components/reference/ReferenceItemModal.vue';
import dayjs from 'dayjs';

const props = defineProps({
  ammo: { type: Object, required: true },
});

const emit = defineEmits(['close', 'stocked']);

const mode = ref('purchase');
const costMode = ref('total');
const purchaseExpanded = ref(true);
const saving = ref(false);
const saveError = ref(null);
const stores = ref([]);
const { quickAddType, openQuickAdd, closeQuickAdd } = useQuickAdd();

const form = ref({
  rounds: null,
  inventory_date: dayjs().format('YYYY-MM-DD'),
  cost: null,
  store_id: null,
  order_ref: null,
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

async function handleSave() {
  saving.value = true;
  saveError.value = null;
  try {
    const isPurchase = mode.value === 'purchase';
    await axiosInstance.post('/inventories', {
      ammunition_id: props.ammo.id,
      inventory_date: form.value.inventory_date,
      rounds: form.value.rounds,
      is_purchase: isPurchase,
      cost: isPurchase ? totalCost.value : null,
      store_id: isPurchase ? form.value.store_id : null,
      order_ref: isPurchase ? form.value.order_ref : null,
    });
    emit('stocked', { rounds: form.value.rounds });
  } catch (e) {
    saveError.value = e;
  } finally {
    saving.value = false;
  }
}
</script>
