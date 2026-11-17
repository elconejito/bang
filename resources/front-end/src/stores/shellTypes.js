import { defineStore } from 'pinia';
import { axiosInstance } from '@/plugins/axios';
import { useReferenceStore } from '@/stores/reference';

export const useShellTypesStore = defineStore('shellTypes', () => {
  const referenceStore = useReferenceStore();

  async function fetchAll() {
    const { data } = await axiosInstance.get('/shell-type');
    return data;
  }

  async function create(payload) {
    const { data } = await axiosInstance.post('/shell-type', payload);
    await referenceStore.fetch('shellType');
    return data;
  }

  async function update(id, payload) {
    const { data } = await axiosInstance.put(`/shell-type/${id}`, payload);
    await referenceStore.fetch('shellType');
    return data;
  }

  async function remove(id) {
    await axiosInstance.delete(`/shell-type/${id}`);
    await referenceStore.fetch('shellType');
  }

  return { fetchAll, create, update, remove };
});
