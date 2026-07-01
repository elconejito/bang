<template>
  <Teleport to="body">
    <div
      class="fixed inset-0 z-50 flex items-start justify-center overflow-auto bg-[rgba(20,22,26,0.46)] px-6 pb-6 pt-14"
      @click.self="$emit('close')"
    >
      <div
        class="w-[484px] max-w-full overflow-hidden rounded border border-[#d6d9dc] bg-white shadow-[0_10px_30px_rgba(20,22,26,0.22),0_2px_8px_rgba(20,22,26,0.12)]"
      >
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
          <!-- Mode toggle -->
          <div class="flex overflow-hidden rounded border border-[#c2c6ca]">
            <button
              class="flex items-center gap-1.5 px-[14px] py-[7px] text-[14px] font-medium transition-colors"
              :class="
                mode === 'purchase'
                  ? 'bg-ink-900 text-white'
                  : 'bg-white text-[#5b6066] hover:bg-[#f5f6f7]'
              "
              @click="mode = 'purchase'"
            >
              <Package class="h-[14px] w-[14px]" />Purchase
            </button>
            <button
              class="border-l border-[#c2c6ca] px-[14px] py-[7px] text-[14px] font-medium transition-colors"
              :class="
                mode === 'adjust'
                  ? 'bg-ink-900 text-white'
                  : 'bg-white text-[#5b6066] hover:bg-[#f5f6f7]'
              "
              @click="mode = 'adjust'"
            >
              Adjustment ±
            </button>
          </div>

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
                class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] font-mono text-[18px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
                placeholder="0"
              />
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="text-[14px] font-medium">Date</label>
              <input
                v-model="form.inventory_date"
                type="date"
                class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[14px] font-mono focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
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
                    class="flex flex-1 items-center gap-1.5 rounded border border-[#c2c6ca] bg-white px-3 py-[9px]"
                  >
                    <span class="font-mono text-muted">$</span>
                    <input
                      v-model="form.cost"
                      type="number"
                      min="0"
                      step="0.01"
                      class="flex-1 font-mono text-[15px] focus:outline-none"
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
                  <label class="text-[14px] font-medium">Store / FFL</label>
                  <select
                    v-model="form.store_id"
                    class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
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
                    class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[15px] placeholder:text-muted focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
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
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { X, Plus, Package, ChevronUp, LoaderCircle } from 'lucide-vue-next';
import { axiosInstance } from '@/plugins/axios';
import FormError from '@/components/FormError.vue';
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
