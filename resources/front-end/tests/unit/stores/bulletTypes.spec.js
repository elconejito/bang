import { beforeEach, describe, expect, it, vi } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';

const { get, post, put, remove, referenceFetch } = vi.hoisted(() => ({
  get: vi.fn(),
  post: vi.fn(),
  put: vi.fn(),
  remove: vi.fn(),
  referenceFetch: vi.fn(),
}));

vi.mock('@/plugins/axios', () => ({
  axiosInstance: { get, post, put, delete: remove },
}));

vi.mock('@/stores/reference', () => ({
  useReferenceStore: () => ({ fetch: referenceFetch }),
}));

import { useBulletTypesStore } from '@/stores/bulletTypes';

describe('bullet types store', () => {
  beforeEach(() => {
    setActivePinia(createPinia());
    get.mockReset();
    post.mockReset();
    put.mockReset();
    remove.mockReset();
    referenceFetch.mockReset();
  });

  it('uses the bullet-type API and refreshes the shared reference data after writes', async () => {
    get.mockResolvedValue({ data: { data: [] } });
    post.mockResolvedValue({ data: { data: { id: 1 } } });
    put.mockResolvedValue({ data: { data: { id: 1 } } });
    remove.mockResolvedValue({});
    const store = useBulletTypesStore();

    await store.fetchAll();
    await store.create({ label: 'Full Metal Jacket', abbreviation: 'FMJ' });
    await store.update(1, { label: 'Full Metal Jacket', abbreviation: 'FMJ' });
    await store.remove(1);

    expect(get).toHaveBeenCalledWith('/bullet-type');
    expect(post).toHaveBeenCalledWith('/bullet-type', {
      label: 'Full Metal Jacket',
      abbreviation: 'FMJ',
    });
    expect(put).toHaveBeenCalledWith('/bullet-type/1', {
      label: 'Full Metal Jacket',
      abbreviation: 'FMJ',
    });
    expect(remove).toHaveBeenCalledWith('/bullet-type/1');
    expect(referenceFetch).toHaveBeenCalledTimes(3);
    expect(referenceFetch).toHaveBeenCalledWith('bulletType');
  });
});
