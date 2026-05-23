import { defineStore } from 'pinia';
import { axiosInstance } from '@/plugins/axios';

export const useTrainingStore = defineStore('training', () => {
  async function fetchAll() {
    const { data } = await axiosInstance.get('/training');
    return data;
  }

  async function fetchOne(trainingId) {
    const { data } = await axiosInstance.get(`/training/${trainingId}`);
    return data;
  }

  async function create(payload) {
    const { data } = await axiosInstance.post('/training', payload);
    return data;
  }

  async function update(id, payload) {
    const { data } = await axiosInstance.put(`/training/${id}`, payload);
    return data;
  }

  return { fetchAll, fetchOne, create, update };
});
