<template>
  <form>
    <fieldset>
      <h3>General Settings</h3>
      <div class="form-group">
        <label for="manufacturer" class="form-control-label">Manufacturer <span class="form-required">*</span></label>
        <input type="text" class="form-control" id="manufacturer" name="manufacturer" placeholder="Manufacturer" v-model="ammunition.manufacturer" required>
        <small class="form-text text-muted">
          The name of the manufacturer of the ammunition, like &quot;Federal&quot; or &quot;Hornady&quot;
        </small>
      </div>
      <div class="form-group">
        <label for="label" class="form-control-label">Label <span class="form-required">*</span></label>
        <input type="text" class="form-control" id="label" name="label" placeholder="Name or Model" v-model="ammunition.label" required>
        <small class="form-text text-muted">
          How this should show up across the website
        </small>
      </div>
      <div class="form-group">
        <label for="purpose_id" class="form-control-label">Purpose</label>
        <select class="form-control" id="purpose_id" name="purpose_id" v-model="ammunition.purpose_id">
          <option>- Select One -</option>
          <option v-for="(item, i) in purpose" :key="`purpose-${i}`" :value="item.id">{{ item.label }}</option>
        </select>
      </div>
    </fieldset>

    <!--If Shotgun-->
    <fieldset v-if="caliber.caliber_type_id === 3">
      <h3>Additional Settings</h3>
      <div class="form-group row">
        <div class="col-6">
          <label for="shell_type_id" class="form-control-label">Shell Type</label>
          <select class="form-control" id="shell_type_id" name="shell_type_id" v-model="ammunition.shell_type_id">
            <option>- Select One -</option>
            <option v-for="(item, i) in shellType" :key="`shell-type-${i}`" :value="item.id">{{ item.label }}</option>
          </select>
        </div>
        <div class="col-6">
          <label for="weight" class="form-control-label">Weight (oz)</label>
          <input type="text" class="form-control" id="weight" name="weight" placeholder="Weight" v-model="ammunition.weight">
        </div>
      </div>
      <div class="form-group row">
        <div class="col-6">
          <label for="shell_length_id" class="form-control-label">Shell Length</label>
          <select class="form-control" id="shell_length_id" name="shell_length_id" v-model="ammunition.shell_length_id">
            <option>- Select One -</option>
            <option v-for="(item, i) in shellLength" :key="`shell-length-${i}`" :value="item.id">{{ item.label }}</option>
          </select>
        </div>
        <div class="col-6">
          <label for="shot_material_id" class="form-control-label">Shot Material</label>
          <select class="form-control" id="shot_material_id" name="shot_material_id" v-model="ammunition.shot_material_id">
            <option>- Select One -</option>
            <option v-for="(item, i) in shotMaterial" :key="`shell-material-${i}`" :value="item.id">{{ item.label }}</option>
          </select>
        </div>
      </div>
    </fieldset>

    <!--If NOT Shotgun-->
    <fieldset v-else>
      <div class="form-group row">
        <div class="col-sm-6">
          <label for="bullet_type_id" class="form-control-label">Bullet Type</label>
          <select class="form-control" id="bullet_type_id" name="bullet_type_id" v-model="ammunition.bullet_type_id">
            <option>- Select One -</option>
            <option v-for="(item, i) in bulletType" :key="`bullet-type-${i}`" :value="item.id">{{ item.label }}</option>
          </select>
        </div>
        <div class="col-sm-6">
          <label for="weight" class="form-control-label">Weight (gr)</label>
          <input type="text" class="form-control" id="weight" name="weight" placeholder="Weight" v-model="ammunition.weight">
        </div>
      </div>
      <div class="form-group row">
        <div class="col-sm-6">
          <label for="ammunition_casing_id" class="form-control-label">Case Material</label>
          <select class="form-control" id="ammunition_casing_id" name="ammunition_casing_id" v-model="ammunition.ammunition_casing_id">
            <option>- Select One -</option>
            <option v-for="(item, i) in ammunitionCasing" :key="`ammunition-case-${i}`" :value="item.id">{{ item.label }}</option>
          </select>
        </div>
        <div class="col-sm-6">
          <label for="ammunition_condition_id" class="form-control-label">Condition</label>
          <select class="form-control" id="ammunition_condition_id" name="ammunition_condition_id" v-model="ammunition.ammunition_condition_id">
            <option>- Select One -</option>
            <option v-for="(item, i) in ammunitionCondition" :key="`ammunition-condition-${i}`" :value="item.id">{{ item.label }}</option>
          </select>
        </div>
      </div>
      <div class="form-group row">
        <div class="col-sm-6">
          <label for="primer_type_id" class="form-control-label">Primer Type</label>
          <select class="form-control" id="primer_type_id" name="primer_type_id" v-model="ammunition.primer_type_id" :disabled="primerIsDisabled">
            <option>- Select One -</option>
            <option v-for="(item, i) in primerType" :key="`primer-type-${i}`" :value="item.id">{{ item.label }}</option>
          </select>
        </div>
      </div>
    </fieldset>

    <FormError v-if="error" :error="error" />

    <div class="form-group">
      <ActionButton text="Save Changes" :is-loading="loading" class="btn-primary" @click="submit" />
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
