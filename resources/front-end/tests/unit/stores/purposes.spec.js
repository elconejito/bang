import { describe, expect, it, vi, beforeEach } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';

const get = vi.fn();
const post = vi.fn();
const put = vi.fn();
const del = vi.fn();

vi.mock('@/plugins/axios', () => ({
  axiosInstance: {
    get: (...args) => get(...args),
    post: (...args) => post(...args),
    put: (...args) => put(...args),
    delete: (...args) => del(...args),
  },
}));

const referenceFetch = vi.fn();

vi.mock('@/stores/reference', () => ({
  useReferenceStore: () => ({ fetch: referenceFetch }),
}));

import { usePurposesStore } from '@/stores/purposes';

beforeEach(() => {
  setActivePinia(createPinia());
  get.mockReset();
  post.mockReset();
  put.mockReset();
  del.mockReset();
  referenceFetch.mockReset();
});

describe('purposes store', () => {
  it('reloads the shared reference list after create', async () => {
    post.mockResolvedValue({ data: { data: { id: 1, label: 'Duty' } } });

    const store = usePurposesStore();
    const result = await store.create({ label: 'Duty' });

    expect(post).toHaveBeenCalledWith('/purpose', { label: 'Duty' });
    expect(referenceFetch).toHaveBeenCalledWith('purpose');
    expect(result).toEqual({ data: { id: 1, label: 'Duty' } });
  });

  it('reloads the shared reference list after update', async () => {
    put.mockResolvedValue({ data: { data: {} } });

    const store = usePurposesStore();
    await store.update(3, { label: 'Renamed' });

    expect(put).toHaveBeenCalledWith('/purpose/3', { label: 'Renamed' });
    expect(referenceFetch).toHaveBeenCalledWith('purpose');
  });

  it('reloads the shared reference list after remove', async () => {
    del.mockResolvedValue({});

    const store = usePurposesStore();
    await store.remove(5);

    expect(del).toHaveBeenCalledWith('/purpose/5');
    expect(referenceFetch).toHaveBeenCalledWith('purpose');
  });

  it('does not reload the shared list on a plain fetch', async () => {
    get.mockResolvedValue({ data: { data: [] } });

    const store = usePurposesStore();
    await store.fetchAll();

    expect(referenceFetch).not.toHaveBeenCalled();
  });
});
