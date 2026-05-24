<template>
  <form>
    <div class="mb-4 grid grid-cols-3 gap-4">
      <div>
        <label for="manufacturer" class="block text-sm font-medium text-gray-700 mb-1">
          Manufacturer <span class="text-red-500">*</span>
        </label>
        <input
          type="text"
          class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
          id="manufacturer"
          name="manufacturer"
          placeholder="Manufacturer"
          required
          v-model="magazine.manufacturer"
        />
        <p class="mt-1 text-xs text-gray-500">The name of the manufacturer</p>
      </div>
      <div class="col-span-2">
        <label for="model" class="block text-sm font-medium text-gray-700 mb-1">
          Model <span class="text-red-500">*</span>
        </label>
        <input
          type="text"
          class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
          id="model"
          name="model"
          placeholder="Model"
          required
          v-model="magazine.model_name"
        />
        <p class="mt-1 text-xs text-gray-500">The Model or Version of the magazine</p>
      </div>
    </div>

    <div class="mb-4 grid grid-cols-2 gap-4">
      <div>
        <label for="label" class="block text-sm font-medium text-gray-700 mb-1">
          Label <span class="text-red-500">*</span>
        </label>
        <input
          type="text"
          class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
          id="label"
          name="label"
          placeholder="Label of magazine"
          required
          v-model="magazine.label"
        />
        <p class="mt-1 text-xs text-gray-500">The label that will show throughout the app</p>
      </div>
      <div>
        <label for="serial_number" class="block text-sm font-medium text-gray-700 mb-1">Serial Number</label>
        <input
          type="text"
          class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
          id="serial_number"
          name="serial_number"
          placeholder="Serial of magazine"
          v-model="magazine.serial_number"
        />
        <p class="mt-1 text-xs text-gray-500">Any serial numbers that identify the magazine</p>
      </div>
    </div>

    <div class="mb-4">
      <label for="capacity" class="block text-sm font-medium text-gray-700 mb-1">
        Capacity <span class="text-red-500">*</span>
      </label>
      <input
        type="number"
        class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
        id="capacity"
        name="capacity"
        placeholder="Magazine Capacity"
        required
        v-model="magazine.capacity"
      />
      <p class="mt-1 text-xs text-gray-500">The max number of rounds this magazine will hold</p>
    </div>

    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700 mb-1">Calibers</label>
      <p class="mb-2 text-xs text-gray-500">Check all the calibers this magazine supports</p>
      <div class="grid grid-cols-2 gap-1">
        <label
          v-for="(caliber, i) in calibers"
          :key="i"
          :for="`check-calibers-${i}`"
          class="flex items-center gap-2 text-sm"
        >
          <input
            class="h-4 w-4 rounded border-gray-300 text-blue-600"
            type="checkbox"
            :value="caliber.id"
            :id="`check-calibers-${i}`"
            v-model="magazine.calibers"
          />
          {{ caliber.label }}
        </label>
      </div>
    </div>

    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700 mb-1">Used By</label>
      <p class="mb-2 text-xs text-gray-500">Check all the firearms that can use this magazine</p>
      <div class="grid grid-cols-2 gap-1">
        <label
          v-for="(firearm, i) in firearms"
          :key="i"
          :for="`check-firearms-${i}`"
          class="flex items-center gap-2 text-sm"
        >
          <input
            class="h-4 w-4 rounded border-gray-300 text-blue-600"
            type="checkbox"
            :value="firearm.id"
            :id="`check-firearms-${i}`"
            v-model="magazine.firearms"
          />
          {{ firearm.label }}
        </label>
      </div>
    </div>

    <FormError v-if="error" :error="error" />

    <div class="mt-6">
      <ActionButton text="Add New" :is-loading="loading" variant="primary" @click="submit" />
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useMagazinesStore } from '@/stores/magazines'
import { useCalibersStore } from '@/stores/calibers'
import { useFirearmsStore } from '@/stores/firearms'
import ActionButton from '@/components/ActionButton.vue'
import FormError from '@/components/FormError.vue'

const emit = defineEmits(['complete'])

const magazinesStore = useMagazinesStore()
const calibersStore = useCalibersStore()
const firearmsStore = useFirearmsStore()

const loading = ref(false)
const error = ref(null)
const calibers = ref([])
const firearms = ref([])
const magazine = ref({
  label: '',
  manufacturer: '',
  model_name: '',
  serial_number: '',
  capacity: 0,
  calibers: [],
  firearms: [],
})

onMounted(async () => {
  const [calibersRes, firearmsRes] = await Promise.all([
    calibersStore.fetchAll(),
    firearmsStore.fetchAll(),
  ])
  calibers.value = calibersRes.data
  firearms.value = firearmsRes.data
})

async function submit() {
  error.value = null
  loading.value = true
  try {
    const { data } = await magazinesStore.create(magazine.value)
    emit('complete', data)
  } catch (err) {
    if (err.response?.data?.errors) err.errorBag = err.response.data.errors
    error.value = err
  } finally {
    loading.value = false
  }
}
</script>
