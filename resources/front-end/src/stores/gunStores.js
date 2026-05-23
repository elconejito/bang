import { defineStore } from 'pinia';
import { axiosInstance } from '@/plugins/axios';

export const useGunStoresStore = defineStore('gunStores', () => {
  async function fetchAll() {
    const { data } = await axiosInstance.get('/stores');
    return data;
  }

  async function fetchOne(storeId) {
    const { data } = await axiosInstance.get(`/stores/${storeId}`);
    return data;
  }

  async function create(payload) {
    const { data } = await axiosInstance.post('/stores', payload);
    return data;
  }

  async function update(id, payload) {
    const { data } = await axiosInstance.put(`/stores/${id}`, payload);
    return data;
  }

  return { fetchAll, fetchOne, create, update };
});
