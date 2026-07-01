import { defineStore } from 'pinia';
import { axiosInstance, queryParams } from '@/plugins/axios';

export const useCalibersStore = defineStore('calibers', () => {
  async function fetchAll() {
    const { data } = await axiosInstance.get('/calibers');
    return data;
  }

  async function fetchOne(caliberId) {
    const { data } = await axiosInstance.get(`/calibers/${caliberId}`);
    return data;
  }

  async function fetchTotal(caliberId, params) {
    const { data } = await axiosInstance.get(`/calibers/${caliberId}/total${queryParams(params)}`);
    return data;
  }

  async function create(payload) {
    const { data } = await axiosInstance.post('/calibers', payload);
    return data;
  }

  async function update(id, payload) {
    const { data } = await axiosInstance.put(`/calibers/${id}`, payload);
    return data;
  }

  return { fetchAll, fetchOne, fetchTotal, create, update };
});
