import { describe, it, expect } from 'vitest'
import { ref } from 'vue'
import { useForm } from '@/composables/useForm'

describe('useForm', () => {
  describe('removeEmpties', () => {
    it('strips falsy values', () => {
      const { removeEmpties } = useForm()
      const result = removeEmpties({ a: 1, b: '', c: null, d: 0, e: 'value' })
      expect(result).toEqual({ a: 1, e: 'value' })
    })

    it('keeps truthy values including zero-like strings', () => {
      const { removeEmpties } = useForm()
      const result = removeEmpties({ x: 'hello', y: 42, z: true })
      expect(result).toEqual({ x: 'hello', y: 42, z: true })
    })

    it('returns empty object when all values are falsy', () => {
      const { removeEmpties } = useForm()
      expect(removeEmpties({ a: '', b: null, c: 0 })).toEqual({})
    })
  })

  describe('initData', () => {
    it('populates formRef keys from originalRef', () => {
      const { initData } = useForm()
      const form = ref({ manufacturer: '', label: '', weight: '' })
      const original = ref({ manufacturer: 'Federal', label: 'Gold Medal', weight: '168', caliber_id: 5 })
      initData(form, original)
      expect(form.value).toEqual({ manufacturer: 'Federal', label: 'Gold Medal', weight: '168' })
    })

    it('ignores keys in original that are not in form', () => {
      const { initData } = useForm()
      const form = ref({ name: '' })
      const original = ref({ name: 'Glock', serial: 'ABC123', purchased_at: '2020-01-01' })
      initData(form, original)
      expect(Object.keys(form.value)).toEqual(['name'])
    })

    it('leaves form keys as-is if not present in original', () => {
      const { initData } = useForm()
      const form = ref({ name: '', notes: '' })
      const original = ref({ name: 'Sig' })
      initData(form, original)
      expect(form.value.name).toBe('Sig')
      expect(form.value.notes).toBeUndefined()
    })
  })
})
