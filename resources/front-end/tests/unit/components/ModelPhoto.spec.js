import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import ModelPhoto from '@/components/photos/ModelPhoto.vue';

describe('ModelPhoto', () => {
  it.each([
    ['primary', 'aspect-[9/4]'],
    ['gallery', 'aspect-[4/3]'],
    ['compact', 'aspect-square'],
    ['expanded', 'object-contain'],
  ])('renders the %s display family', (family, expectedClass) => {
    const wrapper = mount(ModelPhoto, { props: { src: '/photo.webp', alt: 'Photo', family } });
    expect(wrapper.get('img').classes()).toContain(expectedClass);
  });

  it('renders a category-specific placeholder when no source exists', () => {
    const wrapper = mount(ModelPhoto, { props: { modelType: 'firearm', family: 'primary' } });
    expect(wrapper.get('[role="img"]').attributes('aria-label')).toBe('No firearm photo');
    expect(wrapper.find('svg').exists()).toBe(true);
  });
});
