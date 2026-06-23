<template>
  <form @submit.prevent="submit">
    <div class="flex flex-col gap-4 px-6 py-5">
      <div>
        <label for="caliber" class="mb-1.5 block text-[13px] font-semibold text-[#3a3e44]">
          Caliber <span class="text-[#b4452f]">*</span>
        </label>
        <input
          id="caliber"
          v-model="caliber.caliber"
          type="text"
          name="caliber"
          placeholder="Name of caliber"
          required
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[14px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
        />
        <p class="mt-1 text-xs text-gray-500">
          The full name, such as 9mm Luger, 7.62x39mm, or .308 Winchester.
        </p>
      </div>

      <div>
        <label for="label" class="mb-1.5 block text-[13px] font-semibold text-[#3a3e44]">Label</label>
        <input
          id="label"
          v-model="caliber.label"
          type="text"
          name="label"
          placeholder="Display label"
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[14px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
        />
        <p class="mt-1 text-xs text-gray-500">
          The short label shown throughout the app, such as 9mm or 5.56.
        </p>
      </div>

      <div>
        <label for="caliber_type_id" class="mb-1.5 block text-[13px] font-semibold text-[#3a3e44]">
          Caliber Type <span class="text-[#b4452f]">*</span>
        </label>
        <select
          id="caliber_type_id"
          v-model="caliber.caliber_type_id"
          name="caliber_type_id"
          required
          class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[14px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
        >
          <option v-for="(caliberType, i) in caliberTypes" :key="i" :value="caliberType.id">
            {{ caliberType.label }}
          </option>
        </select>
        <p class="mt-1 text-xs text-gray-500">
          The type of caliber, such as rimfire, centerfire, or shotgun.
        </p>
      </div>

      <FormError v-if="error" :error="error" />
    </div>

    <div class="flex items-center justify-end gap-2.5 border-t border-[#eef0f1] bg-[#fafbfb] px-6 py-3.5">
      <button
        type="button"
        class="rounded border border-[#c2c6ca] bg-white px-4 py-2 text-[14px] font-semibold text-[#3a3e44] transition-colors hover:bg-[#f5f6f7]"
        @click="emit('cancel')"
      >
        Cancel
      </button>
      <button
        type="submit"
        :disabled="loading"
        class="inline-flex items-center justify-center gap-2 rounded border border-[#b08a2e] bg-brass px-4 py-2 text-[14px] font-semibold text-[#1a1c1f] transition-colors hover:bg-[#b8902f] disabled:opacity-50"
      >
        <LoaderCircle v-if="loading" class="h-4 w-4 animate-spin" />
        {{ loading ? 'Saving...' : 'Add Caliber' }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, computed } from 'vue'
import { LoaderCircle } from 'lucide-vue-next'
import { useCalibersStore } from '@/stores/calibers'
import { useReferenceStore } from '@/stores/reference'
import FormError from '@/components/FormError.vue'

const emit = defineEmits(['complete', 'cancel'])

const calibersStore = useCalibersStore()
const referenceStore = useReferenceStore()

const caliberTypes = computed(() => referenceStore.caliberType)

const loading = ref(false)
const error = ref(null)
const caliber = ref({
  caliber: '',
  label: '',
  caliber_type_id: '',
})

async function submit() {
  error.value = null
  loading.value = true
  try {
    const { data } = await calibersStore.create(caliber.value)
    emit('complete', data)
  } catch (err) {
    if (err.response?.data?.errors) {
      err.errorBag = err.response.data.errors
    }
    error.value = err
  } finally {
    loading.value = false
  }
}
</script>
