import { describe, it, expect } from 'vitest'
import { useNumbers } from '@/composables/useNumbers'

describe('useNumbers', () => {
  const { formatQuantity, formatSmartQuantity } = useNumbers()

  describe('formatQuantity', () => {
    it('formats integers with comma separators', () => {
      expect(formatQuantity(1000)).toBe('1,000')
      expect(formatQuantity(1234567)).toBe('1,234,567')
    })

    it('returns dash for undefined', () => {
      expect(formatQuantity(undefined)).toBe('-')
    })

    it('formats small numbers without commas', () => {
      expect(formatQuantity(42)).toBe('42')
    })
  })

  describe('formatSmartQuantity', () => {
    it('returns dash for undefined', () => {
      expect(formatSmartQuantity(undefined)).toBe('-')
    })

    it('uses plain format for small numbers (< 4 digits)', () => {
      expect(formatSmartQuantity(500)).toBe('500')
    })

    it('uses abbreviated format for 4-5 digit numbers', () => {
      expect(formatSmartQuantity(1500)).toBe('1.5k')
    })

    it('uses short abbreviated format for 6+ digit numbers', () => {
      expect(formatSmartQuantity(1000000)).toBe('1m')
    })
  })
})
