import { defineStore } from 'pinia';
import { axiosInstance } from '@/plugins/axios';

export const useOrdersStore = defineStore('orders', () => {
  async function fetchAll() {
    const { data } = await axiosInstance.get('/orders');
    return data;
  }

  async function fetchOne(orderId) {
    const { data } = await axiosInstance.get(`/orders/${orderId}`);
    return data;
  }

  async function fetchItemOptions(orderId) {
    const { data } = await axiosInstance.get('/order-item-options', {
      params: orderId ? { order_id: orderId } : undefined,
    });
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

  return { fetchAll, fetchOne, fetchItemOptions, create, update };
});
