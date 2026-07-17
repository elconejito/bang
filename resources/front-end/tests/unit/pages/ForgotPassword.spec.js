import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const forgotPassword = vi.fn();

vi.mock('@/stores/auth', () => ({
  useAuthStore: () => ({ forgotPassword }),
}));

import ForgotPassword from '@/pages/auth/ForgotPassword.vue';

function mountPage() {
  return mount(ForgotPassword, {
    global: {
      stubs: {
        FormError: true,
        RouterLink: true,
      },
    },
  });
}

describe('ForgotPassword', () => {
  beforeEach(() => {
    forgotPassword.mockReset().mockResolvedValue({
      data: {
        message:
          'If an account exists for that email address, a password reset link has been sent.',
      },
    });
  });

  it('requests a reset link and shows the enumeration-safe response', async () => {
    const wrapper = mountPage();
    await wrapper.get('#email').setValue('user@example.com');
    await wrapper.get('form').trigger('submit');
    await flushPromises();

    expect(forgotPassword).toHaveBeenCalledWith('user@example.com');
    expect(wrapper.get('[role="status"]').text()).toContain(
      'If an account exists for that email address'
    );
  });
});
