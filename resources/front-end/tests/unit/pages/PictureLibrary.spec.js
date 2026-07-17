import { describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const fetchLibrary = vi.fn();

vi.mock('@/stores/pictures', () => ({
  usePicturesStore: () => ({
    fetchLibrary,
    deletePicture: vi.fn(),
  }),
}));

vi.mock('@/stores/auth', () => ({
  useAuthStore: () => ({
    pictureUploadsEnabled: false,
    pictureStorage: {
      notice: 'AWS photo storage is not configured. Photo uploads are unavailable.',
    },
  }),
}));

import PictureLibrary from '@/pages/settings/PictureLibrary.vue';

describe('PictureLibrary storage availability', () => {
  it('shows the storage notice without requesting unavailable pictures', async () => {
    const wrapper = mount(PictureLibrary, {
      global: {
        stubs: {
          AppBreadcrumb: true,
          ModelPhoto: true,
          PhotoLightbox: true,
        },
      },
    });
    await flushPromises();

    expect(wrapper.text()).toContain(
      'AWS photo storage is not configured. Photo uploads are unavailable.'
    );
    expect(fetchLibrary).not.toHaveBeenCalled();
  });
});
