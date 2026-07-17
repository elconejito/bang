import { beforeEach, describe, expect, it, vi } from 'vitest';

import { axiosInstance } from '@/plugins/axios';

describe('axios authentication recovery', () => {
  beforeEach(() => {
    localStorage.clear();
    window.history.replaceState({}, '', '/settings/reference-data');
  });

  it('logs out when a request remains unauthorized after a successful token refresh', async () => {
    localStorage.setItem('access_token', 'expired-token');

    axiosInstance.defaults.adapter = vi.fn(async (config) => {
      if (config.url === '/auth/refresh') {
        return {
          config,
          data: {
            authorisation: {
              access_token: 'refreshed-token',
              token_type: 'bearer',
              expires_in: 3600,
            },
          },
          headers: {},
          status: 200,
          statusText: 'OK',
        };
      }

      return Promise.reject({
        config,
        response: {
          config,
          data: { message: 'Unauthenticated.' },
          headers: {},
          status: 401,
          statusText: 'Unauthorized',
        },
      });
    });

    await expect(axiosInstance.get('/purpose')).rejects.toBeTruthy();

    expect(localStorage.getItem('access_token')).toBeNull();
    expect(window.location.pathname).toBe('/auth/login');
  });
});
