import { defineStore } from 'pinia';
import { axiosInstance, queryParams } from '@/plugins/axios';

export const useLightsStore = defineStore('lights', () => {
  async function fetchAll(params) {
    const { data } = await axiosInstance.get(`/lights${queryParams(params)}`);
    return data;
  }

  async function fetchOne(id) {
    const { data } = await axiosInstance.get(`/lights/${id}`);
    return data;
  }

  async function create(payload) {
    const { data } = await axiosInstance.post('/lights', payload);
    return data;
  }

  async function update(id, payload) {
    const { data } = await axiosInstance.put(`/lights/${id}`, payload);
    return data;
  }

  async function destroy(id) {
    await axiosInstance.delete(`/lights/${id}`);
  }

  async function archive(id, payload) {
    const { data } = await axiosInstance.post(`/lights/${id}/archive`, payload);
    return data;
  }

  async function unarchive(id) {
    const { data } = await axiosInstance.post(`/lights/${id}/unarchive`);
    return data;
  }

  return { fetchAll, fetchOne, create, update, destroy, archive, unarchive };
});
