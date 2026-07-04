import { defineStore } from 'pinia';
import { axiosInstance } from '@/plugins/axios';

export const useSessionLinesStore = defineStore('sessionLines', () => {
  async function create(trainingId, payload) {
    const { data } = await axiosInstance.post(`/training/${trainingId}/lines`, payload);
    return data;
  }

  async function update(trainingId, lineId, payload) {
    const { data } = await axiosInstance.put(`/training/${trainingId}/lines/${lineId}`, payload);
    return data;
  }

  async function destroy(trainingId, lineId) {
    await axiosInstance.delete(`/training/${trainingId}/lines/${lineId}`);
  }

  return { create, update, destroy };
});
