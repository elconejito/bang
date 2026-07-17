import { flushPromises, mount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vitest';

const loadFailure = new Error('Unable to load reference data');

vi.mock('@/stores/calibers', () => ({
  useCalibersStore: () => ({ fetchAll: vi.fn().mockRejectedValue(loadFailure) }),
}));
vi.mock('@/stores/purposes', () => ({
  usePurposesStore: () => ({ fetchAll: vi.fn().mockResolvedValue({ data: [] }) }),
}));
vi.mock('@/stores/locations', () => ({
  useLocationsStore: () => ({ fetchAll: vi.fn().mockResolvedValue({ data: [] }) }),
}));
vi.mock('@/stores/gunStores', () => ({
  useGunStoresStore: () => ({ fetchAll: vi.fn().mockResolvedValue({ data: [] }) }),
}));
vi.mock('@/stores/ranges', () => ({
  useRangesStore: () => ({ fetchAll: vi.fn().mockResolvedValue({ data: [] }) }),
}));
vi.mock('@/composables/useLoading', () => ({
  useLoading: () => ({ isLoading: { value: false }, loadingQueue: {} }),
}));

import ReferenceData from '@/pages/settings/ReferenceData.vue';

describe('ReferenceData list loading', () => {
  it('renders an error state instead of empty list counts when loading fails', async () => {
    const wrapper = mount(ReferenceData, {
      global: {
        stubs: {
          AppBreadcrumb: true,
          ErrorCard: {
            props: ['error'],
            template: '<div data-test="list-error">{{ error.message }}</div>',
          },
          ReferenceItemModal: true,
          'router-link': true,
        },
      },
    });

    await flushPromises();

    expect(wrapper.get('[data-test="list-error"]').text()).toBe(loadFailure.message);
    expect(wrapper.text()).not.toContain('No calibers yet.');
  });
});
