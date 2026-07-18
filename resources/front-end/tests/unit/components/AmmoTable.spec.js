import { beforeEach, describe, expect, it, vi } from 'vitest';
import { mount } from '@vue/test-utils';
const { push } = vi.hoisted(() => ({ push: vi.fn() }));
vi.mock('vue-router', () => ({ useRouter: () => ({ push }) }));
import AmmoTable from '@/components/ammunition/AmmoTable.vue';

const ammo = {
  id: 7,
  manufacturer: 'Federal',
  label: 'American Eagle',
  on_hand: 75,
  reorder_min: 100,
  purpose: { id: 1, label: 'Range' },
};
const groups = [{ caliberId: 1, caliberLabel: '9mm', items: [ammo], totalRounds: 75, isLow: true }];

describe('AmmoTable', () => {
  beforeEach(() => push.mockReset());
  it('renders grouped card data without an image', () => {
    const wrapper = mount(AmmoTable, {
      props: { groups },
      global: { stubs: { 'router-link': { template: '<a><slot /></a>' } } },
    });
    expect(wrapper.text()).toContain('9mm');
    expect(wrapper.text()).toContain('75 ON HAND · 1 LOADS');
    expect(wrapper.text()).toContain('Federal');
    expect(wrapper.text()).toContain('American Eagle');
    expect(wrapper.text()).toContain('Range');
    expect(wrapper.text()).toContain('MIN 100');
    expect(wrapper.text()).toContain('Add a 9mm load');
    expect(wrapper.find('img').exists()).toBe(false);
  });
  it('navigates from the row and emits Stock without navigating', async () => {
    const wrapper = mount(AmmoTable, {
      props: { groups },
      global: { stubs: { 'router-link': true } },
    });
    await wrapper.get('[role="link"]').trigger('click');
    expect(push).toHaveBeenLastCalledWith({ name: 'AmmoShow', params: { ammunition_id: 7 } });
    push.mockClear();
    await wrapper.get('[aria-label="Add stock for American Eagle"]').trigger('click');
    expect(wrapper.emitted('add-stock')).toEqual([[ammo]]);
    expect(push).not.toHaveBeenCalled();
  });

  it('opens the edit screen directly from the Edit action', async () => {
    const wrapper = mount(AmmoTable, {
      props: { groups },
      global: { stubs: { 'router-link': true } },
    });

    await wrapper.get('[aria-label="Edit American Eagle"]').trigger('click');

    expect(push).toHaveBeenCalledOnce();
    expect(push).toHaveBeenCalledWith({
      name: 'AmmoEdit',
      params: { ammunition_id: ammo.id },
    });
  });
});
