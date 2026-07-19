import { describe, expect, it, vi } from 'vitest';
import { mount } from '@vue/test-utils';

vi.mock('@/stores/auth', () => ({
  useAuthStore: () => ({ register: vi.fn() }),
}));

import Register from '@/pages/auth/Register.vue';

describe('Register', () => {
  it('uses the shared auth panel, input, and brass action treatments', () => {
    const wrapper = mount(Register, {
      global: { stubs: { FormError: true, RouterLink: true } },
    });

    expect(wrapper.get('.auth-panel').exists()).toBe(true);
    expect(wrapper.get('h1').classes()).toContain('auth-heading');
    expect(wrapper.findAll('.auth-input')).toHaveLength(4);
    expect(wrapper.get('button').classes()).toContain('auth-primary-action');
  });
});
