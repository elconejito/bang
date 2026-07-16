import { beforeEach, describe, expect, it, vi } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';

const { get, remove } = vi.hoisted(() => ({ get: vi.fn(), remove: vi.fn() }));
vi.mock('@/plugins/axios', () => ({ axiosInstance: { get, delete: remove } }));

import { usePicturesStore } from '@/stores/pictures';

describe('pictures store', () => {
  beforeEach(() => {
    setActivePinia(createPinia());
    get.mockReset();
    remove.mockReset();
  });

  it('passes library query parameters to the API', async () => {
    get.mockResolvedValue({ data: { data: [] } });
    await usePicturesStore().fetchLibrary({ search: 'target' });
    expect(get).toHaveBeenCalledWith('/pictures', { params: { search: 'target' } });
  });

  it('uses the permanent picture deletion endpoint', async () => {
    remove.mockResolvedValue({ data: { message: 'Deleted' } });
    await usePicturesStore().deletePicture(4);
    expect(remove).toHaveBeenCalledWith('/pictures/4');
  });
});
