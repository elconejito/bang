import { defineStore } from 'pinia';
import { axiosInstance } from '@/plugins/axios';

export const useColorsStore = defineStore('colors', () => {
  async function fetchAll() {
    const { data } = await axiosInstance.get('/colors');
    return data;
  }

  async function create(payload) {
    const { data } = await axiosInstance.post('/colors', payload);
    return data;
  }

  async function update(id, payload) {
    const { data } = await axiosInstance.put(`/colors/${id}`, payload);
    return data;
  }

  async function remove(id) {
    await axiosInstance.delete(`/colors/${id}`);
  }

  return { fetchAll, create, update, remove };
});
