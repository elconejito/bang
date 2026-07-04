import { describe, expect, it, vi, beforeEach } from 'vitest';
import { mount, flushPromises } from '@vue/test-utils';

const login = vi.fn();
const push = vi.fn();

vi.mock('@/stores/auth', () => ({
  useAuthStore: () => ({ login }),
}));

vi.mock('vue-router', () => ({
  useRouter: () => ({ push }),
}));

import Login from '@/pages/auth/Login.vue';

function mountLogin() {
  return mount(Login, {
    global: {
      stubs: { FormError: true },
    },
  });
}

async function fillCredentials(wrapper) {
  await wrapper.find('#email').setValue('user@example.com');
  await wrapper.find('#password').setValue('secret');
}

describe('Login', () => {
  beforeEach(() => {
    login.mockReset().mockResolvedValue(undefined);
    push.mockReset();
  });

  it('submits when the form is submitted (Enter key)', async () => {
    const wrapper = mountLogin();
    await fillCredentials(wrapper);

    await wrapper.find('form').trigger('submit');
    await flushPromises();

    expect(login).toHaveBeenCalledWith({ email: 'user@example.com', password: 'secret' });
    expect(push).toHaveBeenCalledWith({ name: 'dashboard' });
  });

  it('exposes the log in button as a submit control inside the form', () => {
    const wrapper = mountLogin();

    // A type="submit" button inside the form submits it on click in the browser,
    // which routes through the same @submit.prevent="login" handler.
    const button = wrapper.find('form button');
    expect(button.exists()).toBe(true);
    expect(button.attributes('type')).toBe('submit');
  });

  it('does not navigate when login fails', async () => {
    login.mockRejectedValue(new Error('Invalid credentials'));
    const wrapper = mountLogin();
    await fillCredentials(wrapper);

    await wrapper.find('form').trigger('submit');
    await flushPromises();

    expect(push).not.toHaveBeenCalled();
  });
});
