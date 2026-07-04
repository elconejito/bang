import { ref, reactive, watch } from 'vue';

export function useLoading() {
  const isLoading = ref(false);
  const loadingQueue = reactive({});

  watch(
    loadingQueue,
    (value) => {
      if (isLoading.value) {
        const total = Object.keys(value).length;
        const complete = Object.keys(value).filter((key) => value[key]).length;
        if (total === complete) {
          isLoading.value = false;
        }
      }
    },
    { deep: true, immediate: true }
  );

  return { isLoading, loadingQueue };
}
