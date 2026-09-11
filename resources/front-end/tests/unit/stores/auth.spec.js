import { describe, expect, it, vi, beforeEach } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';

const get = vi.fn();
const post = vi.fn();
const patch = vi.fn();
const put = vi.fn();

vi.mock('@/plugins/axios', () => ({
  axiosInstance: {
    get: (...args) => get(...args),
    post: (...args) => post(...args),
    patch: (...args) => patch(...args),
    put: (...args) => put(...args),
    defaults: { headers: { common: {} } },
  },
}));

import { useAuthStore } from '@/stores/auth';

beforeEach(() => {
  setActivePinia(createPinia());
  localStorage.clear();
  get.mockReset();
  post.mockReset();
  patch.mockReset();
  put.mockReset();
});

describe('restoreFromStorage', () => {
  it('loads the current user when a token is present', async () => {
    localStorage.setItem('access_token', 'saved-token');
    get.mockResolvedValue({
      data: {
        data: { id: 1, name: 'Alex Rivera' },
        meta: {
          picture_storage: {
            driver: 'local',
            aws_configured: false,
            uploads_enabled: false,
            notice: 'AWS photo storage is not configured. Photo uploads are unavailable.',
          },
        },
      },
    });

    const store = useAuthStore();
    await store.restoreFromStorage();

    expect(get).toHaveBeenCalledWith('/auth/me');
    expect(store.isAuthenticated).toBe(true);
    expect(store.currentUser).toEqual({ id: 1, name: 'Alex Rivera' });
    expect(store.pictureUploadsEnabled).toBe(false);
    expect(store.pictureStorage.driver).toBe('local');
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

describe('public authentication flows', () => {
  it('loads registration and password-reset availability', async () => {
    get.mockResolvedValue({
      data: {
        data: { registration_enabled: true, password_reset_enabled: true },
      },
    });
    const store = useAuthStore();

    await store.loadPublicConfiguration();

    expect(get).toHaveBeenCalledWith('/auth/configuration');
    expect(store.registrationEnabled).toBe(true);
    expect(store.passwordResetEnabled).toBe(true);
  });

  it('posts forgot-password and reset-password requests', async () => {
    post.mockResolvedValue({ data: { message: 'ok' } });
    const store = useAuthStore();

    await store.forgotPassword('user@example.com');
    await store.resetPassword({ token: 'token', email: 'user@example.com' });

    expect(post).toHaveBeenNthCalledWith(1, '/auth/forgot-password', {
      email: 'user@example.com',
    });
    expect(post).toHaveBeenNthCalledWith(2, '/auth/reset-password', {
      token: 'token',
      email: 'user@example.com',
    });
  });
});

describe('preferences', () => {
  it('updates the current user after saving profile information', async () => {
    patch.mockResolvedValue({
      data: { data: { id: 1, name: 'Alex Updated', email: 'alex@example.com' } },
    });
    const store = useAuthStore();

    const result = await store.updateProfile({ name: 'Alex Updated', email: 'alex@example.com' });

    expect(patch).toHaveBeenCalledWith('/auth/profile', {
      name: 'Alex Updated',
      email: 'alex@example.com',
    });
    expect(store.currentUser).toEqual({ id: 1, name: 'Alex Updated', email: 'alex@example.com' });
    expect(result.data.name).toBe('Alex Updated');
  });

  it('stores the replacement token after changing the password', async () => {
    put.mockResolvedValue({
      data: {
        data: { id: 1, name: 'Alex Rivera', email: 'alex@example.com' },
        authorisation: { access_token: 'replacement-token', expires_in: 3600 },
      },
    });
    const store = useAuthStore();

    await store.updatePassword({
      current_password: 'current-password',
      password: 'new-secure-password',
      password_confirmation: 'new-secure-password',
    });

    expect(put).toHaveBeenCalledWith('/auth/password', {
      current_password: 'current-password',
      password: 'new-secure-password',
      password_confirmation: 'new-secure-password',
    });
    expect(localStorage.getItem('access_token')).toBe('replacement-token');
    expect(store.currentUser.email).toBe('alex@example.com');
  });
});
