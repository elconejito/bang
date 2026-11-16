import { flushPromises, mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';

const loadFailure = new Error('Unable to load reference data');
const { caliberFetch, bulletTypeFetch, shellTypeFetch, shotMaterialFetch } = vi.hoisted(() => ({
  shellTypeFetch: vi.fn(),
  shotMaterialFetch: vi.fn(),
  caliberFetch: vi.fn(),
  bulletTypeFetch: vi.fn(),
}));

vi.mock('@/stores/calibers', () => ({
  useCalibersStore: () => ({ fetchAll: caliberFetch }),
}));
vi.mock('@/stores/purposes', () => ({
  usePurposesStore: () => ({ fetchAll: vi.fn().mockResolvedValue({ data: [] }) }),
}));
vi.mock('@/stores/bulletTypes', () => ({
  useBulletTypesStore: () => ({ fetchAll: bulletTypeFetch }),
}));
vi.mock('@/stores/shellTypes', () => ({
  useShellTypesStore: () => ({ fetchAll: shellTypeFetch }),
}));
vi.mock('@/stores/shotMaterials', () => ({
  useShotMaterialsStore: () => ({ fetchAll: shotMaterialFetch }),
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
    shellTypeFetch.mockReset().mockResolvedValue({ data: [] });
    shotMaterialFetch.mockReset().mockResolvedValue({ data: [] });
    caliberFetch.mockReset();
    bulletTypeFetch.mockReset();
    bulletTypeFetch.mockResolvedValue({ data: [] });
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

  it('loads and displays the Bullet Types list', async () => {
    caliberFetch.mockResolvedValue({ data: [] });
    bulletTypeFetch.mockResolvedValue({
      data: [
        { id: 1, label: 'Full Metal Jacket', abbreviation: 'FMJ', loads_count: 0 },
        { id: 2, label: 'Hollow Point', abbreviation: 'HP', loads_count: 0 },
      ],
    });

    const wrapper = mount(ReferenceData, {
      props: { list: 'bulletType' },
      global: {
        stubs: {
          AppBreadcrumb: true,
          LoadingState: true,
          ReferenceItemModal: true,
          'router-link': true,
        },
      },
    });
    await flushPromises();

    expect(bulletTypeFetch).toHaveBeenCalledOnce();
    expect(wrapper.text()).toContain('Bullet Types');
    expect(wrapper.text()).toContain('Full Metal Jacket');
    expect(wrapper.text()).toContain('FMJ');

    await wrapper.find('input').setValue('fmj');

    expect(wrapper.text()).toContain('Full Metal Jacket');
    expect(wrapper.text()).not.toContain('Hollow Point');
  });
});

describe.each([
  ['shellType', 'Shell Types', shellTypeFetch],
  ['shotMaterial', 'Shot Materials', shotMaterialFetch],
])('ReferenceData — %s', (type, title, fetch) => {
  it('displays and searches the list, opens editing, and refreshes after saving', async () => {
    caliberFetch.mockResolvedValue({ data: [] });
    fetch.mockResolvedValue({
      data: [
        { id: 1, label: 'Custom option', loads_count: 2 },
        { id: 2, label: 'Unused option', loads_count: 0 },
      ],
    });
    const wrapper = mount(ReferenceData, {
      props: { list: type },
      global: {
        stubs: {
          AppBreadcrumb: true,
          LoadingState: true,
          ReferenceItemModal: true,
          'router-link': true,
        },
      },
    });
    await flushPromises();
    expect(wrapper.text()).toContain(title);
    expect(wrapper.text()).toContain('Custom option');
    expect(wrapper.text()).toContain('2 loads');
    expect(wrapper.findAll('button[title="Delete"]')).toHaveLength(1);
    await wrapper.find('input').setValue('custom');
    expect(wrapper.text()).not.toContain('Unused option');
    await wrapper.get('button[title="Edit"]').trigger('click');
    const modal = wrapper.findComponent({ name: 'ReferenceItemModal' });
    expect(modal.props('type')).toBe(type);
    expect(modal.props('item').id).toBe(1);
    fetch.mockResolvedValue({ data: [{ id: 1, label: 'Renamed option', loads_count: 2 }] });
    await wrapper.find('input').setValue('');
    modal.vm.$emit('saved');
    await flushPromises();
    expect(wrapper.text()).toContain('Renamed option');
  });
});
