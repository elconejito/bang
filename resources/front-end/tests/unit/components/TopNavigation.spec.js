import { describe, expect, it, vi } from 'vitest';
import { mount } from '@vue/test-utils';

const push = vi.fn();

vi.mock('vue-router', () => ({
  useRoute: () => ({ path: '/' }),
  useRouter: () => ({ push }),
}));

vi.mock('@/stores/auth', () => ({
  useAuthStore: () => ({
    currentUser: { name: 'Harvey Syde' },
    logout: vi.fn(),
  }),
}));

import TopNavigation from '@/components/navigation/TopNavigation.vue';

describe('TopNavigation responsive layout', () => {
  it('keeps primary links and the account name out of the compact header', () => {
    const wrapper = mount(TopNavigation, {
      global: {
        stubs: {
          AppLogoMark: true,
          'router-link': { props: ['to'], template: '<a><slot /></a>' },
        },
      },
    });

    expect(wrapper.get('[data-testid="primary-navigation"]').classes()).toEqual(
      expect.arrayContaining(['hidden', 'md:flex'])
    );
    expect(wrapper.get('[aria-label="Toggle navigation"]').classes()).toContain('md:hidden');
    expect(wrapper.get('[data-testid="mobile-navigation"]').classes()).toContain('md:hidden');
    expect(wrapper.get('[data-testid="account-name"]').classes()).toEqual(
      expect.arrayContaining(['hidden', 'sm:inline'])
    );
  });

  it('opens the mobile navigation from the compact header control', async () => {
    const wrapper = mount(TopNavigation, {
      global: {
        stubs: {
          AppLogoMark: true,
          'router-link': { props: ['to'], template: '<a><slot /></a>' },
        },
      },
    });
    const toggle = wrapper.get('[aria-label="Toggle navigation"]');
    const mobileNavigation = wrapper.get('[data-testid="mobile-navigation"]');

    expect(toggle.attributes('aria-expanded')).toBe('false');
    expect(mobileNavigation.attributes('style')).toContain('display: none');

    await toggle.trigger('click');

    expect(toggle.attributes('aria-expanded')).toBe('true');
    expect(mobileNavigation.attributes('style') ?? '').not.toContain('display: none');
    expect(mobileNavigation.text()).toContain('Firearms');
    expect(mobileNavigation.text()).toContain('Training');
    expect(mobileNavigation.text()).toContain('Preferences');
  });

  it('links the account menu to preferences', async () => {
    const wrapper = mount(TopNavigation, {
      global: {
        stubs: {
          AppLogoMark: true,
          'router-link': { props: ['to'], template: '<a :data-route="to.name"><slot /></a>' },
        },
      },
    });

    await wrapper.get('[aria-haspopup="true"]').trigger('click');

    expect(wrapper.get('[data-route="Preferences"]').text()).toBe('Preferences');
  });
});
