import { defineStore } from 'pinia';
import { axiosInstance, queryParams } from '@/plugins/axios';

export const useInventoriesStore = defineStore('inventories', () => {
  async function fetchAll(params) {
    const { data } = await axiosInstance.get(`/inventories${queryParams(params)}`);
    return data;
  }

  async function fetchForAmmo(ammunitionId) {
    const { data } = await axiosInstance.get(
      `/inventories?filter[ammunition_id]=${ammunitionId}&sort=-inventory_date,rounds`,
    );
    return data;
  }

  async function create(payload) {
    const { data } = await axiosInstance.post('/inventories', payload);
    return data;
  }

  async function update(id, payload) {
    const { data } = await axiosInstance.put(`/inventories/${id}`, payload);
    return data;
  }

  return { fetchAll, fetchForAmmo, create, update };
});
