import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import PictureStorageNotice from '@/components/photos/PictureStorageNotice.vue';

describe('PictureStorageNotice', () => {
  it('shows the configured storage notice', () => {
    const wrapper = mount(PictureStorageNotice, {
      props: {
        status: {
          notice: 'AWS photo storage is not configured. Photo uploads are unavailable.',
        },
      },
    });

    expect(wrapper.get('[role="status"]').text()).toContain('AWS photo storage is not configured');
  });

  it('renders nothing when storage needs no notice', () => {
    const wrapper = mount(PictureStorageNotice, { props: { status: { notice: null } } });

    expect(wrapper.html()).toBe('<!--v-if-->');
  });
});
