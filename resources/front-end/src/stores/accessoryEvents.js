import { defineStore } from 'pinia'
import { axiosInstance } from '@/plugins/axios'

export const useAccessoryEventsStore = defineStore('accessoryEvents', () => {
  async function fetchForEntity(entityType, entityId) {
    const { data } = await axiosInstance.get(`/${entityType}/${entityId}/events`)
    return data
  }

  async function createForEntity(entityType, entityId, payload) {
    const { data } = await axiosInstance.post(`/${entityType}/${entityId}/events`, payload)
    return data
  }

  return { fetchForEntity, createForEntity }
})
