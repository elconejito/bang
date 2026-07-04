import { defineStore } from 'pinia';
import { axiosInstance } from '@/plugins/axios';

export const useAccessoriesStore = defineStore('accessories', () => {
  async function fetchAll() {
    const { data } = await axiosInstance.get('/accessories');
    return data;
  }

  return { fetchAll };
});
