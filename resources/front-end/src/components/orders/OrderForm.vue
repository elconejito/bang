<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { Check, ChevronDown, LoaderCircle, Package, Plus, Store, X } from 'lucide-vue-next';
import FormError from '@/components/FormError.vue';
import { useAmmunitionStore } from '@/stores/ammunition';
import { useGunStoresStore } from '@/stores/gunStores';

const props = defineProps({
  initialOrder: { type: Object, default: null },
  initialStoreId: { type: Number, default: null },
  loading: { type: Boolean, default: false },
  error: { type: [Object, String], default: null },
  submitLabel: { type: String, default: 'Save order' },
});

const emit = defineEmits(['submit', 'cancel']);
const ammunitionStore = useAmmunitionStore();
const gunStoresStore = useGunStoresStore();
const loadingOptions = ref(true);
const optionsError = ref(null);
const stores = ref([]);
const ammunition = ref([]);

function newItem(item = {}) {
  return {
    id: item.id,
    ammunition_id: item.ammunition_id ?? item.ammunition?.id ?? '',
    rounds: item.rounds ?? '',
    cost: item.cost ?? '',
  };
}

const form = ref({
  store_id: props.initialStoreId ?? '',
  order_date: new Date().toISOString().substring(0, 10),
  order_ref: '',
  items: [newItem()],
});

function applyOrder(order) {
  if (!order) return;
  form.value = {
    store_id: order.store_id ?? order.store?.id ?? '',
    order_date: order.order_date?.substring(0, 10) ?? '',
    order_ref: order.order_ref ?? '',
    items: order.items?.length ? order.items.map(newItem) : [newItem()],
  };
}

watch(() => props.initialOrder, applyOrder, { immediate: true });

const totalRounds = computed(() =>
  form.value.items.reduce((sum, item) => sum + (Number(item.rounds) || 0), 0)
);
const totalCost = computed(() =>
  form.value.items.reduce((sum, item) => sum + (Number(item.cost) || 0), 0)
);
const canSubmit = computed(
  () =>
    form.value.store_id &&
    form.value.order_date &&
    form.value.items.length > 0 &&
    new Set(form.value.items.map((item) => Number(item.ammunition_id))).size ===
      form.value.items.length &&
    form.value.items.every(
      (item) => item.ammunition_id && Number(item.rounds) > 0 && Number(item.cost) >= 0
    )
);

function money(value) {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value || 0);
}

function addItem() {
  form.value.items.push(newItem());
}

function removeItem(index) {
  form.value.items.splice(index, 1);
}

function isAmmoUsed(ammunitionId, currentItem) {
  return form.value.items.some(
    (item) => item !== currentItem && Number(item.ammunition_id) === Number(ammunitionId)
  );
}

function submit() {
  emit('submit', {
    store_id: Number(form.value.store_id),
    order_date: form.value.order_date,
    order_ref: form.value.order_ref.trim() || null,
    items: form.value.items.map((item) => ({
      ...(item.id ? { id: item.id } : {}),
      ammunition_id: Number(item.ammunition_id),
      rounds: Number(item.rounds),
      cost: Number(item.cost),
    })),
  });
}

onMounted(async () => {
  try {
    const [storeResponse, ammunitionResponse] = await Promise.all([
      gunStoresStore.fetchAll(),
      ammunitionStore.fetchAll(),
    ]);
    stores.value = storeResponse.data ?? storeResponse;
    ammunition.value = ammunitionResponse.data ?? ammunitionResponse;
  } catch (err) {
    optionsError.value = err;
  } finally {
    loadingOptions.value = false;
  }
});
</script>

<template>
  <div v-if="loadingOptions" class="py-12 text-center text-sm text-muted">Loading…</div>
  <div v-else-if="optionsError" class="rounded border border-[#e4b9ae] bg-[#fbf2ef] p-5">
    <FormError :error="optionsError" />
    <p class="mt-2 text-[13px] text-[#6b7077]">
      Could not load stores or ammunition. Refresh the page to try again.
    </p>
  </div>
  <form v-else class="space-y-[18px]" @submit.prevent="submit">
    <div class="rounded border border-line bg-white p-5">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-[14px] font-medium text-[#3a3e44]"
            >Store <span class="text-red-500">*</span></label
          >
          <div class="relative">
            <Store
              class="pointer-events-none absolute left-3 top-1/2 h-[15px] w-[15px] -translate-y-1/2 text-muted"
            />
            <select
              v-model="form.store_id"
              required
              class="h-10 w-full appearance-none rounded border border-[#c2c6ca] bg-white pl-9 pr-9 text-[15px] outline-none focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
            >
              <option value="">Select store</option>
              <option v-for="store in stores" :key="store.id" :value="store.id">
                {{ store.label }}
              </option>
            </select>
            <ChevronDown
              class="pointer-events-none absolute right-3 top-1/2 h-[15px] w-[15px] -translate-y-1/2 text-muted"
            />
          </div>
        </div>
        <div>
          <label class="mb-1.5 block text-[14px] font-medium text-[#3a3e44]"
            >Order date <span class="text-red-500">*</span></label
          >
          <input
            v-model="form.order_date"
            type="date"
            required
            class="h-10 w-full rounded border border-[#c2c6ca] bg-white px-3 font-mono text-[14px] outline-none focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
          />
        </div>
        <div>
          <label class="mb-1.5 block text-[14px] font-medium text-[#3a3e44]"
            >Order / reference number <span class="font-normal text-muted">· optional</span></label
          >
          <input
            v-model="form.order_ref"
            type="text"
            placeholder="e.g. A-10482"
            class="h-10 w-full rounded border border-[#c2c6ca] bg-white px-3 font-mono text-[14px] outline-none focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
          />
        </div>
      </div>
    </div>

    <div class="font-mono text-[11px] tracking-[0.1em] text-muted">ORDER ITEMS</div>
    <div class="space-y-[14px]">
      <div
        v-for="(item, index) in form.items"
        :key="item.id ?? index"
        class="overflow-hidden rounded border border-line bg-white"
      >
        <div class="flex items-center gap-3 border-b border-[#eef0f1] bg-[#fafbfb] px-4 py-3">
          <Package class="h-[17px] w-[17px] text-[#7d6320]" />
          <span class="font-display text-[16px] font-semibold">Ammo item {{ index + 1 }}</span>
          <button
            v-if="form.items.length > 1"
            type="button"
            title="Remove item"
            class="ml-auto flex h-7 w-7 items-center justify-center rounded text-muted hover:bg-[#f7e9e4] hover:text-[#b4452f]"
            @click="removeItem(index)"
          >
            <X class="h-[17px] w-[17px]" />
          </button>
        </div>
        <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-[1fr_130px_150px]">
          <div>
            <label class="mb-1.5 block text-[14px] font-medium text-[#3a3e44]"
              >Ammunition <span class="text-red-500">*</span></label
            >
            <div class="relative">
              <select
                v-model="item.ammunition_id"
                required
                class="h-10 w-full appearance-none rounded border border-[#c2c6ca] bg-white px-3 pr-9 text-[15px] outline-none focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
              >
                <option value="">Select ammunition</option>
                <option
                  v-for="ammo in ammunition"
                  :key="ammo.id"
                  :value="ammo.id"
                  :disabled="isAmmoUsed(ammo.id, item)"
                >
                  {{ [ammo.manufacturer, ammo.label].filter(Boolean).join(' · ') }}
                </option>
              </select>
              <ChevronDown
                class="pointer-events-none absolute right-3 top-1/2 h-[15px] w-[15px] -translate-y-1/2 text-muted"
              />
            </div>
          </div>
          <div>
            <label class="mb-1.5 block text-[14px] font-medium text-[#3a3e44]"
              >Rounds <span class="text-red-500">*</span></label
            >
            <input
              v-model="item.rounds"
              type="number"
              min="1"
              required
              placeholder="100"
              class="h-10 w-full rounded border border-[#c2c6ca] bg-white px-3 font-mono text-[15px] outline-none focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
            />
          </div>
          <div>
            <label class="mb-1.5 block text-[14px] font-medium text-[#3a3e44]"
              >Line cost <span class="text-red-500">*</span></label
            >
            <div class="relative">
              <span
                class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 font-mono text-muted"
                >$</span
              ><input
                v-model="item.cost"
                type="number"
                min="0"
                step="0.01"
                required
                placeholder="0.00"
                class="h-10 w-full rounded border border-[#c2c6ca] bg-white pl-7 pr-3 font-mono text-[15px] outline-none focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <button
      type="button"
      class="flex w-full items-center justify-center gap-2 rounded border border-dashed border-[#c2c6ca] bg-[#fafbfb] py-[14px] text-[15px] font-semibold text-[#7d6320] hover:bg-[#f3f4f5]"
      @click="addItem"
    >
      <Plus class="h-[17px] w-[17px]" />Add another item
    </button>
    <FormError v-if="error" :error="error" />
    <div
      class="sticky bottom-0 z-10 flex flex-wrap items-center gap-5 rounded border border-line bg-white px-[18px] py-[14px] shadow-[0_-2px_8px_rgba(20,22,26,0.06)]"
    >
      <div>
        <div class="font-mono text-[18px] font-medium leading-none">
          {{ totalRounds.toLocaleString() }}
        </div>
        <div class="font-mono text-[9px] tracking-[0.05em] text-muted">ROUNDS</div>
      </div>
      <div>
        <div class="font-mono text-[18px] font-medium leading-none">{{ money(totalCost) }}</div>
        <div class="font-mono text-[9px] tracking-[0.05em] text-muted">ORDER TOTAL</div>
      </div>
      <div class="ml-auto flex items-center gap-2.5">
        <button
          type="button"
          class="rounded border border-[#c2c6ca] bg-white px-[18px] py-[9px] text-[15px] font-semibold text-[#3a3e44] hover:bg-[#f5f6f7]"
          @click="emit('cancel')"
        >
          Cancel
        </button>
        <button
          type="submit"
          :disabled="loading || !canSubmit"
          class="inline-flex items-center gap-2 rounded border border-[#b08a2e] bg-brass px-5 py-[9px] text-[15px] font-semibold text-ink-900 hover:bg-brass-600 disabled:cursor-not-allowed disabled:opacity-50"
        >
          <LoaderCircle v-if="loading" class="h-4 w-4 animate-spin" /><Check
            v-else
            class="h-4 w-4"
          />{{ submitLabel }}
        </button>
      </div>
    </div>
  </form>
</template>
