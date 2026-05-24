import { describe, it, expect } from 'vitest'
import { useDateTimes } from '@/composables/useDateTimes'

describe('useDateTimes', () => {
  const { ago } = useDateTimes()

  it('returns a relative time string for a recent date', () => {
    const recent = new Date(Date.now() - 60 * 1000)
    expect(ago(recent)).toMatch(/minute/)
  })

  it('returns a relative time string for an old date', () => {
    expect(ago('2020-01-01')).toMatch(/year/)
  })

  it('returns "a few seconds ago" for very recent timestamps', () => {
    const now = new Date()
    expect(ago(now)).toMatch(/second/)
  })
})
