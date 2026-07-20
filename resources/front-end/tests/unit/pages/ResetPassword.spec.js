import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const resetPassword = vi.fn();

vi.mock('@/stores/auth', () => ({
  useAuthStore: () => ({ resetPassword }),
}));

vi.mock('vue-router', () => ({
  useRoute: () => ({ query: { token: 'reset-token', email: 'user@example.com' } }),
}));

import ResetPassword from '@/pages/auth/ResetPassword.vue';

function mountPage() {
  return mount(ResetPassword, {
    global: {
      stubs: {
        FormError: true,
        RouterLink: {
          template: '<a><slot /></a>',
        },
      },
    },
  });
}

describe('ResetPassword', () => {
  beforeEach(() => {
    resetPassword.mockReset().mockResolvedValue({
      data: { message: 'Your password has been reset.' },
    });
  });

  it('submits the emailed token and new password', async () => {
    const wrapper = mountPage();
    await wrapper.get('#password').setValue('new-secure-password');
    await wrapper.get('#password_confirmation').setValue('new-secure-password');
    await wrapper.get('form').trigger('submit');
    await flushPromises();

    expect(resetPassword).toHaveBeenCalledWith({
      token: 'reset-token',
      email: 'user@example.com',
      password: 'new-secure-password',
      password_confirmation: 'new-secure-password',
    });
    expect(wrapper.text()).toContain('Your password has been reset.');
    expect(wrapper.text()).toContain('Continue to login');
  });

  it('uses the shared auth panel and field treatments', () => {
    const wrapper = mountPage();

    expect(wrapper.get('.auth-panel').exists()).toBe(true);
    expect(wrapper.findAll('.auth-input')).toHaveLength(3);
    expect(wrapper.get('button[type="submit"]').classes()).toContain('auth-primary-action');
  });
});
