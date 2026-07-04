import { ref } from 'vue';

/**
 * Drives the in-context "quick add" reference modal from any form that has a
 * caliber / purpose / location / store / range picker. Open the modal for a type,
 * and on save append the freshly-created item to the relevant options list and
 * select it — no refetch needed, since the store returns the created record.
 *
 * @example
 * const { quickAddType, openQuickAdd, closeQuickAdd } = useQuickAdd();
 * function onQuickAddSaved(item) {
 *   calibers.value.push(item);
 *   form.value.caliber_id = item.id;
 *   closeQuickAdd();
 * }
 */
export function useQuickAdd() {
  /** @type {import('vue').Ref<null|'caliber'|'purpose'|'location'|'store'|'range'>} */
  const quickAddType = ref(null);

  function openQuickAdd(type) {
    quickAddType.value = type;
  }

  function closeQuickAdd() {
    quickAddType.value = null;
  }

  return { quickAddType, openQuickAdd, closeQuickAdd };
}
