import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const { fetchAllLocations, fetchOneLocation, fetchOneStore } = vi.hoisted(() => ({
  fetchAllLocations: vi.fn(),
  fetchOneLocation: vi.fn(),
  fetchOneStore: vi.fn(),
}));

vi.mock('vue-router', () => ({
  useRouter: () => ({ back: vi.fn(), push: vi.fn() }),
}));

vi.mock('@/stores/locations', () => ({
  useLocationsStore: () => ({
    fetchAll: fetchAllLocations,
    fetchOne: fetchOneLocation,
    create: vi.fn(),
    update: vi.fn(),
  }),
}));

vi.mock('@/stores/gunStores', () => ({
  useGunStoresStore: () => ({
    fetchOne: fetchOneStore,
    create: vi.fn(),
    update: vi.fn(),
  }),
}));

import LocationsCreate from '@/pages/locations/LocationsCreate.vue';
import StoresCreate from '@/pages/stores/StoresCreate.vue';

const global = {
  stubs: {
    AppBreadcrumb: true,
    LoadingState: {
      props: ['message'],
      template: '<div data-testid="loading">{{ message }}</div>',
    },
    ErrorCard: {
      props: ['title', 'retryLabel'],
      emits: ['retry'],
      template:
        '<div data-testid="load-error">{{ title }}<button data-testid="retry" @click="$emit(\'retry\')">{{ retryLabel }}</button></div>',
    },
  },
};

describe('facility form loading', () => {
  beforeEach(() => {
    fetchAllLocations.mockReset();
    fetchOneLocation.mockReset();
    fetchOneStore.mockReset();
  });

  it('defers the location form until its parent options and edit values are ready', async () => {
    let resolveLocations;
    let resolveLocation;
    fetchAllLocations.mockReturnValue(
      new Promise((resolve) => {
        resolveLocations = resolve;
      })
    );
    fetchOneLocation.mockReturnValue(
      new Promise((resolve) => {
        resolveLocation = resolve;
      })
    );

    const wrapper = mount(LocationsCreate, { props: { locationId: 10 }, global });

    expect(wrapper.get('[data-testid="loading"]').text()).toContain('Loading location form');
    expect(wrapper.find('input').exists()).toBe(false);

    resolveLocations({ data: [{ id: 2, label: 'Safe' }] });
    resolveLocation({
      data: { id: 10, label: 'Top Shelf', description: 'Upper storage', parent_location_id: 2 },
    });
    await flushPromises();

    expect(wrapper.get('input').element.value).toBe('Top Shelf');
    expect(wrapper.get('textarea').element.value).toBe('Upper storage');
    expect(wrapper.get('select').element.value).toBe('2');
  });

  it('shows a retryable error instead of an incomplete location form', async () => {
    fetchAllLocations.mockRejectedValueOnce(new Error('Unavailable'));
    const wrapper = mount(LocationsCreate, { global });
    await flushPromises();

    expect(wrapper.get('[data-testid="load-error"]').text()).toContain(
      'Could not load location options'
    );
    expect(wrapper.find('input').exists()).toBe(false);

    fetchAllLocations.mockResolvedValue({ data: [] });
    await wrapper.get('[data-testid="retry"]').trigger('click');
    await flushPromises();

    expect(wrapper.find('input').exists()).toBe(true);
  });

  it('keeps the store create form immediately usable but defers store edit until values load', async () => {
    const createWrapper = mount(StoresCreate, { global });
    expect(createWrapper.find('input').exists()).toBe(true);
    expect(fetchOneStore).not.toHaveBeenCalled();

    let resolveStore;
    fetchOneStore.mockReturnValue(
      new Promise((resolve) => {
        resolveStore = resolve;
      })
    );
    const editWrapper = mount(StoresCreate, { props: { storeId: 6 }, global });
    expect(editWrapper.get('[data-testid="loading"]').text()).toContain('Loading store form');
    expect(editWrapper.find('input').exists()).toBe(false);

    resolveStore({ data: { id: 6, label: 'Eagle Rock', description: 'Local shop' } });
    await flushPromises();

    expect(editWrapper.get('input').element.value).toBe('Eagle Rock');
    expect(editWrapper.get('textarea').element.value).toBe('Local shop');
  });

  it('shows a retryable error when store edit values fail to load', async () => {
    fetchOneStore.mockRejectedValueOnce(new Error('Unavailable'));
    const wrapper = mount(StoresCreate, { props: { storeId: 6 }, global });
    await flushPromises();

    expect(wrapper.get('[data-testid="load-error"]').text()).toContain(
      'Could not load store details'
    );
    expect(wrapper.find('input').exists()).toBe(false);

    fetchOneStore.mockResolvedValue({ data: { id: 6, label: 'Eagle Rock', description: null } });
    await wrapper.get('[data-testid="retry"]').trigger('click');
    await flushPromises();

    expect(wrapper.get('input').element.value).toBe('Eagle Rock');
  });
});
