import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import SiteFooter from '@/components/SiteFooter.vue';

describe('SiteFooter', () => {
  it('uses the system footer surface and gives the icon link an accessible name', () => {
    const wrapper = mount(SiteFooter, {
      global: { stubs: { FontAwesomeIcon: true } },
    });

    expect(wrapper.get('footer').classes()).toEqual(
      expect.arrayContaining(['border-t', 'border-line', 'bg-surface'])
    );
    expect(wrapper.get('span').classes()).toEqual(
      expect.arrayContaining(['font-mono', 'text-muted'])
    );
    expect(wrapper.get('a').attributes('aria-label')).toBe('Bang on GitHub');
  });
});
