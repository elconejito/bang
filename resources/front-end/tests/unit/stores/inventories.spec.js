import { beforeEach, describe, expect, it, vi } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';

const { remove } = vi.hoisted(() => ({ remove: vi.fn() }));

vi.mock('@/plugins/axios', () => ({
  axiosInstance: { delete: remove },
  queryParams: vi.fn(),
}));

import { useInventoriesStore } from '@/stores/inventories';

describe('inventories store', () => {
  beforeEach(() => {
    setActivePinia(createPinia());
    remove.mockReset();
  });

  it('deletes an inventory entry by ID', async () => {
    remove.mockResolvedValue({});

    await useInventoriesStore().remove(42);

    expect(remove).toHaveBeenCalledWith('/inventories/42');
  });
});
