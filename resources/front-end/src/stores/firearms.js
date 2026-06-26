import { defineStore } from 'pinia';
import { axiosInstance, queryParams } from '@/plugins/axios';

export const useFirearmsStore = defineStore('firearms', () => {
  async function fetchAll(params) {
    const { data } = await axiosInstance.get(`/firearms${queryParams(params)}`);
    return data;
  }

  async function fetchOne(firearmId, params) {
    const { data } = await axiosInstance.get(`/firearms/${firearmId}${queryParams(params)}`);
    return data;
  }

  async function create(payload) {
    const { data } = await axiosInstance.post('/firearms', payload);
    return data;
  }

  async function update(id, payload) {
    const { data } = await axiosInstance.put(`/firearms/${id}`, payload);
    return data;
  }

  async function fetchNotes(firearmId, params) {
    const { data } = await axiosInstance.get(
      `/firearms/${firearmId}/notes${queryParams(params)}`
    );
    return data;
  }

  async function createNote(firearmId, payload) {
    const { data } = await axiosInstance.post(`/firearms/${firearmId}/notes`, payload);
    return data;
  }

  async function fetchActivity(firearmId, params) {
    const { data } = await axiosInstance.get(`/firearms/${firearmId}/activity${queryParams(params)}`);
    return data;
  }

  return { fetchAll, fetchOne, create, update, fetchNotes, createNote, fetchActivity };
});
