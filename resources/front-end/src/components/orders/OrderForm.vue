<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { Check, LoaderCircle, Plus, Search, X } from 'lucide-vue-next';
import FormError from '@/components/FormError.vue';
import OrderAssetCreateDialog from './OrderAssetCreateDialog.vue';
import {
  itemCost,
  itemRounds,
  normalizeOrderItem,
  orderItemLabel,
  orderItemSearchText,
} from '@/composables/useOrderItems';
import { useGunStoresStore } from '@/stores/gunStores';
import { useOrdersStore } from '@/stores/orders';

const props = defineProps({
  initialOrder: Object,
  initialStoreId: Number,
  loading: Boolean,
  error: [Object, String],
  submitLabel: { type: String, default: 'Save order' },
});
const emit = defineEmits(['submit', 'cancel']);
const gunStoresStore = useGunStoresStore();
const ordersStore = useOrdersStore();
const stores = ref([]);
const options = ref([]);
const loadingOptions = ref(true);
const optionsError = ref(null);
const search = ref('');
const dialogType = ref(null);
const dialogIndex = ref(null);
const form = ref({
  store_id: props.initialStoreId ?? '',
  order_date: new Date().toISOString().slice(0, 10),
  order_ref: '',
  items: [normalizeOrderItem({ type: 'ammunition' })],
});
function applyOrder(order) {
  if (!order) return;
  form.value = {
    store_id: order.store_id ?? order.store?.id ?? '',
    order_date: order.order_date?.slice(0, 10) ?? '',
    order_ref: order.order_ref ?? '',
    items: order.items?.length
      ? order.items.map(normalizeOrderItem)
      : [normalizeOrderItem({ type: 'ammunition' })],
  };
}
watch(() => props.initialOrder, applyOrder, { immediate: true });
const totalRounds = computed(() => itemRounds(form.value.items));
const totalCost = computed(() => itemCost(form.value.items));
function filteredOptions(item) {
  const q = search.value.trim().toLowerCase();
  return options.value.filter(
    (o) =>
      o.type === item.type &&
      (!q ||
        orderItemSearchText(o).includes(q) ||
        form.value.items.some(
          (other) =>
            other.type === o.type &&
            Number(other.type === 'ammunition' ? other.ammunition_id : other.asset_id) ===
              Number(o.id)
        ))
  );
}
const canSubmit = computed(() =>
  Boolean(
    form.value.store_id &&
    form.value.order_date &&
    form.value.items.length &&
    form.value.items.every((i) =>
      i.type === 'ammunition'
        ? i.ammunition_id &&
          Number.isInteger(Number(i.rounds)) &&
          Number(i.rounds) > 0 &&
          i.cost !== '' &&
          Number(i.cost) >= 0
        : i.asset_id && i.cost !== '' && Number(i.cost) >= 0
    ) &&
    new Set(
      form.value.items.map(
        (i) => `${i.type}:${i.type === 'ammunition' ? i.ammunition_id : i.asset_id}`
      )
    ).size === form.value.items.length
  )
);
function money(value) {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(
    Number(value) || 0
  );
}
function typeLabel(type) {
  return type === 'ammunition'
    ? 'Ammunition'
    : type.replace('-', ' ').replace(/\b\w/g, (c) => c.toUpperCase());
}
function addItem(type = 'ammunition') {
  form.value.items.push(normalizeOrderItem({ type }));
}
function removeItem(index) {
  form.value.items.splice(index, 1);
}
function chooseOption(item, event) {
  const id = event.target.value;
  if (item.type === 'ammunition') item.ammunition_id = id;
  else {
    if (Number(item.asset_id) !== Number(id)) item.id = undefined;
    item.asset_id = id;
  }
}
function optionFor(item) {
  const id = item.type === 'ammunition' ? item.ammunition_id : item.asset_id;
  return options.value.find((o) => o.type === item.type && Number(o.id) === Number(id));
}
function submit() {
  if (!canSubmit.value || props.loading) return;
  emit('submit', {
    store_id: Number(form.value.store_id),
    order_date: form.value.order_date,
    order_ref: form.value.order_ref.trim() || null,
    items: form.value.items.map((i) => ({
      ...(i.id ? { id: i.id } : {}),
      type: i.type,
      ...(i.type === 'ammunition'
        ? { ammunition_id: Number(i.ammunition_id), rounds: Number(i.rounds) }
        : { asset_id: Number(i.asset_id) }),
      cost: Number(i.cost),
    })),
  });
}
function assetCreated(asset, type) {
  options.value.push({
    type,
    id: asset.id,
    label: [asset.manufacturer, asset.label || asset.model || asset.model_name, asset.model_number]
      .filter(Boolean)
      .join(' · '),
    manufacturer: asset.manufacturer,
    secondary_label: [asset.serial || asset.serial_number, asset.id_marking]
      .filter(Boolean)
      .join(' · '),
    asset,
  });
  const item =
    form.value.items[dialogIndex.value ?? form.value.items.length - 1] ??
    normalizeOrderItem({ type });
  if (!form.value.items.includes(item)) form.value.items.push(item);
  item.type = type;
  item.asset_id = asset.id;
  item.id = undefined;
  dialogType.value = null;
  dialogIndex.value = null;
}
function createNew(index) {
  dialogIndex.value = index;
  dialogType.value = form.value.items[index].type;
}
onMounted(async () => {
  try {
    const [storesResponse, optionsResponse] = await Promise.all([
      gunStoresStore.fetchAll(),
      ordersStore.fetchItemOptions(props.initialOrder?.id),
    ]);
    stores.value = storesResponse.data ?? storesResponse;
    options.value = optionsResponse.data ?? optionsResponse;
  } catch (error) {
    optionsError.value = error;
  } finally {
    loadingOptions.value = false;
  }
});
</script>
<template>
  <LoadingState v-if="loadingOptions" message="Loading order options…" />
  <div v-else-if="optionsError" class="rounded border border-[#e4b9ae] bg-[#fbf2ef] p-5">
    <FormError :error="optionsError" />
  </div>
  <form v-else class="space-y-[18px]" @submit.prevent="submit">
    <div class="rounded border border-line bg-white p-5">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <label class="sm:col-span-2 text-[14px] font-medium"
          >Store<select
            v-model="form.store_id"
            required
            class="mt-1 h-10 w-full rounded border border-[#c2c6ca] bg-white px-3"
          >
            <option value="">Select store</option>
            <option v-for="store in stores" :key="store.id" :value="store.id">
              {{ store.label }}
            </option>
          </select></label
        ><label class="text-[14px] font-medium"
          >Order date<input
            v-model="form.order_date"
            type="date"
            required
            class="mt-1 h-10 w-full rounded border border-[#c2c6ca] px-3 font-mono" /></label
        ><label class="text-[14px] font-medium"
          >Order / reference number<input
            v-model="form.order_ref"
            class="mt-1 h-10 w-full rounded border border-[#c2c6ca] px-3 font-mono"
        /></label>
      </div>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div class="font-mono text-[11px] tracking-[0.1em] text-muted">ORDER ITEMS</div>
      <div class="relative">
        <Search
          class="pointer-events-none absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-muted"
        /><input
          v-model="search"
          aria-label="Search order items"
          placeholder="Search caliber, manufacturer, label…"
          class="h-9 rounded border border-[#c2c6ca] pl-8 pr-3 text-[13px]"
        />
      </div>
    </div>
    <div class="space-y-[14px]">
      <div
        v-for="(item, index) in form.items"
        :key="item._key"
        class="overflow-hidden rounded border border-line bg-white p-4"
      >
        <div class="mb-3 flex items-center gap-3">
          <span class="font-display text-[16px] font-semibold">Item {{ index + 1 }}</span
          ><span class="text-[12px] text-muted">{{ typeLabel(item.type) }}</span
          ><button
            v-if="form.items.length > 1"
            type="button"
            class="ml-auto text-muted hover:text-red-600"
            @click="removeItem(index)"
          >
            <X class="h-4 w-4" />
          </button>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <label class="text-[14px] font-medium"
            >Category<select
              v-model="item.type"
              class="mt-1 h-10 w-full rounded border border-[#c2c6ca] bg-white px-3"
              @change="
                item.ammunition_id = '';
                item.asset_id = '';
                item.id = undefined;
              "
            >
              <option value="ammunition">Ammunition</option>
              <option
                v-for="type in [
                  'firearm',
                  'suppressor',
                  'optic',
                  'light',
                  'misc-accessory',
                  'mount',
                  'magazine',
                ]"
                :key="type"
                :value="type"
              >
                {{ typeLabel(type) }}
              </option>
            </select></label
          >
          <label class="text-[14px] font-medium"
            >Item<select
              :value="item.type === 'ammunition' ? item.ammunition_id : item.asset_id"
              required
              class="mt-1 h-10 w-full rounded border border-[#c2c6ca] bg-white px-3"
              @change="chooseOption(item, $event)"
            >
              <option value="">Select item</option>
              <option
                v-for="option in filteredOptions(item)"
                :key="option.type + ':' + option.id"
                :value="option.id"
                :disabled="
                  form.items.some(
                    (other) =>
                      other !== item &&
                      other.type === option.type &&
                      Number(other.type === 'ammunition' ? other.ammunition_id : other.asset_id) ===
                        Number(option.id)
                  )
                "
              >
                {{ [option.label, option.secondary_label].filter(Boolean).join(' · ') }}
              </option>
            </select></label
          ><button
            v-if="item.type !== 'ammunition'"
            type="button"
            class="self-end rounded border border-[#c2c6ca] px-3 py-2 text-[13px] text-brass-800"
            @click="createNew(index)"
          >
            Create new</button
          ><label v-if="item.type === 'ammunition'" class="text-[14px] font-medium"
            >Rounds<input
              v-model="item.rounds"
              type="number"
              min="1"
              required
              class="mt-1 h-10 w-full rounded border border-[#c2c6ca] px-3 font-mono" /></label
          ><label class="text-[14px] font-medium"
            >Line cost<input
              v-model="item.cost"
              type="number"
              min="0"
              step="0.01"
              required
              class="mt-1 h-10 w-full rounded border border-[#c2c6ca] px-3 font-mono"
          /></label>
        </div>
        <div v-if="optionFor(item)" class="mt-2 text-[12px] text-muted">
          {{ orderItemLabel(optionFor(item)) }}
        </div>
      </div>
    </div>
    <div class="flex flex-wrap gap-2">
      <button
        type="button"
        class="inline-flex items-center gap-2 rounded border border-dashed border-[#c2c6ca] px-4 py-3 text-[14px] font-semibold text-brass-800"
        @click="addItem()"
      >
        <Plus class="h-4 w-4" />Add ammunition</button
      ><button
        type="button"
        class="rounded border border-dashed border-[#c2c6ca] px-4 py-3 text-[14px] text-brass-800"
        @click="addItem()"
      >
        + Add item
      </button>
    </div>
    <FormError v-if="error" :error="error" />
    <div
      class="sticky bottom-0 z-10 flex flex-wrap items-center gap-5 rounded border border-line bg-white px-[18px] py-[14px]"
    >
      <div>
        <div class="font-mono text-[18px]">{{ totalRounds.toLocaleString() }}</div>
        <div class="font-mono text-[9px] text-muted">ROUNDS</div>
      </div>
      <div>
        <div class="font-mono text-[18px]">{{ money(totalCost) }}</div>
        <div class="font-mono text-[9px] text-muted">ORDER TOTAL</div>
      </div>
      <div class="ml-auto flex gap-2">
        <button type="button" class="rounded border px-4 py-2" @click="emit('cancel')">
          Cancel</button
        ><button
          type="submit"
          :disabled="loading || !canSubmit"
          class="inline-flex items-center gap-2 rounded border border-[#b08a2e] bg-brass px-5 py-2 font-semibold disabled:opacity-50"
        >
          <LoaderCircle v-if="loading" class="h-4 w-4 animate-spin" /><Check
            v-else
            class="h-4 w-4"
          />{{ submitLabel }}
        </button>
      </div>
    </div>
  </form>
  <OrderAssetCreateDialog
    v-if="dialogType"
    :type="dialogType"
    @complete="assetCreated($event, dialogType)"
    @cancel="dialogType = null"
  />
</template>
