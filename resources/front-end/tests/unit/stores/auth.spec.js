import { describe, expect, it, vi, beforeEach } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';

const get = vi.fn();
const post = vi.fn();

vi.mock('@/plugins/axios', () => ({
  axiosInstance: {
    get: (...args) => get(...args),
    post: (...args) => post(...args),
    defaults: { headers: { common: {} } },
  },
}));

import { useAuthStore } from '@/stores/auth';

beforeEach(() => {
  setActivePinia(createPinia());
  localStorage.clear();
  get.mockReset();
  post.mockReset();
});

describe('restoreFromStorage', () => {
  it('loads the current user when a token is present', async () => {
    localStorage.setItem('access_token', 'saved-token');
    get.mockResolvedValue({ data: { data: { id: 1, name: 'Alex Rivera' } } });

    const store = useAuthStore();
    await store.restoreFromStorage();

    expect(get).toHaveBeenCalledWith('/auth/me');
    expect(store.isAuthenticated).toBe(true);
    expect(store.currentUser).toEqual({ id: 1, name: 'Alex Rivera' });
  });

  it('clears auth state when the saved token is no longer valid', async () => {
    localStorage.setItem('access_token', 'stale-token');
    get.mockRejectedValue(new Error('unauthenticated'));

    const store = useAuthStore();
    await store.restoreFromStorage();

    expect(store.isAuthenticated).toBe(false);
    expect(store.currentUser).toBeNull();
    expect(localStorage.getItem('access_token')).toBeNull();
  });

  it('does nothing when there is no saved token', async () => {
    const store = useAuthStore();
    await store.restoreFromStorage();

    expect(get).not.toHaveBeenCalled();
    expect(store.isAuthenticated).toBe(false);
  });
});
