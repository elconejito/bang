import { ref, watch } from 'vue';

export function usePersistentViewMode(
  key,
  defaultValue = 'grid',
  allowedValues = ['grid', 'table']
) {
  const storageKey = `bang:view-mode:${key}`;
  const savedValue = window.localStorage.getItem(storageKey);
  const viewMode = ref(allowedValues.includes(savedValue) ? savedValue : defaultValue);

  watch(viewMode, (value) => {
    if (allowedValues.includes(value)) {
      window.localStorage.setItem(storageKey, value);
    }
  });

  return viewMode;
}
