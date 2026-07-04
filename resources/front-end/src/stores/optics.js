import { defineStore } from 'pinia';
import { axiosInstance, queryParams } from '@/plugins/axios';

export const useOpticsStore = defineStore('optics', () => {
  async function fetchAll(params) {
    const { data } = await axiosInstance.get(`/optics${queryParams(params)}`);
    return data;
  }

  async function fetchOne(id) {
    const { data } = await axiosInstance.get(`/optics/${id}`);
    return data;
  }

  async function create(payload) {
    const { data } = await axiosInstance.post('/optics', payload);
    return data;
  }

  async function update(id, payload) {
    const { data } = await axiosInstance.put(`/optics/${id}`, payload);
    return data;
  }

  async function destroy(id) {
    await axiosInstance.delete(`/optics/${id}`);
  }

  return { fetchAll, fetchOne, create, update, destroy };
});
