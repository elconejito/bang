import { ref } from 'vue';

export function useErrors() {
  const hasErrors = ref(false);
  const errors = ref([]);

  function clearErrors() {
    hasErrors.value = false;
    errors.value = [];
  }

  function setErrors(newErrors) {
    errors.value = newErrors;
    hasErrors.value = newErrors.length > 0;
  }

  return { hasErrors, errors, clearErrors, setErrors };
}
