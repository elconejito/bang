import * as _ from 'lodash';

export default {
  computed: {
    ammunitionCasingLabel() {
      return this.ammunition.ammunition_casing ? this.ammunition.ammunition_casing.label : 'None';
    },
    ammunitionConditionLabel() {
      return this.ammunition.ammunition_condition ? this.ammunition.ammunition_condition.label : 'None';
    },
    bulletTypeLabel() {
      return this.ammunition.bullet_type ? this.ammunition.bullet_type.label : 'None';
    },
    caliberLabel() {
      return this.ammunition.caliber ? this.ammunition.caliber.label : 'None';
    },
    purposeLabel() {
      return this.ammunition.purpose ? this.ammunition.purpose.label : 'None';
    },
    primerTypeLabel() {
      return this.ammunition.primer_type ? this.ammunition.primer_type.label : 'None';
    },
    shellLengthLabel() {
      return this.ammunition.shell_length ? this.ammunition.shell_length.label : 'None';
    },
    shellTypeLabel() {
      return this.ammunition.shell_type ? this.ammunition.shell_type.label : 'None';
    },
    shotMaterialLabel() {
      return this.ammunition.shot_material ? this.ammunition.shot_material.label : 'None';
    },
  },
};
