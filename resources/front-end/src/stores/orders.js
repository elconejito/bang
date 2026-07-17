import { defineStore } from 'pinia';
import { axiosInstance } from '@/plugins/axios';

export const useOrdersStore = defineStore('orders', () => {
  async function fetchOne(orderId) {
    const { data } = await axiosInstance.get(`/orders/${orderId}`);
    return data;
  }

  async function create(payload) {
    const { data } = await axiosInstance.post('/orders', payload);
    return data;
  }

  async function update(orderId, payload) {
    const { data } = await axiosInstance.put(`/orders/${orderId}`, payload);
    return data;
  }

  return { fetchOne, create, update };
});
