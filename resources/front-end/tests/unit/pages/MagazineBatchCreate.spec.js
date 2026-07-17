import { describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const createBatch = vi.fn().mockResolvedValue({ data: [] });
const fetchGroupMagazines = vi.fn().mockResolvedValue({
  group: {
    key: 12,
    manufacturer: 'Magpul',
    model_name: 'PMAG GL9',
    capacity: 17,
    calibers: [{ id: 9, label: '9mm' }],
  },
});

vi.mock('vue-router', () => ({
  useRoute: () => ({ query: { group: '12' } }),
  useRouter: () => ({ back: vi.fn(), push: vi.fn() }),
}));

vi.mock('@/stores/magazineGroups', () => ({
  useMagazineGroupsStore: () => ({ fetchGroupMagazines, createBatch }),
}));

vi.mock('@/stores/calibers', () => ({
  useCalibersStore: () => ({
    fetchAll: vi.fn().mockResolvedValue({ data: [{ id: 9, label: '9mm' }] }),
  }),
}));

vi.mock('@/stores/firearms', () => ({
  useFirearmsStore: () => ({ fetchAll: vi.fn().mockResolvedValue({ data: [] }) }),
}));

vi.mock('@/stores/locations', () => ({
  useLocationsStore: () => ({ fetchAll: vi.fn().mockResolvedValue({ data: [] }) }),
}));

import MagazineBatchCreate from '@/pages/magazines/MagazineBatchCreate.vue';

describe('MagazineBatchCreate', () => {
  it('prefills the fields that define the originating magazine group', async () => {
    const wrapper = mount(MagazineBatchCreate, {
      global: { stubs: { AppBreadcrumb: true } },
    });
    await flushPromises();

    const inputs = wrapper.findAll('input');

    expect(fetchGroupMagazines).toHaveBeenCalledWith('12', { per_page: 1 });
    expect(inputs[0].element.value).toBe('Magpul');
    expect(inputs[1].element.value).toBe('PMAG GL9');
    expect(wrapper.get('input[type="number"][min="1"]').element.value).toBe('17');
    expect(wrapper.get('input[type="checkbox"][value="9"]').element.checked).toBe(true);
    expect(wrapper.get('input[placeholder="e.g. 1"]').element.value).toBe('');
    expect(wrapper.get('input[placeholder="e.g. 3"]').element.value).toBe('');

    await wrapper.get('form').trigger('submit');

    expect(createBatch).toHaveBeenCalledWith(
      expect.objectContaining({
        marking_prefix: null,
        marking_start: null,
        marking_width: null,
      })
    );
  });
});
