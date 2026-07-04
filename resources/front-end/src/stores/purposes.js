import { defineStore } from 'pinia';
import { axiosInstance } from '@/plugins/axios';
import { useReferenceStore } from '@/stores/reference';

export const usePurposesStore = defineStore('purposes', () => {
  const referenceStore = useReferenceStore();

  async function fetchAll() {
    const { data } = await axiosInstance.get('/purpose');
    return data;
  }

  async function create(payload) {
    const { data } = await axiosInstance.post('/purpose', payload);
    await referenceStore.fetch('purpose');
    return data;
  }

  async function update(id, payload) {
    const { data } = await axiosInstance.put(`/purpose/${id}`, payload);
    await referenceStore.fetch('purpose');
    return data;
  }

  async function remove(id) {
    await axiosInstance.delete(`/purpose/${id}`);
    await referenceStore.fetch('purpose');
  }

  return { fetchAll, create, update, remove };
});
