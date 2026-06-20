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

  async function fetchStats() {
    const { data } = await axiosInstance.get('/training/stats');
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

  async function destroy(id) {
    await axiosInstance.delete(`/training/${id}`);
  }

  async function addTarget(trainingId, formData) {
    const { data } = await axiosInstance.post(`/training/${trainingId}/targets`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    return data;
  }

  async function deleteTarget(trainingId, targetId) {
    await axiosInstance.delete(`/training/${trainingId}/targets/${targetId}`);
  }

  return { fetchAll, fetchOne, fetchStats, create, update, destroy, addTarget, deleteTarget };
});
