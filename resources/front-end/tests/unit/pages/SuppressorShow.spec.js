import { describe, expect, it, vi, beforeEach } from 'vitest'
import { mount, flushPromises } from '@vue/test-utils'

const fetchOne = vi.fn()
const update = vi.fn()

vi.mock('@/stores/suppressors', () => ({
  useSuppressorsStore: () => ({ fetchOne, update }),
}))

import SuppressorShow from '@/pages/accessories/suppressors/SuppressorShow.vue'

const suppressor = {
  id: 1,
  type: 'suppressor',
  label: 'Omega 9K',
  manufacturer: 'SilencerCo',
  serial: 'SC-9K-0421',
  is_nfa: true,
  caliber: { id: 1, label: '9mm' },
  mount_type: 'Tri-lug',
  length: '4.70',
  weight: '9.60',
  rounds_fired: 1890,
  last_cleaned_rounds: 1640,
  firearm_id: 7,
  firearm: { id: 7, label: 'Nightstand', manufacturer: 'Glock' },
  mounted_since: '2024-04-30',
  location: null,
  pictures_count: 0,
  thumbnail_urls: [],
}

async function mountShow() {
  fetchOne.mockResolvedValue({ data: suppressor })
  const wrapper = mount(SuppressorShow, {
    props: { suppressorId: 1 },
    global: {
      stubs: {
        'router-link': { template: '<a><slot /></a>' },
        AppBreadcrumb: true,
        AccessoryEventTimeline: true,
        MoveAccessoryModal: true,
      },
    },
  })
  await flushPromises()
  return wrapper
}

function findButton(wrapper, text) {
  return wrapper.findAll('button').find((b) => b.text().includes(text))
}

describe('SuppressorShow', () => {
  beforeEach(() => {
    fetchOne.mockReset()
    update.mockReset()
  })

  it('renders the Move button, mounted-on detail and spec rows', async () => {
    const wrapper = await mountShow()

    expect(findButton(wrapper, 'Move')).toBeTruthy()
    expect(wrapper.text()).toContain('MOUNTED ON')
    expect(wrapper.text()).toContain('Nightstand')
    expect(wrapper.text()).toContain('since Apr 30')
    // Specs
    expect(wrapper.text()).toContain('Tri-lug')
    expect(wrapper.text()).toContain('4.7″')
    expect(wrapper.text()).toContain('9.6 oz')
    // Rounds card
    expect(wrapper.text()).toContain('at 1,640 rds')
  })

  it('opens the move modal and updates + refetches on move', async () => {
    update.mockResolvedValue({ data: suppressor })
    const wrapper = await mountShow()

    await findButton(wrapper, 'Move').trigger('click')
    await flushPromises()

    // The modal is mounted; emit its move event directly.
    const modal = wrapper.findComponent({ name: 'MoveAccessoryModal' })
    expect(modal.exists()).toBe(true)

    fetchOne.mockClear()
    modal.vm.$emit('move', 9)
    await flushPromises()

    expect(update).toHaveBeenCalledWith(1, { firearm_id: 9 })
    expect(fetchOne).toHaveBeenCalledWith(1)
  })
})
