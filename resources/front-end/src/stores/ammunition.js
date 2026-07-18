import { defineStore } from 'pinia';
import { axiosInstance, queryParams } from '@/plugins/axios';

export const useAmmunitionStore = defineStore('ammunition', () => {
  async function fetchAll(params) {
    const { data } = await axiosInstance.get(`/ammunition${queryParams(params)}`);
    return data;
  }

  async function fetchOne(ammunitionId) {
    const { data } = await axiosInstance.get(`/ammunition/${ammunitionId}`);
    return data;
  }

  async function fetchTotal(ammunitionId) {
    const { data } = await axiosInstance.get(`/ammunition/${ammunitionId}/total`);
    return data;
  }

  async function fetchStats(ammunitionId) {
    const { data } = await axiosInstance.get(`/ammunition/${ammunitionId}/stats`);
    return data;
  }

  async function create(payload) {
    const { data } = await axiosInstance.post('/ammunition', payload);
    return data;
  }

  async function update(id, payload) {
    const { data } = await axiosInstance.put(`/ammunition/${id}`, payload);
    return data;
  }

  return { fetchAll, fetchOne, fetchTotal, fetchStats, create, update };
});
