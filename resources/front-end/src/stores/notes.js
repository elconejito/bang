import { defineStore } from 'pinia';
import { axiosInstance } from '@/plugins/axios';

export const useNotesStore = defineStore('notes', () => {
  async function fetchAll(entityType, entityId, params = {}) {
    const { data } = await axiosInstance.get(`/${entityType}/${entityId}/notes`, { params });
    return data;
  }

  async function create(entityType, entityId, payload) {
    const { data } = await axiosInstance.post(`/${entityType}/${entityId}/notes`, payload);
    return data;
  }

  return { fetchAll, create };
});
