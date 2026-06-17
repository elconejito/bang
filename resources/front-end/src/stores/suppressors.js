import { defineStore } from 'pinia';
import { axiosInstance, queryParams } from '@/plugins/axios';

export const useSuppressorsStore = defineStore('suppressors', () => {
  async function fetchAll(params) {
    const { data } = await axiosInstance.get(`/suppressors${queryParams(params)}`);
    return data;
  }

  async function fetchOne(id) {
    const { data } = await axiosInstance.get(`/suppressors/${id}`);
    return data;
  }

  async function create(payload) {
    const { data } = await axiosInstance.post('/suppressors', payload);
    return data;
  }

  async function update(id, payload) {
    const { data } = await axiosInstance.put(`/suppressors/${id}`, payload);
    return data;
  }

  async function destroy(id) {
    await axiosInstance.delete(`/suppressors/${id}`);
  }

  return { fetchAll, fetchOne, create, update, destroy };
});
