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
          v-model="firearm.manufacturer"
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
          v-model="firearm.model"
        />
        <p class="mt-1 text-xs text-gray-500">The Model or Version of the firearm</p>
      </div>
    </div>

    <div class="mb-4">
      <label for="label" class="block text-sm font-medium text-gray-700 mb-1">
        Label <span class="text-red-500">*</span>
      </label>
      <input
        type="text"
        class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
        id="label"
        name="label"
        placeholder="Label of Firearm"
        required
        v-model="firearm.label"
      />
      <p class="mt-1 text-xs text-gray-500">The label that will show throughout the app</p>
    </div>

    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700 mb-1">Calibers</label>
      <p class="mb-2 text-xs text-gray-500">Check all the calibers this firearm supports</p>
      <div class="grid grid-cols-2 gap-1">
        <label
          v-for="(caliber, i) in calibers"
          :key="i"
          :for="`check-${i}`"
          class="flex items-center gap-2 text-sm"
        >
          <input
            class="h-4 w-4 rounded border-gray-300 text-blue-600"
            type="checkbox"
            :value="caliber.id"
            :id="`check-${i}`"
            v-model="firearm.calibers"
          />
          {{ caliber.label }}
        </label>
      </div>
    </div>

    <FormError v-if="error" :error="error" />

    <div class="mt-6">
      <ActionButton text="Save Changes" :is-loading="loading" variant="primary" @click="submit" />
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted, toRef } from 'vue'
import { useFirearmsStore } from '@/stores/firearms'
import { useCalibersStore } from '@/stores/calibers'
import { useForm } from '@/composables/useForm'
import ActionButton from '@/components/ActionButton.vue'
import FormError from '@/components/FormError.vue'

const props = defineProps({
  original: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['complete'])

const firearmsStore = useFirearmsStore()
const calibersStore = useCalibersStore()
const { initData } = useForm()

const loading = ref(false)
const error = ref(null)
const calibers = ref([])
const firearm = ref({
  id: '',
  label: '',
  manufacturer: '',
  model: '',
  calibers: [],
})

onMounted(async () => {
  initData(firearm, toRef(props, 'original'))
  // calibers relationship comes back as objects; flatten to IDs for checkboxes
  if (firearm.value.calibers) {
    firearm.value.calibers = firearm.value.calibers.map((c) => c.id ?? c)
  }
  const { data } = await calibersStore.fetchAll()
  calibers.value = data
})

async function submit() {
  error.value = null
  loading.value = true
  try {
    await firearmsStore.update(firearm.value.id, firearm.value)
    emit('complete')
  } catch (err) {
    if (err.response?.data?.errors) err.errorBag = err.response.data.errors
    error.value = err
  } finally {
    loading.value = false
  }
}
</script>
