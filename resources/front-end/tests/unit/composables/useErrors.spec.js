import { describe, it, expect } from 'vitest'
import { useErrors } from '@/composables/useErrors'

describe('useErrors', () => {
  it('starts with no errors', () => {
    const { hasErrors, errors } = useErrors()
    expect(hasErrors.value).toBe(false)
    expect(errors.value).toEqual([])
  })

  it('setErrors populates errors and sets hasErrors true', () => {
    const { hasErrors, errors, setErrors } = useErrors()
    setErrors(['Field is required', 'Must be valid email'])
    expect(hasErrors.value).toBe(true)
    expect(errors.value).toEqual(['Field is required', 'Must be valid email'])
  })

  it('setErrors with empty array sets hasErrors false', () => {
    const { hasErrors, setErrors } = useErrors()
    setErrors(['some error'])
    setErrors([])
    expect(hasErrors.value).toBe(false)
  })

  it('clearErrors resets state', () => {
    const { hasErrors, errors, setErrors, clearErrors } = useErrors()
    setErrors(['some error'])
    clearErrors()
    expect(hasErrors.value).toBe(false)
    expect(errors.value).toEqual([])
  })

  it('each call returns independent state', () => {
    const a = useErrors()
    const b = useErrors()
    a.setErrors(['error in a'])
    expect(b.hasErrors.value).toBe(false)
  })
})
