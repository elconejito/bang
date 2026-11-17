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

import { useShellTypesStore } from '@/stores/shellTypes';
import { useShotMaterialsStore } from '@/stores/shotMaterials';

describe.each([
  ['shellType', '/shell-type', useShellTypesStore],
  ['shotMaterial', '/shot-material', useShotMaterialsStore],
])('%s store', (type, endpoint, useStore) => {
  beforeEach(() => {
    setActivePinia(createPinia());
    get.mockReset();
    post.mockReset();
    put.mockReset();
    remove.mockReset();
    referenceFetch.mockReset();
  });

  it('uses its API and refreshes the shared reference data after writes', async () => {
    get.mockResolvedValue({ data: { data: [] } });
    post.mockResolvedValue({ data: { data: { id: 1 } } });
    put.mockResolvedValue({ data: { data: { id: 1 } } });
    remove.mockResolvedValue({});
    const store = useStore();

    await store.fetchAll();
    await store.create({ label: 'Custom option' });
    await store.update(1, { label: 'Custom option' });
    await store.remove(1);

    expect(get).toHaveBeenCalledWith(endpoint);
    expect(post).toHaveBeenCalledWith(endpoint, {
      label: 'Custom option',
    });
    expect(put).toHaveBeenCalledWith(`${endpoint}/1`, {
      label: 'Custom option',
    });
    expect(remove).toHaveBeenCalledWith(`${endpoint}/1`);
    expect(referenceFetch).toHaveBeenCalledTimes(3);
    expect(referenceFetch).toHaveBeenCalledWith(type);
  });
});
