<template>
  <form>
    <div class="mb-6">
      <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500">General Settings</h3>
      <div class="mb-4">
        <label for="manufacturer" class="block text-sm font-medium text-gray-700 mb-1">
          Manufacturer <span class="text-red-500">*</span>
        </label>
        <input
          type="text"
          class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
          id="manufacturer" name="manufacturer" placeholder="Manufacturer"
          v-model="ammunition.manufacturer" required
        />
        <p class="mt-1 text-xs text-gray-500">The name of the manufacturer, like "Federal" or "Hornady"</p>
      </div>
      <div class="mb-4">
        <label for="label" class="block text-sm font-medium text-gray-700 mb-1">
          Label <span class="text-red-500">*</span>
        </label>
        <input
          type="text"
          class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
          id="label" name="label" placeholder="Name or Model"
          v-model="ammunition.label" required
        />
        <p class="mt-1 text-xs text-gray-500">How this should show up across the website</p>
      </div>
      <div class="mb-4">
        <label for="purpose_id" class="block text-sm font-medium text-gray-700 mb-1">Purpose</label>
        <select
          class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
          id="purpose_id" name="purpose_id" v-model="ammunition.purpose_id"
        >
          <option value="">- Select One -</option>
          <option v-for="(item, i) in purpose" :key="`purpose-${i}`" :value="item.id">{{ item.label }}</option>
        </select>
      </div>
    </div>

    <!-- Shotgun -->
    <div v-if="caliber.caliber_type_id === 3" class="mb-6">
      <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500">Additional Settings</h3>
      <div class="mb-4 grid grid-cols-2 gap-4">
        <div>
          <label for="shell_type_id" class="block text-sm font-medium text-gray-700 mb-1">Shell Type</label>
          <select
            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            id="shell_type_id" name="shell_type_id" v-model="ammunition.shell_type_id"
          >
            <option value="">- Select One -</option>
            <option v-for="(item, i) in shellType" :key="`shell-type-${i}`" :value="item.id">{{ item.label }}</option>
          </select>
        </div>
        <div>
          <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">Weight (oz)</label>
          <input
            type="text"
            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            id="weight" name="weight" placeholder="Weight" v-model="ammunition.weight"
          />
        </div>
      </div>
      <div class="mb-4 grid grid-cols-2 gap-4">
        <div>
          <label for="shell_length_id" class="block text-sm font-medium text-gray-700 mb-1">Shell Length</label>
          <select
            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            id="shell_length_id" name="shell_length_id" v-model="ammunition.shell_length_id"
          >
            <option value="">- Select One -</option>
            <option v-for="(item, i) in shellLength" :key="`shell-length-${i}`" :value="item.id">{{ item.label }}</option>
          </select>
        </div>
        <div>
          <label for="shot_material_id" class="block text-sm font-medium text-gray-700 mb-1">Shot Material</label>
          <select
            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            id="shot_material_id" name="shot_material_id" v-model="ammunition.shot_material_id"
          >
            <option value="">- Select One -</option>
            <option v-for="(item, i) in shotMaterial" :key="`shell-material-${i}`" :value="item.id">{{ item.label }}</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Not Shotgun -->
    <div v-else class="mb-6">
      <div class="mb-4 grid grid-cols-2 gap-4">
        <div>
          <label for="bullet_type_id" class="block text-sm font-medium text-gray-700 mb-1">Bullet Type</label>
          <select
            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            id="bullet_type_id" name="bullet_type_id" v-model="ammunition.bullet_type_id"
          >
            <option value="">- Select One -</option>
            <option v-for="(item, i) in bulletType" :key="`bullet-type-${i}`" :value="item.id">{{ item.label }}</option>
          </select>
        </div>
        <div>
          <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">Weight (gr)</label>
          <input
            type="text"
            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            id="weight" name="weight" placeholder="Weight" v-model="ammunition.weight"
          />
        </div>
      </div>
      <div class="mb-4 grid grid-cols-2 gap-4">
        <div>
          <label for="ammunition_casing_id" class="block text-sm font-medium text-gray-700 mb-1">Case Material</label>
          <select
            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            id="ammunition_casing_id" name="ammunition_casing_id" v-model="ammunition.ammunition_casing_id"
          >
            <option value="">- Select One -</option>
            <option v-for="(item, i) in ammunitionCasing" :key="`ammunition-case-${i}`" :value="item.id">{{ item.label }}</option>
          </select>
        </div>
        <div>
          <label for="ammunition_condition_id" class="block text-sm font-medium text-gray-700 mb-1">Condition</label>
          <select
            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            id="ammunition_condition_id" name="ammunition_condition_id" v-model="ammunition.ammunition_condition_id"
          >
            <option value="">- Select One -</option>
            <option v-for="(item, i) in ammunitionCondition" :key="`ammunition-condition-${i}`" :value="item.id">{{ item.label }}</option>
          </select>
        </div>
      </div>
      <div class="mb-4 max-w-xs">
        <label for="primer_type_id" class="block text-sm font-medium text-gray-700 mb-1">Primer Type</label>
        <select
          class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-400"
          id="primer_type_id" name="primer_type_id" v-model="ammunition.primer_type_id" :disabled="primerIsDisabled"
        >
          <option value="">- Select One -</option>
          <option v-for="(item, i) in primerType" :key="`primer-type-${i}`" :value="item.id">{{ item.label }}</option>
        </select>
      </div>
    </div>

    <FormError v-if="error" :error="error" />

    <div class="mt-6">
      <ActionButton text="Save Changes" :is-loading="loading" variant="primary" @click="submit" />
    </div>
  </form>
</template>

<script setup>
import { ref, computed, onMounted, toRef } from 'vue'
import { useAmmunitionStore } from '@/stores/ammunition'
import { useReferenceStore } from '@/stores/reference'
import { useForm } from '@/composables/useForm'
import ActionButton from '@/components/ActionButton.vue'
import FormError from '@/components/FormError.vue'

const props = defineProps({
  caliber: {
    type: Object,
    required: true,
  },
  original: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['complete'])

const ammunitionStore = useAmmunitionStore()
const refStore = useReferenceStore()
const { initData, removeEmpties } = useForm()

const ammunitionCasing = computed(() => refStore.ammunitionCasing)
const ammunitionCondition = computed(() => refStore.ammunitionCondition)
const bulletType = computed(() => refStore.bulletType)
const primerType = computed(() => refStore.primerType)
const purpose = computed(() => refStore.purpose)
const shellLength = computed(() => refStore.shellLength)
const shellType = computed(() => refStore.shellType)
const shotMaterial = computed(() => refStore.shotMaterial)

const loading = ref(false)
const error = ref(null)

const isShotgun = props.caliber.caliber_type_id === 3
const isRimfire = props.caliber.caliber_type_id === 2

const ammunition = ref({
  id: '',
  manufacturer: '',
  label: '',
  purpose_id: '',
  weight: '',
  ...(isShotgun
    ? { shell_type_id: '', shell_length_id: '', shot_material_id: '' }
    : { bullet_type_id: '', ammunition_casing_id: '', ammunition_condition_id: '', primer_type_id: isRimfire ? 3 : '' }),
})

const primerIsDisabled = computed(
  () => props.caliber.caliber_type_id === 1 && ammunition.value.primer_type_id === 3
)

onMounted(() => {
  initData(ammunition, toRef(props, 'original'))
})

async function submit() {
  error.value = null
  loading.value = true
  try {
    await ammunitionStore.update(props.caliber.id, ammunition.value.id, removeEmpties(ammunition.value))
    emit('complete')
  } catch (err) {
    if (err.response?.data?.errors) err.errorBag = err.response.data.errors
    error.value = err
  } finally {
    loading.value = false
  }
}
</script>
