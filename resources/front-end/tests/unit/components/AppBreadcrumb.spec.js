import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';

const RouterLinkStub = {
  props: ['to'],
  template: '<a :href="to"><slot /></a>',
};

describe('AppBreadcrumb', () => {
  it('does not repeat a leading Home crumb after the home icon', () => {
    const wrapper = mount(AppBreadcrumb, {
      props: {
        crumbs: [{ label: 'Home', to: '/' }, { label: 'Firearms' }],
      },
      global: {
        stubs: { RouterLink: RouterLinkStub },
      },
    });

    expect(wrapper.text()).toBe('Firearms');
    expect(wrapper.findAll('a')).toHaveLength(1);
    expect(wrapper.get('a').attributes('aria-label')).toBe('Home');
  });

  it('preserves non-Home crumbs', () => {
    const wrapper = mount(AppBreadcrumb, {
      props: {
        crumbs: [{ label: 'Account' }, { label: 'Manage Lists' }],
      },
      global: {
        stubs: { RouterLink: RouterLinkStub },
      },
    });

    expect(wrapper.text()).toContain('Account');
    expect(wrapper.text()).toContain('Manage Lists');
  });
});
