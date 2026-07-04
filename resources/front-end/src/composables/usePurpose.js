import { computed } from 'vue';
import { useReferenceStore } from '@/stores/reference';

export function usePurpose() {
  const referenceStore = useReferenceStore();

  const purposes = computed(() => referenceStore.purpose);

  function getPurposeLabel(id) {
    const match = purposes.value.find((p) => p.id === Number(id));
    return (match ?? { label: 'unknown' }).label;
  }

  return { purposes, getPurposeLabel };
}
