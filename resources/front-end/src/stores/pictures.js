import { defineStore } from 'pinia'
import { axiosInstance } from '@/plugins/axios'

export const usePicturesStore = defineStore('pictures', () => {
  async function fetchLibrary() {
    const { data } = await axiosInstance.get('/pictures')
    return data
  }

  async function fetchForFirearm(firearmId) {
    const { data } = await axiosInstance.get(`/firearms/${firearmId}/pictures`)
    return data
  }

  async function uploadToFirearm(firearmId, file) {
    const form = new FormData()
    form.append('image', file)
    const { data } = await axiosInstance.post(`/firearms/${firearmId}/pictures`, form, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return data
  }

  async function attachToFirearm(firearmId, pictureId) {
    const { data } = await axiosInstance.post(
      `/firearms/${firearmId}/pictures/${pictureId}/attach`,
    )
    return data
  }

  async function detachFromFirearm(firearmId, pictureId) {
    await axiosInstance.delete(`/firearms/${firearmId}/pictures/${pictureId}`)
  }

  async function setPrimary(firearmId, pictureId) {
    await axiosInstance.patch(`/firearms/${firearmId}/pictures/${pictureId}/primary`)
  }

  async function reorder(firearmId, ids) {
    await axiosInstance.patch(`/firearms/${firearmId}/pictures/reorder`, { ids })
  }

  return { fetchLibrary, fetchForFirearm, uploadToFirearm, attachToFirearm, detachFromFirearm, setPrimary, reorder }
})
