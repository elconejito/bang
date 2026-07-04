import { pick } from 'lodash';

export function useForm() {
  /**
   * Reset formRef back to the matching keys from originalRef.
   * Replaces the Options API pattern of initData('formPropertyName').
   */
  function initData(formRef, originalRef) {
    const paths = Object.keys(formRef.value);
    formRef.value = pick(originalRef.value, paths);
  }

  function removeEmpties(data) {
    const ret = {};
    Object.keys(data).forEach((key) => {
      if (data[key]) ret[key] = data[key];
    });
    return ret;
  }

  return { initData, removeEmpties };
}
