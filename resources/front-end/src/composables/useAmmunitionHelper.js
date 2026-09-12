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

const collator = new Intl.Collator(undefined, {
  sensitivity: 'base',
});

export function sortAmmunitionOptions(ammunition) {
  return [...ammunition].sort(
    (first, second) =>
      collator.compare(first.caliber?.label ?? '', second.caliber?.label ?? '') ||
      collator.compare(first.manufacturer ?? '', second.manufacturer ?? '') ||
      collator.compare(first.label ?? '', second.label ?? '') ||
      (first.weight ?? Number.MAX_SAFE_INTEGER) -
      (second.weight ?? Number.MAX_SAFE_INTEGER)
  );
}

export function ammoLabel(ammunition) {
  return [
    ammunition.caliber?.label,
    ammunition.manufacturer,
    ammunition.label,
    ammunition.weight ? `${ammunition.weight}gr` : null,
  ].filter(Boolean).join(' · ');
}
