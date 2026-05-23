import { defineStore } from 'pinia';
import { axiosInstance, queryParams } from '@/plugins/axios';

export const useAmmunitionStore = defineStore('ammunition', () => {
  async function fetchAll(caliberId, params) {
    const { data } = await axiosInstance.get(
      `/calibers/${caliberId}/ammunition${queryParams(params)}`
    );
    return data;
  }

  async function fetchOne(caliberId, ammunitionId) {
    const { data } = await axiosInstance.get(
      `/calibers/${caliberId}/ammunition/${ammunitionId}`
    );
    return data;
  }

  async function fetchTotal(ammunitionId, params) {
    const { data } = await axiosInstance.get(
      `/ammunition/${ammunitionId}/total${queryParams(params)}`
    );
    return data;
  }

  async function fetchNotes(ammunitionId, params) {
    const { data } = await axiosInstance.get(
      `/ammunition/${ammunitionId}/notes${queryParams(params)}`
    );
    return data;
  }

  async function create(caliberId, payload) {
    const { data } = await axiosInstance.post(`/calibers/${caliberId}/ammunition`, payload);
    return data;
  }

  async function update(caliberId, id, payload) {
    const { data } = await axiosInstance.put(
      `/calibers/${caliberId}/ammunition/${id}`,
      payload
    );
    return data;
  }

  async function createNote(ammunitionId, payload) {
    const { data } = await axiosInstance.post(`/ammunition/${ammunitionId}/notes`, payload);
    return data;
  }

  return { fetchAll, fetchOne, fetchTotal, fetchNotes, create, update, createNote };
});
