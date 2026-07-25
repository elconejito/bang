import { flushPromises, mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';

const loadFailure = new Error('Unable to load reference data');
const { caliberFetch } = vi.hoisted(() => ({ caliberFetch: vi.fn() }));

vi.mock('@/stores/calibers', () => ({
  useCalibersStore: () => ({ fetchAll: caliberFetch }),
}));
vi.mock('@/stores/purposes', () => ({
  usePurposesStore: () => ({ fetchAll: vi.fn().mockResolvedValue({ data: [] }) }),
}));
vi.mock('@/stores/colors', () => ({
  useColorsStore: () => ({ fetchAll: vi.fn().mockResolvedValue({ data: [] }) }),
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
import ReferenceData from '@/pages/settings/ReferenceData.vue';

describe('ReferenceData list loading', () => {
  beforeEach(() => {
    caliberFetch.mockReset();
  });

  it('renders a loading state instead of zero counts before lists resolve', () => {
    caliberFetch.mockReturnValue(new Promise(() => {}));

    const wrapper = mount(ReferenceData, {
      global: {
        stubs: {
          AppBreadcrumb: true,
          LoadingState: { template: '<div data-test="loading-lists">Loading lists…</div>' },
          ReferenceItemModal: true,
          'router-link': true,
        },
      },
    });

    expect(wrapper.get('[data-test="loading-lists"]').exists()).toBe(true);
    expect(wrapper.text()).not.toContain('No calibers yet.');
  });

  it('renders an error state instead of empty list counts when loading fails', async () => {
    caliberFetch.mockRejectedValue(loadFailure);

    const wrapper = mount(ReferenceData, {
      global: {
        stubs: {
          AppBreadcrumb: true,
          ErrorCard: {
            props: ['error'],
            template: '<div data-test="list-error">{{ error.message }}</div>',
          },
          LoadingState: true,
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
