import { beforeEach, describe, expect, it, vi } from 'vitest';

const patchRequest = vi.hoisted(() => vi.fn());

vi.mock('@/plugins/axios', () => ({
  axiosInstance: { patch: patchRequest },
}));
vi.mock('pinia', () => ({
  defineStore: (_name, setup) => setup,
}));

import { useMagazineGroupsStore } from '@/stores/magazineGroups';

describe('magazineGroups store', () => {
  beforeEach(() => patchRequest.mockReset());

  it('patches the group bulk endpoint with the selected ids and changes', async () => {
    const response = { data: { data: { updated_count: 2 }, meta: { updated_group_key: 77 } } };
    patchRequest.mockResolvedValue(response);
    const payload = { magazine_ids: [12, 13], changes: { label: null, loaded_rounds: 0 } };

    await expect(useMagazineGroupsStore().bulkUpdateMagazines('12', payload)).resolves.toEqual(
      response.data
    );

    expect(patchRequest).toHaveBeenCalledWith('/magazine-groups/12/magazines/bulk', payload);
  });
});
