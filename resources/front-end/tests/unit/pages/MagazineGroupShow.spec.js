import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';
import { nextTick, reactive } from 'vue';
import MagazineGroupShow from '@/pages/magazines/MagazineGroupShow.vue';

const mocks = vi.hoisted(() => ({
  routeState: {
    fullPath: '/magazines/groups/12',
    query: { lifecycle_status: 'active', per_page: '25' },
  },
  push: vi.fn(),
  replace: vi.fn(),
  fetchGroupMagazines: vi.fn(),
  bulkUpdateMagazines: vi.fn(),
  fetchLocations: vi.fn(),
}));
const route = reactive(mocks.routeState);
mocks.route = route;

vi.mock('vue-router', () => ({
  useRoute: () => mocks.route,
  useRouter: () => ({ push: mocks.push, replace: mocks.replace }),
}));
vi.mock('@/stores/magazineGroups', () => ({
  useMagazineGroupsStore: () => ({
    fetchGroupMagazines: mocks.fetchGroupMagazines,
    bulkUpdateMagazines: mocks.bulkUpdateMagazines,
  }),
}));
vi.mock('@/stores/locations', () => ({
  useLocationsStore: () => ({ fetchAll: mocks.fetchLocations }),
}));

const magazine = {
  id: 12,
  id_marking: 'GL9-01',
  capacity: 17,
  lifecycle_status: 'active',
  display_status: 'empty',
  loaded_rounds: 0,
  loaded_ammunition: null,
};

function mountPage() {
  return mount(MagazineGroupShow, {
    props: { groupKey: '12' },
    global: {
      stubs: {
        AppBreadcrumb: true,
        MagazineStateModal: true,
        MagazineBulkEditModal: {
          props: ['magazines', 'locations', 'saving', 'serverError'],
          template:
            '<button type="button" data-testid="stub-bulk-save" @click="$emit(\'save\', { label: \'Batch\' })">Save bulk changes</button>',
        },
        'router-link': { template: '<a><slot /></a>' },
      },
    },
  });
}

describe('MagazineGroupShow bulk editing', () => {
  beforeEach(() => {
    mocks.route.fullPath = '/magazines/groups/12';
    mocks.route.query = { lifecycle_status: 'active', per_page: '25' };
    mocks.push.mockReset();
    mocks.replace.mockReset();
    mocks.fetchGroupMagazines.mockResolvedValue({
      data: [magazine],
      group: { manufacturer: 'Glock', model_name: 'GL9', capacity: 17, calibers: [] },
      meta: { current_page: 1, last_page: 2, per_page: 25, from: 1, to: 1, total: 26 },
    });
    mocks.fetchLocations.mockResolvedValue({ data: [] });
    mocks.bulkUpdateMagazines.mockResolvedValue({
      data: { updated_count: 1 },
      meta: { remaining_group_key: null, updated_group_key: 77 },
    });
  });

  it('clears selected rows when pagination query changes', async () => {
    const wrapper = mountPage();
    await flushPromises();

    await wrapper.get('[data-testid="enter-magazine-bulk-mode"]').trigger('click');
    await wrapper.get('[data-testid="magazine-row-12"]').trigger('click');
    expect(wrapper.text()).toContain('1 selected');

    mocks.route.query.page = '2';
    await nextTick();

    expect(wrapper.text()).toContain('0 selected');
  });

  it('submits selected ids and follows the updated group metadata', async () => {
    const wrapper = mountPage();
    await flushPromises();

    await wrapper.get('[data-testid="enter-magazine-bulk-mode"]').trigger('click');
    await wrapper.get('[data-testid="magazine-row-12"]').trigger('click');
    await wrapper.get('[data-testid="magazine-bulk-edit"]').trigger('click');
    await wrapper.get('[data-testid="stub-bulk-save"]').trigger('click');
    await flushPromises();

    expect(mocks.bulkUpdateMagazines).toHaveBeenCalledWith('12', {
      magazine_ids: [12],
      changes: { label: 'Batch' },
    });
    expect(mocks.replace).toHaveBeenCalledWith({
      name: 'MagazineGroupShow',
      params: { group: '77' },
      query: { lifecycle_status: 'active', per_page: '25', page: undefined },
    });
  });
});
