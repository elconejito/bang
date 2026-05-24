import { describe, it, expect } from 'vitest'
import { nextTick } from 'vue'
import { useLoading } from '@/composables/useLoading'

describe('useLoading', () => {
  it('starts not loading', () => {
    const { isLoading } = useLoading()
    expect(isLoading.value).toBe(false)
  })

  it('setting isLoading true marks as loading', () => {
    const { isLoading } = useLoading()
    isLoading.value = true
    expect(isLoading.value).toBe(true)
  })

  it('resolves to not loading when all queue entries become true', async () => {
    const { isLoading, loadingQueue } = useLoading()
    isLoading.value = true
    loadingQueue.caliber = false
    loadingQueue.ammunition = false
    await nextTick()
    expect(isLoading.value).toBe(true)

    loadingQueue.caliber = true
    await nextTick()
    expect(isLoading.value).toBe(true)

    loadingQueue.ammunition = true
    await nextTick()
    expect(isLoading.value).toBe(false)
  })

  it('does not auto-resolve when isLoading is false', async () => {
    const { isLoading, loadingQueue } = useLoading()
    loadingQueue.item = true
    await nextTick()
    expect(isLoading.value).toBe(false)
  })

  it('each call returns independent state', async () => {
    const a = useLoading()
    const b = useLoading()
    a.isLoading.value = true
    expect(b.isLoading.value).toBe(false)
  })
})
