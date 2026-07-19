import { defineStore } from 'pinia';
import { axiosInstance, queryParams } from '@/plugins/axios';

export const useAccessoriesStore = defineStore('accessories', () => {
  async function fetchAll(params) {
    const { data } = await axiosInstance.get(`/accessories${queryParams(params)}`);
    return data;
  }

  return { fetchAll };
});
