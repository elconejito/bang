import { beforeEach, describe, expect, it, vi } from 'vitest';

const { post } = vi.hoisted(() => ({ post: vi.fn() }));

vi.mock('@/plugins/axios', () => ({
  axiosInstance: { post },
  queryParams: vi.fn(),
}));

vi.mock('pinia', () => ({
  defineStore: (_name, setup) => setup,
}));

import { useAccessoryEventsStore } from '@/stores/accessoryEvents';

describe('accessoryEvents store', () => {
  beforeEach(() => post.mockReset());

  it('posts firearm manual activity to its dedicated endpoint', async () => {
    post.mockResolvedValue({ data: { data: { type: 'CLEAN' } } });

    await useAccessoryEventsStore().createForEntity('firearms', 4, {
      event_type: 'CLEAN',
      event_date: '2026-07-18',
    });

    expect(post).toHaveBeenCalledWith('/firearms/4/activity', {
      event_type: 'CLEAN',
      event_date: '2026-07-18',
    });
  });
});
