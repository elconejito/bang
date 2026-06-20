import { defineStore } from 'pinia'
import { axiosInstance } from '@/plugins/axios'

export const usePicturesStore = defineStore('pictures', () => {
  async function fetchLibrary() {
    const { data } = await axiosInstance.get('/pictures')
    return data
  }

  async function fetchForEntity(entityType, entityId) {
    const { data } = await axiosInstance.get(`/${entityType}/${entityId}/pictures`)
    return data
  }

  async function uploadToEntity(entityType, entityId, file) {
    const form = new FormData()
    form.append('image', file)
    const { data } = await axiosInstance.post(`/${entityType}/${entityId}/pictures`, form, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return data
  }

  async function attachToEntity(entityType, entityId, pictureId) {
    const { data } = await axiosInstance.post(
      `/${entityType}/${entityId}/pictures/${pictureId}/attach`,
    )
    return data
  }

  async function detachFromEntity(entityType, entityId, pictureId) {
    await axiosInstance.delete(`/${entityType}/${entityId}/pictures/${pictureId}`)
  }

  async function setPrimaryForEntity(entityType, entityId, pictureId) {
    await axiosInstance.patch(`/${entityType}/${entityId}/pictures/${pictureId}/primary`)
  }

  async function reorderEntity(entityType, entityId, ids) {
    await axiosInstance.patch(`/${entityType}/${entityId}/pictures/reorder`, { ids })
  }

  return {
    fetchLibrary,
    fetchForEntity,
    uploadToEntity,
    attachToEntity,
    detachFromEntity,
    setPrimaryForEntity,
    reorderEntity,
  }
})
