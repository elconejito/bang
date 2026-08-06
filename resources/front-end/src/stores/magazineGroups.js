import { defineStore } from 'pinia';
import { axiosInstance } from '@/plugins/axios';

export const useMagazineGroupsStore = defineStore('magazineGroups', () => {
  async function fetchAll(params = {}) {
    const { data } = await axiosInstance.get('/magazine-groups', { params });

    return data;
  }

  async function fetchGroupMagazines(groupKey, params = {}) {
    const { data } = await axiosInstance.get(
      `/magazine-groups/${encodeURIComponent(groupKey)}/magazines`,
      { params }
    );

    return data;
  }

  async function changeMagazineState(magazineId, payload) {
    const { data } = await axiosInstance.patch(`/magazines/${magazineId}/state`, payload);

    return data;
  }

  async function bulkUpdateMagazines(groupKey, payload) {
    const { data } = await axiosInstance.patch(
      `/magazine-groups/${encodeURIComponent(groupKey)}/magazines/bulk`,
      payload
    );

    return data;
  }

  async function createBatch(payload) {
    const { data } = await axiosInstance.post('/magazine-batches', payload);

    return data;
  }

  return { fetchAll, fetchGroupMagazines, changeMagazineState, bulkUpdateMagazines, createBatch };
});
