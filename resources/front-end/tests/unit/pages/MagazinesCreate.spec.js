import { describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

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
  useMagazineGroupsStore: () => ({ fetchGroupMagazines }),
}));

import MagazinesCreate from '@/pages/magazines/MagazinesCreate.vue';

describe('MagazinesCreate', () => {
  it('passes the originating group fields to the magazine form as defaults', async () => {
    const wrapper = mount(MagazinesCreate, {
      global: {
        stubs: {
          AppBreadcrumb: true,
          MagazineForm: {
            name: 'MagazineForm',
            props: ['defaults'],
            template: '<div data-testid="magazine-form" />',
          },
        },
      },
    });
    await flushPromises();

    expect(fetchGroupMagazines).toHaveBeenCalledWith('12', { per_page: 1 });
    expect(wrapper.getComponent({ name: 'MagazineForm' }).props('defaults')).toEqual(
      expect.objectContaining({
        manufacturer: 'Magpul',
        model_name: 'PMAG GL9',
        capacity: 17,
        calibers: [{ id: 9, label: '9mm' }],
      })
    );
  });
});
