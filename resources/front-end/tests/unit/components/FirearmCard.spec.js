import { describe, expect, it, vi } from 'vitest';
import { mount } from '@vue/test-utils';

vi.mock('vue-router', () => ({
  useRouter: () => ({ push: vi.fn() }),
}));

import FirearmCard from '@/components/firearms/FirearmCard.vue';

describe('FirearmCard', () => {
  it('renders badges for mounted accessories', () => {
    const wrapper = mount(FirearmCard, {
      props: {
        firearm: {
          id: 1,
          label: 'Nightstand',
          manufacturer: 'Glock',
          model: '19',
          calibers: [{ id: 1, label: '9mm' }],
          mounted_accessories: [
            { id: 2, type: 'Optic', label: 'Holosun 507C' },
            { id: 3, type: 'Suppressor', label: 'Omega 9K' },
            { id: 4, type: 'Light', label: 'TLR-7A' },
          ],
          rounds_fired: 100,
          location: { label: 'Safe' },
        },
      },
    });

    expect(wrapper.text()).toContain('OPTIC');
    expect(wrapper.text()).toContain('SUPPR');
    expect(wrapper.text()).toContain('LIGHT');
    expect(wrapper.get('[title="Omega 9K"]').classes()).toContain('bg-special-bg');
  });

  it('identifies a customized firearm without replacing its manufacturer', () => {
    const wrapper = mount(FirearmCard, {
      props: {
        firearm: {
          id: 2,
          label: '1301',
          manufacturer: 'Beretta',
          model: '1301 Tactical C',
          customizer: 'Langdon Tactical',
          calibers: [],
          mounted_accessories: [],
          rounds_fired: 0,
        },
      },
    });

    expect(wrapper.text()).toContain('Beretta · 1301 Tactical C');
    expect(wrapper.text()).toContain('Customized by Langdon Tactical');
  });

  it('prominently identifies an archived firearm and its reason', () => {
    const wrapper = mount(FirearmCard, {
      props: {
        firearm: {
          id: 3,
          label: 'Retired 1911',
          manufacturer: 'Colt',
          model: '1911',
          status: 'archived',
          archive_reason: 'retired',
          archive_description: 'Preserved for historical records.',
          calibers: [],
          mounted_accessories: [],
          rounds_fired: 500,
        },
      },
    });

    expect(wrapper.text()).toContain('Archived · Retired');
    expect(wrapper.text()).toContain('Preserved for historical records.');
    expect(wrapper.text()).toContain('View');
    expect(wrapper.text()).not.toContain('Log');
  });
});
