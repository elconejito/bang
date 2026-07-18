import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

vi.mock('@/stores/accessories', () => ({
  useAccessoriesStore: () => ({
    fetchAll: vi.fn().mockResolvedValue({
      data: {
        suppressors: [{ id: 1, manufacturer: 'SilencerCo', label: 'Omega 9K' }],
        optics: [{ id: 2, manufacturer: 'Holosun', label: '507C' }],
        lights: [],
        misc: [],
        magazines: [],
      },
    }),
  }),
}));

import AccessoriesIndex from '@/pages/accessories/AccessoriesIndex.vue';

describe('AccessoriesIndex category view', () => {
  beforeEach(() => {
    window.localStorage.clear();
  });

  it('shows only the route-scoped category and has no category dropdown', async () => {
    const wrapper = mount(AccessoriesIndex, {
      props: { category: 'optics' },
      global: {
        stubs: {
          AppBreadcrumb: true,
          PageHeader: { template: '<div><slot name="actions" /></div>' },
          EmptyState: true,
          SuppressorCard: true,
          OpticCard: true,
          LightCard: true,
          MiscCard: true,
          MagazineGroupCard: true,
          'router-link': { props: ['to'], template: '<a :data-route="to.name"><slot /></a>' },
        },
      },
    });
    await flushPromises();

    expect(wrapper.findAll('optic-card-stub')).toHaveLength(1);
    expect(wrapper.findAll('suppressor-card-stub')).toHaveLength(0);
    expect(wrapper.findAll('button').some((button) => button.text().trim() === 'Category')).toBe(
      false
    );
    expect(wrapper.get('[data-route="OpticCreate"]').text()).toContain('Add Optic');
    expect(wrapper.text()).not.toContain('Add Accessory');
  });

  it('switches between the grid and table while preserving the scoped category', async () => {
    const wrapper = mount(AccessoriesIndex, {
      props: { category: 'optics' },
      global: {
        stubs: {
          AppBreadcrumb: true,
          PageHeader: { template: '<div><slot name="actions" /></div>' },
          EmptyState: true,
          SuppressorCard: true,
          OpticCard: true,
          LightCard: true,
          MiscCard: true,
          MagazineGroupCard: true,
          AccessoriesTable: {
            props: ['items', 'type'],
            template: '<div data-test="accessories-table">{{ type }}:{{ items.length }}</div>',
          },
          'router-link': { props: ['to'], template: '<a :data-route="to.name"><slot /></a>' },
        },
      },
    });
    await flushPromises();

    expect(wrapper.findAll('optic-card-stub')).toHaveLength(1);
    await wrapper
      .findAll('button')
      .find((button) => button.text().includes('Table'))
      .trigger('click');

    expect(wrapper.findAll('optic-card-stub')).toHaveLength(0);
    expect(wrapper.get('[data-test="accessories-table"]').text()).toBe('optics:1');
    expect(window.localStorage.getItem('bang:view-mode:accessories')).toBe('table');
  });
});
