import { describe, expect, it, vi, beforeEach } from 'vitest';
import { mount, flushPromises } from '@vue/test-utils';

const login = vi.fn();
const loadPublicConfiguration = vi.fn();
const push = vi.fn();
const authState = {
  login,
  loadPublicConfiguration,
  registrationEnabled: false,
  passwordResetEnabled: true,
};

vi.mock('@/stores/auth', () => ({
  useAuthStore: () => authState,
}));

vi.mock('vue-router', () => ({
  useRouter: () => ({ push }),
}));

import Login from '@/pages/auth/Login.vue';

function mountLogin() {
  return mount(Login, {
    global: {
      stubs: {
        FormError: true,
        RouterLink: {
          props: ['to'],
          template: '<a :data-route="to.name"><slot /></a>',
        },
      },
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
    loadPublicConfiguration.mockReset().mockResolvedValue(undefined);
    push.mockReset();
    authState.registrationEnabled = false;
    authState.passwordResetEnabled = true;
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

  it('links to forgot password and conditionally links to registration', async () => {
    authState.registrationEnabled = true;
    const wrapper = mountLogin();
    await flushPromises();

    expect(loadPublicConfiguration).toHaveBeenCalledOnce();
    expect(wrapper.get('[data-route="forgotPassword"]').text()).toContain('Forgot password?');
    expect(wrapper.get('[data-route="register"]').text()).toContain('Register');
  });

  it('hides registration when it is disabled', async () => {
    const wrapper = mountLogin();
    await flushPromises();

    expect(wrapper.find('[data-route="register"]').exists()).toBe(false);
  });

  it('uses the shared design-system auth panel, inputs, and brass action', () => {
    const wrapper = mountLogin();

    expect(wrapper.get('.auth-panel').exists()).toBe(true);
    expect(wrapper.get('h1').classes()).toContain('auth-heading');
    expect(wrapper.findAll('.auth-input')).toHaveLength(2);
    expect(wrapper.get('button[type="submit"]').classes()).toContain('auth-primary-action');
  });
});
