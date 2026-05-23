import { defineStore } from 'pinia';
import { axiosInstance } from '@/plugins/axios';

export const useLocationsStore = defineStore('locations', () => {
  async function fetchAll() {
    const { data } = await axiosInstance.get('/locations');
    return data;
  }

  async function fetchOne(locationId) {
    const { data } = await axiosInstance.get(`/locations/${locationId}`);
    return data;
  }

  async function create(payload) {
    const { data } = await axiosInstance.post('/locations', payload);
    return data;
  }

  async function update(id, payload) {
    const { data } = await axiosInstance.put(`/locations/${id}`, payload);
    return data;
  }

  return { fetchAll, fetchOne, create, update };
});
