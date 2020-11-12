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
        <small class="form-text text-muted">
          Purpose description
        </small>
      </div>
    </fieldset>

    <!--If Shotgun-->
    <fieldset v-if="caliber.caliber_type_id === 3">
      <!-- Shotgun Options -->
      <h3>Additional Settings</h3>
      <div class="form-group row">
        <div class="col-6">
          <label for="shell_type_id" class="form-control-label">Shell Type</label>
          <select class="form-control" id="shell_type_id" name="shell_type_id" v-model="ammunition.shell_type_id">
            <option>- Select One -</option>
            <option v-for="(item, i) in shellType" :key="`shell-type-${i}`" :value="item.id">{{ item.label }}</option>
          </select>
          <small class="form-text text-muted">
            Shell Type description
          </small>
        </div>
        <div class="col-6">
          <label for="weight" class="form-control-label">Weight (oz)</label>
          <input type="text" class="form-control" id="weight" name="weight" placeholder="Weight" v-model="ammunition.weight">
          <small class="form-text text-muted">
            Weight description
          </small>
        </div>
      </div>

      <div class="form-group row">
        <div class="col-6">
          <label for="shell_length_id" class="form-control-label">Shell Length</label>
          <select class="form-control" id="shell_length_id" name="shell_length_id" v-model="ammunition.shell_length_id">
            <option>- Select One -</option>
            <option v-for="(item, i) in shellLength" :key="`shell-length-${i}`" :value="item.id">{{ item.label }}</option>
          </select>
          <small class="form-text text-muted">
            Shell Length description
          </small>
        </div>

        <div class="col-6">
          <label for="shot_material_id" class="form-control-label">Shot Material</label>
          <select class="form-control" id="shot_material_id" name="shot_material_id" v-model="ammunition.shot_material_id">
            <option>- Select One -</option>
            <option v-for="(item, i) in shotMaterial" :key="`shell-material-${i}`" :value="item.id">{{ item.label }}</option>
          </select>
          <small class="form-text text-muted">
            Shot Material description
          </small>
        </div>
      </div>
    </fieldset>

    <!--If NOT Shotgun-->
    <fieldset v-else>
      <!-- Bullet Options -->
      <div class="form-group row">
        <div class="col-sm-6">
          <label for="bullet_type_id" class="form-control-label">Bullet Type</label>
          <select class="form-control" id="bullet_type_id" name="bullet_type_id" v-model="ammunition.bullet_type_id">
            <option>- Select One -</option>
            <option v-for="(item, i) in bulletType" :key="`bullet-type-${i}`" :value="item.id">{{ item.label }}</option>
          </select>
          <small class="form-text text-muted">
            Bullet Type description
          </small>
        </div>
        <div class="col-sm-6">
          <label for="weight" class="form-control-label">Weight (gr)</label>
          <input type="text" class="form-control" id="weight" name="weight" placeholder="Weight" v-model="ammunition.weight">
          <small class="form-text text-muted">
            Weight description
          </small>
        </div>
      </div>
      <div class="form-group row">
        <div class="col-sm-6">
          <label for="ammunition_casing_id" class="form-control-label">Case Material</label>
          <select class="form-control" id="ammunition_casing_id" name="ammunition_casing_id" v-model="ammunition.ammunition_casing_id">
            <option>- Select One -</option>
            <option v-for="(item, i) in ammunitionCasing" :key="`ammunition-case-${i}`" :value="item.id">{{ item.label }}</option>
          </select>
          <small class="form-text text-muted">
            Case Material description
          </small>
        </div>
        <div class="col-sm-6">
          <label for="ammunition_condition_id" class="form-control-label">Condition</label>
          <select class="form-control" id="ammunition_condition_id" name="ammunition_condition_id" v-model="ammunition.ammunition_condition_id">
            <option>- Select One -</option>
            <option v-for="(item, i) in ammunitionCondition" :key="`ammunition-condition-${i}`" :value="item.id">{{ item.label }}</option>
          </select>
          <small class="form-text text-muted">
            Ammunition Condition description
          </small>
        </div>
      </div>

      <div class="form-group row">
        <div class="col-sm-6">
          <label for="primer_type_id" class="form-control-label">Primer Type</label>
          <select class="form-control" id="primer_type_id" name="primer_type_id" v-model="ammunition.primer_type_id" :disabled="primerIsDisabled">
            <option>- Select One -</option>
            <option v-for="(item, i) in primerType" :key="`primer-type-${i}`" :value="item.id">{{ item.label }}</option>
          </select>
          <small class="form-text text-muted">
            Primer Type description
          </small>
        </div>
      </div>
    </fieldset>

    <FormError v-if="error" :error="error" />

    <div class="form-group">
      <ActionButton text="Add New" :is-loading="loading" class="btn-primary" @click="submit" />
    </div>
  </form>
</template>

<script>
import { mapGetters } from 'vuex';
import ActionButton from '../ActionButton';
import FormError from '../FormError';
import HasForm from 'mixins/HasForm';

export default {
  name: 'AmmunitionForm',
  components: { FormError, ActionButton },
  mixins: [HasForm],
  props: {
    caliber: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      error: false,
      loading: false,
      ammunition: this.createAmmoFields(),
    };
  },
  computed: {
    ...mapGetters('reference', [
      'ammunitionCasing',
      'ammunitionCondition',
      'bulletType',
      'caliberType',
      'primerType',
      'purpose',
      'shellLength',
      'shellType',
      'shotMaterial',
    ]),
    primerIsDisabled() {
      // If its rimfire, lock the type
      return this.caliber.caliber_type_id === 2 && this.ammunition.primer_type_id === 3;
    },
  },
  methods: {
    createAmmoFields() {
      const commonFields = {
        manufacturer: '',
        label: '',
        purpose_id: '',
        weight: '',
      };
      const shottyFields = {
        shell_type_id: '',
        shell_length_id: '',
        shot_material_id: '',
      };
      const nonShottyFields = {
        bullet_type_id: '',
        ammunition_casing_id: '',
        ammunition_condition_id: '',
        // Set PrimerType if its rimfire
        primer_type_id: this.caliber.caliber_type_id === 2 ? 3 : '',
      };

      return Object.assign(
        commonFields,
        this.caliber.caliber_type_id === 3 ? shottyFields : nonShottyFields
      );
    },
    submit() {
      console.log('AmmunitionForm submit()');
      // init statuses
      this.error = false;
      this.loading = true;

      // gather data
      const data = {
        caliberId: this.caliber.id,
        data: this.removeEmpties(this.ammunition),
      };

      // submit to api
      this.$store
        .dispatch('ammunition/store', data)
        .then((response) => {
          console.log('AmmunitionForm submit() dispatch then', response, data);
          this.$emit('complete');
        })
        .catch((error) => {
          console.error('AmmunitionForm submit() dispatch catch', error, data);
          this.error = this.$errorProcessor(error);
        })
        .finally((response) => {
          console.log('AmmunitionForm submit() dispatch finally', response, data);
          this.loading = false;
        });
      // reset statuses
    },
  },
};
</script>

<style scoped></style>
