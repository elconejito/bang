import { flushPromises, mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';

const { caliberFetch, colorFetch, locationFetch, storeFetch, orderFetch, firearmCreate } =
  vi.hoisted(() => ({
    caliberFetch: vi.fn(),
    colorFetch: vi.fn(),
    locationFetch: vi.fn(),
    storeFetch: vi.fn(),
    orderFetch: vi.fn(),
    firearmCreate: vi.fn(),
  }));

vi.mock('@/stores/calibers', () => ({ useCalibersStore: () => ({ fetchAll: caliberFetch }) }));
vi.mock('@/stores/colors', () => ({ useColorsStore: () => ({ fetchAll: colorFetch }) }));
vi.mock('@/stores/locations', () => ({ useLocationsStore: () => ({ fetchAll: locationFetch }) }));
vi.mock('@/stores/gunStores', () => ({ useGunStoresStore: () => ({ fetchAll: storeFetch }) }));
vi.mock('@/stores/orders', () => ({ useOrdersStore: () => ({ fetchAll: orderFetch }) }));
vi.mock('@/stores/firearms', () => ({
  useFirearmsStore: () => ({ create: firearmCreate, update: vi.fn() }),
}));

import FirearmFormCard from '@/components/firearms/FirearmFormCard.vue';

function deferred() {
  let resolve;
  let reject;
  const promise = new Promise((resolvePromise, rejectPromise) => {
    resolve = resolvePromise;
    reject = rejectPromise;
  });

  return { promise, resolve, reject };
}

function mountForm() {
  return mount(FirearmFormCard, {
    global: {
      stubs: {
        LoadingState: {
          props: ['message'],
          template: '<div data-testid="loading">{{ message }}</div>',
        },
        ErrorCard: {
          props: ['error', 'retryLabel'],
          emits: ['retry'],
          template:
            '<div data-testid="error">{{ error.message }}<button @click="$emit(\'retry\')">{{ retryLabel }}</button></div>',
        },
        ReferenceItemModal: true,
      },
    },
  });
}

describe('FirearmFormCard lookup loading', () => {
  beforeEach(() => {
    caliberFetch.mockReset();
    colorFetch.mockReset();
    locationFetch.mockReset();
    storeFetch.mockReset();
    orderFetch.mockReset();
    firearmCreate.mockReset();
  });

  it('keeps controls hidden until the final lookup resolves', async () => {
    const calibers = deferred();
    const colors = deferred();
    const locations = deferred();
    const stores = deferred();
    const orders = deferred();
    caliberFetch.mockReturnValue(calibers.promise);
    colorFetch.mockReturnValue(colors.promise);
    locationFetch.mockReturnValue(locations.promise);
    storeFetch.mockReturnValue(stores.promise);
    orderFetch.mockReturnValue(orders.promise);

    const wrapper = mountForm();
    expect(wrapper.get('[aria-busy]').attributes('aria-busy')).toBe('true');
    expect(wrapper.get('[data-testid="loading"]').text()).toContain('Loading firearm options…');
    expect(wrapper.find('input').exists()).toBe(false);

    calibers.resolve({ data: [] });
    colors.resolve({ data: [] });
    locations.resolve({ data: [] });
    stores.resolve({ data: [] });
    await flushPromises();

    expect(wrapper.find('[data-testid="loading"]').exists()).toBe(true);
    expect(wrapper.find('input').exists()).toBe(false);

    orders.resolve({ data: [] });
    await flushPromises();

    expect(wrapper.get('[aria-busy]').attributes('aria-busy')).toBe('false');
    expect(wrapper.find('[data-testid="loading"]').exists()).toBe(false);
    expect(wrapper.find('input').exists()).toBe(true);
  });

  it('shows an error and retries lookups without rendering an incomplete form', async () => {
    const failure = new Error('Unable to load colors');
    caliberFetch.mockResolvedValue({ data: [] });
    colorFetch.mockRejectedValueOnce(failure).mockResolvedValue({ data: [] });
    locationFetch.mockResolvedValue({ data: [] });
    storeFetch.mockResolvedValue({ data: [] });
    orderFetch.mockResolvedValue({ data: [] });

    const wrapper = mountForm();
    await flushPromises();

    expect(wrapper.get('[aria-busy]').attributes('aria-busy')).toBe('false');
    expect(wrapper.get('[data-testid="error"]').text()).toContain('Unable to load colors');
    expect(wrapper.find('input').exists()).toBe(false);

    await wrapper.get('[data-testid="error"] button').trigger('click');
    await flushPromises();

    expect(colorFetch).toHaveBeenCalledTimes(2);
    expect(wrapper.find('[data-testid="error"]').exists()).toBe(false);
    expect(wrapper.find('input').exists()).toBe(true);
  });
});
