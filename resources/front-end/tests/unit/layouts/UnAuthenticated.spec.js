import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';

import UnAuthenticated from '@/layouts/UnAuthenticated.vue';

describe('UnAuthenticated', () => {
  it('centers auth content within the available viewport without nesting full-screen pages', () => {
    const wrapper = mount(UnAuthenticated, {
      global: {
        stubs: {
          TopNavigation: true,
          RouterView: {
            template: '<div data-test="auth-page" />',
          },
          SiteFooter: true,
        },
      },
    });

    expect(wrapper.classes()).toContain('min-h-dvh');
    expect(wrapper.get('main').classes()).toEqual(
      expect.arrayContaining(['min-h-[calc(100dvh-7.25rem)]', 'items-center', 'justify-center'])
    );
    expect(wrapper.get('[data-test="auth-page"]').exists()).toBe(true);
  });
});
