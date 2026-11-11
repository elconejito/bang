import { defineStore } from 'pinia';
import { axiosInstance } from '@/plugins/axios';
import { useReferenceStore } from '@/stores/reference';

export const useBulletTypesStore = defineStore('bulletTypes', () => {
  const referenceStore = useReferenceStore();

  async function fetchAll() {
    const { data } = await axiosInstance.get('/bullet-type');
    return data;
  }

  async function create(payload) {
    const { data } = await axiosInstance.post('/bullet-type', payload);
    await referenceStore.fetch('bulletType');
    return data;
  }

  async function update(id, payload) {
    const { data } = await axiosInstance.put(`/bullet-type/${id}`, payload);
    await referenceStore.fetch('bulletType');
    return data;
  }

  async function remove(id) {
    await axiosInstance.delete(`/bullet-type/${id}`);
    await referenceStore.fetch('bulletType');
  }

  return { fetchAll, create, update, remove };
});
