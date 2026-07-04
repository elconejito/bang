import { defineStore } from 'pinia';
import { axiosInstance, queryParams } from '@/plugins/axios';

export const useMiscAccessoriesStore = defineStore('miscAccessories', () => {
  async function fetchAll(params) {
    const { data } = await axiosInstance.get(`/misc-accessories${queryParams(params)}`);
    return data;
  }

  async function fetchOne(id) {
    const { data } = await axiosInstance.get(`/misc-accessories/${id}`);
    return data;
  }

  async function create(payload) {
    const { data } = await axiosInstance.post('/misc-accessories', payload);
    return data;
  }

  async function update(id, payload) {
    const { data } = await axiosInstance.put(`/misc-accessories/${id}`, payload);
    return data;
  }

  async function destroy(id) {
    await axiosInstance.delete(`/misc-accessories/${id}`);
  }

  return { fetchAll, fetchOne, create, update, destroy };
});
