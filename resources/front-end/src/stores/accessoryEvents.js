import { defineStore } from 'pinia'
import { axiosInstance, queryParams } from '@/plugins/axios'

export const useAccessoryEventsStore = defineStore('accessoryEvents', () => {
  async function fetchForEntity(entityType, entityId, params) {
    const { data } = await axiosInstance.get(`/${entityType}/${entityId}/events${queryParams(params)}`)
    return data
  }

  async function createForEntity(entityType, entityId, payload) {
    const { data } = await axiosInstance.post(`/${entityType}/${entityId}/events`, payload)
    return data
  }

  return { fetchForEntity, createForEntity }
})
