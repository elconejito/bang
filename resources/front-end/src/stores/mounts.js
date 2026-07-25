import { defineStore } from 'pinia';
import { axiosInstance, queryParams } from '@/plugins/axios';

export const useMountsStore = defineStore('mounts', () => {
  async function fetchAll(params) {
    const { data } = await axiosInstance.get(`/mounts${queryParams(params)}`);
    return data;
  }
  async function fetchOne(id) {
    const { data } = await axiosInstance.get(`/mounts/${id}`);
    return data;
  }
  async function create(payload) {
    const { data } = await axiosInstance.post('/mounts', payload);
    return data;
  }
  async function update(id, payload) {
    const { data } = await axiosInstance.put(`/mounts/${id}`, payload);
    return data;
  }
  async function destroy(id) {
    await axiosInstance.delete(`/mounts/${id}`);
  }
  return { fetchAll, fetchOne, create, update, destroy };
});
