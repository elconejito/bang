import { beforeEach, describe, expect, it, vi } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';

const { get, remove } = vi.hoisted(() => ({ get: vi.fn(), remove: vi.fn() }));

vi.mock('@/plugins/axios', () => ({
  axiosInstance: { delete: remove, get },
  queryParams: vi.fn(),
}));

import { useInventoriesStore } from '@/stores/inventories';

describe('inventories store', () => {
  beforeEach(() => {
    setActivePinia(createPinia());
    get.mockReset();
    remove.mockReset();
  });

  it('deletes an inventory entry by ID', async () => {
    remove.mockResolvedValue({});

    await useInventoriesStore().remove(42);

    expect(remove).toHaveBeenCalledWith('/inventories/42');
  });

  it('uses the canonical newest date and ID ordering by default', async () => {
    get.mockResolvedValue({ data: { data: [] } });

    await useInventoriesStore().fetchForAmmo(7);

    expect(get).toHaveBeenCalledWith('/inventories', {
      params: {
        'filter[ammunition_id]': 7,
        sort: '-inventory_date,-id',
      },
    });
  });
});
