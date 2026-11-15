import { beforeEach, describe, expect, it, vi } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';

const { post } = vi.hoisted(() => ({ post: vi.fn() }));

vi.mock('@/plugins/axios', () => ({
  axiosInstance: { post },
  queryParams: vi.fn(),
}));

import { useAmmunitionStore } from '@/stores/ammunition';

describe('ammunition store', () => {
  beforeEach(() => {
    setActivePinia(createPinia());
    post.mockReset();
  });

  it('checks and repairs an ammunition count', async () => {
    const response = { data: { previous_count: 1181, on_hand: 1170, corrected: true } };
    post.mockResolvedValue({ data: response });

    await expect(useAmmunitionStore().checkCount(7)).resolves.toEqual(response);

    expect(post).toHaveBeenCalledWith('/ammunition/7/check-count');
  });
});
