import { describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

vi.mock('@/plugins/axios', () => ({
  axiosInstance: {
    get: vi.fn().mockResolvedValue({ data: { data: [] } }),
    post: vi.fn(),
  },
}));

import AddStockModal from '@/components/ammunition/AddStockModal.vue';

describe('AddStockModal', () => {
  it('uses descriptive entry type choices and reveals only purchase fields for purchases', async () => {
    const wrapper = mount(AddStockModal, {
      props: {
        ammo: { id: 1, label: 'Training ammo', caliber: { label: '9mm' }, on_hand: 100 },
      },
      global: {
        stubs: {
          Teleport: true,
          ReferenceItemModal: true,
        },
      },
    });
    await flushPromises();

    const choices = wrapper.findAll('[role="radio"]');
    expect(choices).toHaveLength(2);
    expect(choices[0].attributes('aria-checked')).toBe('true');
    expect(wrapper.text()).toContain('Add rounds and optionally track cost and store.');
    expect(wrapper.text()).toContain('Purchase details');
    expect(wrapper.findAll('input.h-10')).toHaveLength(3);
    expect(wrapper.get('select').classes()).toContain('h-10');

    await choices[1].trigger('click');

    expect(wrapper.findAll('[role="radio"]')[1].attributes('aria-checked')).toBe('true');
    expect(wrapper.text()).toContain('Correct the count by adding or removing rounds.');
    expect(wrapper.text()).not.toContain('Purchase details');
    expect(wrapper.text()).toContain('Rounds (±)');
  });
});
