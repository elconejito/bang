import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';
import { createMemoryHistory, createRouter } from 'vue-router';
import routes from '@/router/routes';

const updateProfile = vi.fn();
const updatePassword = vi.fn();
const authState = {
  currentUser: { id: 1, name: 'Alex Rivera', email: 'alex@example.com' },
  updateProfile,
  updatePassword,
};

vi.mock('@/stores/auth', () => ({
  useAuthStore: () => authState,
}));

import Preferences from '@/pages/settings/Preferences.vue';

function mountPage() {
  return mount(Preferences, {
    global: {
      stubs: {
        AppBreadcrumb: true,
        FormError: { props: ['error'], template: '<div>{{ error.message }}</div>' },
      },
    },
  });
}

describe('Preferences', () => {
  beforeEach(() => {
    updateProfile.mockReset().mockResolvedValue({
      data: { id: 1, name: 'Alex Updated', email: 'alex.updated@example.com' },
      message: 'Your profile information has been updated.',
    });
    updatePassword.mockReset().mockResolvedValue({ message: 'Your password has been updated.' });
    authState.currentUser = { id: 1, name: 'Alex Rivera', email: 'alex@example.com' };
  });

  it('shows the current profile information and saves edits', async () => {
    const wrapper = mountPage();

    expect(wrapper.get('#name').element.value).toBe('Alex Rivera');
    expect(wrapper.get('#email').element.value).toBe('alex@example.com');
    await wrapper.get('#name').setValue('Alex Updated');
    await wrapper.get('#email').setValue('alex.updated@example.com');
    await wrapper.findAll('form')[0].trigger('submit');
    await flushPromises();

    expect(updateProfile).toHaveBeenCalledWith({
      name: 'Alex Updated',
      email: 'alex.updated@example.com',
    });
    expect(wrapper.text()).toContain('Your profile information has been updated.');
  });

  it('is available from the protected settings route', () => {
    const router = createRouter({ history: createMemoryHistory(), routes });

    expect(router.resolve({ name: 'Preferences' }).href).toBe('/settings/preferences');
  });

  it('submits the current password and clears sensitive fields after success', async () => {
    const wrapper = mountPage();
    await wrapper.get('#current_password').setValue('current-password');
    await wrapper.get('#new_password').setValue('new-secure-password');
    await wrapper.get('#password_confirmation').setValue('new-secure-password');
    await wrapper.findAll('form')[1].trigger('submit');
    await flushPromises();

    expect(updatePassword).toHaveBeenCalledWith({
      current_password: 'current-password',
      password: 'new-secure-password',
      password_confirmation: 'new-secure-password',
    });
    expect(wrapper.get('#current_password').element.value).toBe('');
    expect(wrapper.get('#new_password').element.value).toBe('');
    expect(wrapper.text()).toContain('Your password has been updated.');
  });

  it('shows server validation feedback for a failed password change', async () => {
    updatePassword.mockRejectedValue({
      message: 'Request failed',
      response: { data: { errors: { current_password: ['The current password is incorrect.'] } } },
    });
    const wrapper = mountPage();
    await wrapper.get('#current_password').setValue('wrong-password');
    await wrapper.get('#new_password').setValue('new-secure-password');
    await wrapper.get('#password_confirmation').setValue('new-secure-password');
    await wrapper.findAll('form')[1].trigger('submit');
    await flushPromises();

    expect(wrapper.text()).toContain('Request failed');
  });
});
