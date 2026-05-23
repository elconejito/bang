import { defineStore } from 'pinia';
import { axiosInstance } from '@/plugins/axios';

export const useMagazinesStore = defineStore('magazines', () => {
  async function fetchAll() {
    const { data } = await axiosInstance.get('/magazines');
    return data;
  }

  async function fetchOne(magazineId) {
    const { data } = await axiosInstance.get(`/magazines/${magazineId}`);
    return data;
  }

  async function create(payload) {
    const { data } = await axiosInstance.post('/magazines', payload);
    return data;
  }

  async function update(id, payload) {
    const { data } = await axiosInstance.put(`/magazines/${id}`, payload);
    return data;
  }

  return { fetchAll, fetchOne, create, update };
});
