<template>
  <div class="rounded border border-line bg-surface p-6">
    <div class="flex flex-col gap-[18px]">

      <!-- Nickname / label -->
      <div class="flex flex-col gap-1.5">
        <label class="text-[14px] font-medium">Nickname / label</label>
        <input
          v-model="form.label"
          type="text"
          class="rounded border border-[#c2c6ca] bg-surface px-3 py-[9px] text-[15px] outline-none transition-shadow focus:border-brass focus:ring-[3px] focus:ring-brass-200"
          placeholder="e.g. Nightstand"
        />
      </div>

      <!-- Manufacturer + Model -->
      <div class="grid grid-cols-2 gap-[14px]">
        <div class="flex flex-col gap-1.5">
          <label class="text-[14px] font-medium">Manufacturer</label>
          <input
            v-model="form.manufacturer"
            type="text"
            class="rounded border border-[#c2c6ca] bg-surface px-3 py-[9px] text-[15px] outline-none transition-shadow focus:border-brass focus:ring-[3px] focus:ring-brass-200"
            placeholder="e.g. Glock"
          />
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-[14px] font-medium">Model</label>
          <input
            v-model="form.model"
            type="text"
            class="rounded border border-[#c2c6ca] bg-surface px-3 py-[9px] text-[15px] outline-none transition-shadow focus:border-brass focus:ring-[3px] focus:ring-brass-200"
            placeholder="e.g. 19 Gen5"
          />
        </div>
      </div>

      <!-- Calibers -->
      <div class="flex flex-col gap-1.5">
        <label class="text-[14px] font-medium">
          Calibers <span class="font-normal text-ink-400">· a firearm can accept more than one</span>
        </label>
        <div
          ref="caliberBoxRef"
          class="relative min-h-[42px] rounded border border-[#c2c6ca] bg-surface px-[9px] py-[7px]"
        >
          <div class="flex flex-wrap items-center gap-[7px]">
            <span
              v-for="caliber in selectedCalibers"
              :key="caliber.id"
              class="inline-flex items-center gap-1.5 rounded border border-[#c2c6ca] bg-ink-50 py-[3px] pl-[10px] pr-[6px] text-[13px] text-ink-700"
            >
              {{ caliber.label }}
              <button
                type="button"
                class="flex items-center text-ink-400 hover:text-ink-700"
                @click="removeCAliber(caliber.id)"
              >
                <X class="h-[13px] w-[13px]" />
              </button>
            </span>
            <button
              type="button"
              class="inline-flex items-center gap-[5px] px-[6px] py-[3px] text-[13px] font-semibold text-brass-800 hover:text-brass-700"
              @click.stop="caliberDropdownOpen = !caliberDropdownOpen"
            >
              <Plus class="h-[13px] w-[13px]" />
              Add caliber
            </button>
          </div>

          <!-- Caliber dropdown -->
          <div
            v-if="caliberDropdownOpen && availableCalibers.length"
            class="absolute left-0 top-full z-20 mt-1 max-h-48 w-full overflow-y-auto rounded border border-line bg-surface shadow-lg"
          >
            <div class="py-1">
              <button
                v-for="caliber in availableCalibers"
                :key="caliber.id"
                type="button"
                class="w-full px-3 py-2 text-left text-[14px] text-ink-700 transition-colors hover:bg-ink-50"
                @click="addCAliber(caliber.id); caliberDropdownOpen = false"
              >{{ caliber.label }}</button>
            </div>
          </div>
          <p v-else-if="caliberDropdownOpen" class="absolute left-0 top-full z-20 mt-1 w-full rounded border border-line bg-surface px-3 py-2 text-[14px] text-muted shadow-lg">
            All calibers selected
          </p>
        </div>
      </div>

      <!-- Serial + Storage -->
      <div class="grid grid-cols-2 gap-[14px]">
        <div class="flex flex-col gap-1.5">
          <label class="text-[14px] font-medium">Serial number</label>
          <input
            v-model="form.serial"
            type="text"
            class="rounded border border-[#c2c6ca] bg-surface px-3 py-[9px] font-mono text-[14px] outline-none transition-shadow focus:border-brass focus:ring-[3px] focus:ring-brass-200"
            placeholder="e.g. ABX-1234"
          />
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-[14px] font-medium">Storage location</label>
          <div class="flex items-center gap-2 rounded border border-[#c2c6ca] bg-surface px-3 py-[9px] transition-shadow focus-within:border-brass focus-within:ring-[3px] focus-within:ring-brass-200">
            <MapPin class="h-[15px] w-[15px] shrink-0 text-ink-400" />
            <select
              v-model="form.location_id"
              class="flex-1 appearance-none bg-transparent text-[15px] outline-none"
            >
              <option :value="null">No location</option>
              <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.label }}</option>
            </select>
            <ChevronDown class="h-[15px] w-[15px] shrink-0 text-ink-400 pointer-events-none" />
          </div>
        </div>
      </div>

      <!-- Purchase date + Price -->
      <div class="grid grid-cols-2 gap-[14px]">
        <div class="flex flex-col gap-1.5">
          <label class="text-[14px] font-medium">Purchase date</label>
          <div class="flex items-center gap-2 rounded border border-[#c2c6ca] bg-surface px-3 py-[9px] transition-shadow focus-within:border-brass focus-within:ring-[3px] focus-within:ring-brass-200">
            <input
              v-model="form.purchase_date"
              type="date"
              class="flex-1 bg-transparent font-mono text-[14px] outline-none"
            />
            <CalendarDays class="h-[15px] w-[15px] shrink-0 text-ink-400 pointer-events-none" />
          </div>
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-[14px] font-medium">Price paid</label>
          <div class="flex items-center gap-1.5 rounded border border-[#c2c6ca] bg-surface px-3 py-[9px] transition-shadow focus-within:border-brass focus-within:ring-[3px] focus-within:ring-brass-200">
            <span class="font-mono text-[15px] text-ink-400">$</span>
            <input
              v-model="form.purchase_price"
              type="number"
              min="0"
              step="0.01"
              class="flex-1 bg-transparent font-mono text-[15px] outline-none"
              placeholder="0.00"
            />
          </div>
        </div>
      </div>

      <!-- Purchase store -->
      <div class="flex flex-col gap-1.5">
        <label class="text-[14px] font-medium">
          Purchased from <span class="font-normal text-ink-400">· optional</span>
        </label>
        <div class="flex items-center gap-2 rounded border border-[#c2c6ca] bg-surface px-3 py-[9px] transition-shadow focus-within:border-brass focus-within:ring-[3px] focus-within:ring-brass-200">
          <Store class="h-[15px] w-[15px] shrink-0 text-ink-400" />
          <select
            v-model="form.purchase_store_id"
            class="flex-1 appearance-none bg-transparent text-[15px] outline-none"
          >
            <option :value="null">No store selected</option>
            <option v-for="store in stores" :key="store.id" :value="store.id">{{ store.label }}</option>
          </select>
          <ChevronDown class="h-[15px] w-[15px] shrink-0 text-ink-400 pointer-events-none" />
        </div>
      </div>

      <FormError v-if="error" :error="error" />
    </div>
  </div>

  <!-- Actions -->
  <div class="mt-5 flex items-center gap-2.5">
    <button
      type="button"
      class="inline-flex items-center gap-[7px] rounded border border-[#b08a2e] bg-brass px-5 py-[9px] text-[15px] font-semibold text-ink-900 transition-colors hover:bg-brass-600 disabled:opacity-60"
      :disabled="isSaving"
      @click="submit"
    >
      <LoaderCircle v-if="isSaving" class="h-4 w-4 animate-spin" />
      <Check v-else class="h-4 w-4" />
      {{ isEditing ? 'Save changes' : 'Save firearm' }}
    </button>
    <button
      type="button"
      class="rounded border border-[#c2c6ca] bg-surface px-5 py-[9px] text-[15px] font-semibold text-ink-700 transition-colors hover:bg-ink-50"
      @click="$emit('cancel')"
    >
      Cancel
    </button>
    <span v-if="!isEditing" class="ml-auto inline-flex items-center gap-[7px] text-[13px] text-muted">
      <Info class="h-[15px] w-[15px]" />
      Add photos after saving
    </span>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onBeforeUnmount } from 'vue'
import { CalendarDays, Check, ChevronDown, Info, LoaderCircle, MapPin, Plus, Store, X } from 'lucide-vue-next'
import { useFirearmsStore } from '@/stores/firearms'
import { useCalibersStore } from '@/stores/calibers'
import { useLocationsStore } from '@/stores/locations'
import { useGunStoresStore } from '@/stores/gunStores'
import FormError from '@/components/FormError.vue'

const props = defineProps({
  firearm: { type: Object, default: null },
})

const emit = defineEmits(['complete', 'cancel'])

const firearmsStore = useFirearmsStore()
const calibersStore = useCalibersStore()
const locationsStore = useLocationsStore()
const gunStoresStore = useGunStoresStore()

const allCalibers = ref([])
const locations = ref([])
const stores = ref([])
const isSaving = ref(false)
const error = ref(null)
const caliberDropdownOpen = ref(false)
const caliberBoxRef = ref(null)

const isEditing = computed(() => !!props.firearm?.id)

const form = reactive({
  label:             props.firearm?.label            ?? '',
  manufacturer:      props.firearm?.manufacturer     ?? '',
  model:             props.firearm?.model            ?? '',
  serial:            props.firearm?.serial           ?? '',
  location_id:       props.firearm?.location_id      ?? null,
  purchase_date:     props.firearm?.purchase_date    ? String(props.firearm.purchase_date).slice(0, 10) : '',
  purchase_price:    props.firearm?.purchase_price   ?? '',
  purchase_store_id: props.firearm?.purchase_store_id ?? null,
  calibers:          props.firearm?.calibers?.map(c => c.id) ?? [],
})

const selectedCalibers = computed(() =>
  allCalibers.value.filter(c => form.calibers.includes(c.id))
)

const availableCalibers = computed(() =>
  allCalibers.value.filter(c => !form.calibers.includes(c.id))
)

function addCAliber(id) {
  if (!form.calibers.includes(id)) form.calibers.push(id)
}

function removeCAliber(id) {
  form.calibers = form.calibers.filter(c => c !== id)
}

function closeCaliberDropdown(e) {
  if (caliberBoxRef.value && !caliberBoxRef.value.contains(e.target)) {
    caliberDropdownOpen.value = false
  }
}

async function submit() {
  error.value = null
  isSaving.value = true
  try {
    const payload = { ...form }
    const { data } = isEditing.value
      ? await firearmsStore.update(props.firearm.id, payload)
      : await firearmsStore.create(payload)
    emit('complete', data)
  } catch (err) {
    if (err.response?.data?.errors) err.errorBag = err.response.data.errors
    error.value = err
  } finally {
    isSaving.value = false
  }
}

onMounted(async () => {
  document.addEventListener('click', closeCaliberDropdown)
  const [calRes, locRes, storeRes] = await Promise.all([
    calibersStore.fetchAll(),
    locationsStore.fetchAll(),
    gunStoresStore.fetchAll(),
  ])
  allCalibers.value = calRes.data
  locations.value = locRes.data
  stores.value = storeRes.data
})

onBeforeUnmount(() => {
  document.removeEventListener('click', closeCaliberDropdown)
})
</script>
