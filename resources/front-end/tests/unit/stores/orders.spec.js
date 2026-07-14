import { beforeEach, describe, expect, it, vi } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';

const get = vi.fn();
const post = vi.fn();
const put = vi.fn();

vi.mock('@/plugins/axios', () => ({
  axiosInstance: {
    get: (...args) => get(...args),
    post: (...args) => post(...args),
    put: (...args) => put(...args),
  },
}));

import { useOrdersStore } from '@/stores/orders';

beforeEach(() => {
  setActivePinia(createPinia());
  get.mockReset();
  post.mockReset();
  put.mockReset();
});

describe('orders store', () => {
  it('uses the order REST endpoints', async () => {
    const payload = { store_id: 2, items: [] };
    get.mockResolvedValue({ data: { data: { id: 7 } } });
    post.mockResolvedValue({ data: { data: { id: 7 } } });
    put.mockResolvedValue({ data: { data: { id: 7 } } });
    const store = useOrdersStore();

    await store.fetchOne(7);
    await store.create(payload);
    await store.update(7, payload);

    expect(get).toHaveBeenCalledWith('/orders/7');
    expect(post).toHaveBeenCalledWith('/orders', payload);
    expect(put).toHaveBeenCalledWith('/orders/7', payload);
  });
});
