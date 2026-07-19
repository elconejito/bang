import { defineStore } from 'pinia';
import { axiosInstance, queryParams } from '@/plugins/axios';

export const useMagazinesStore = defineStore('magazines', () => {
  async function fetchAll(params) {
    const { data } = await axiosInstance.get(`/magazines${queryParams(params)}`);
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

  async function archive(id, payload) {
    const { data } = await axiosInstance.post(`/magazines/${id}/archive`, payload);
    return data;
  }

  async function unarchive(id) {
    const { data } = await axiosInstance.post(`/magazines/${id}/unarchive`);
    return data;
  }

  async function destroy(id) {
    await axiosInstance.delete(`/magazines/${id}`);
  }

  return { fetchAll, fetchOne, create, update, archive, unarchive, destroy };
});
