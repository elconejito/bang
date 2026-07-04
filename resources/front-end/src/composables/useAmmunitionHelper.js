import { computed } from 'vue';

export function useAmmunitionHelper(ammunition) {
  const label = (field) => ammunition.value?.[field]?.label ?? 'None';

  const ammunitionCasingLabel = computed(() => label('ammunition_casing'));
  const ammunitionConditionLabel = computed(() => label('ammunition_condition'));
  const bulletTypeLabel = computed(() => label('bullet_type'));
  const caliberLabel = computed(() => label('caliber'));
  const purposeLabel = computed(() => label('purpose'));
  const primerTypeLabel = computed(() => label('primer_type'));
  const shellLengthLabel = computed(() => label('shell_length'));
  const shellTypeLabel = computed(() => label('shell_type'));
  const shotMaterialLabel = computed(() => label('shot_material'));

  return {
    ammunitionCasingLabel,
    ammunitionConditionLabel,
    bulletTypeLabel,
    caliberLabel,
    purposeLabel,
    primerTypeLabel,
    shellLengthLabel,
    shellTypeLabel,
    shotMaterialLabel,
  };
}
