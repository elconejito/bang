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

  async function archive(id, payload) {
    const { data } = await axiosInstance.post(`/firearms/${id}/archive`, payload);
    return data;
  }

  async function unarchive(id) {
    const { data } = await axiosInstance.post(`/firearms/${id}/unarchive`);
    return data;
  }

  async function destroy(id) {
    await axiosInstance.delete(`/firearms/${id}`);
  }

  async function fetchActivity(firearmId, params) {
    const { data } = await axiosInstance.get(
      `/firearms/${firearmId}/activity${queryParams(params)}`
    );
    return data;
  }

  async function fetchMountableAccessories(firearmId) {
    const { data } = await axiosInstance.get(`/firearms/${firearmId}/mountable-accessories`);
    return data;
  }

  async function mountAccessories(firearmId, accessories) {
    await axiosInstance.post(`/firearms/${firearmId}/mount-accessories`, { accessories });
  }

  return {
    fetchAll,
    fetchOne,
    create,
    update,
    archive,
    unarchive,
    destroy,
    fetchActivity,
    fetchMountableAccessories,
    mountAccessories,
  };
});
