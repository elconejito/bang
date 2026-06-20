import { defineStore } from 'pinia'
import { axiosInstance, queryParams } from '@/plugins/axios'

export const useRangesStore = defineStore('ranges', () => {
  async function fetchAll(params) {
    const { data } = await axiosInstance.get(`/ranges${queryParams(params)}`)
    return data
  }

  async function fetchOne(id) {
    const { data } = await axiosInstance.get(`/ranges/${id}`)
    return data
  }

  async function create(payload) {
    const { data } = await axiosInstance.post('/ranges', payload)
    return data
  }

  async function update(id, payload) {
    const { data } = await axiosInstance.put(`/ranges/${id}`, payload)
    return data
  }

  async function destroy(id) {
    await axiosInstance.delete(`/ranges/${id}`)
  }

  return { fetchAll, fetchOne, create, update, destroy }
})
