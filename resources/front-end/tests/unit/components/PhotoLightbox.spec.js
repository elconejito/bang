import { afterEach, describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import PhotoLightbox from '@/components/photos/PhotoLightbox.vue';

describe('PhotoLightbox', () => {
  afterEach(() => {
    document.body.innerHTML = '';
  });

  it('is modal, focuses close, and closes with Escape', async () => {
    const trigger = document.createElement('button');
    document.body.appendChild(trigger);
    trigger.focus();
    const wrapper = mount(PhotoLightbox, {
      attachTo: document.body,
      props: { src: '/large.webp', alt: 'Firearm' },
    });
    await wrapper.vm.$nextTick();
    expect(wrapper.get('[role="dialog"]').attributes('aria-modal')).toBe('true');
    expect(document.activeElement?.getAttribute('aria-label')).toBe('Close expanded photo');
    document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape' }));
    expect(wrapper.emitted('close')).toHaveLength(1);
    wrapper.unmount();
    expect(document.activeElement).toBe(trigger);
  });
});
