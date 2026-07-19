import { describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const fetchLibrary = vi.fn();

vi.mock('@/stores/pictures', () => ({
  usePicturesStore: () => ({
    fetchLibrary,
    attachToEntity: vi.fn(),
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

import LibraryPickerModal from '@/components/gallery/LibraryPickerModal.vue';

describe('LibraryPickerModal storage availability', () => {
  it('shows a specific storage error without requesting the library', async () => {
    const wrapper = mount(LibraryPickerModal, {
      props: { entityType: 'firearms', entityId: 1 },
      global: { stubs: { LoadingState: true, ModelPhoto: true } },
    });
    await flushPromises();

    expect(wrapper.get('.modal-scrim').exists()).toBe(true);
    expect(wrapper.get('.modal-shell').exists()).toBe(true);
    expect(wrapper.get('[role="alert"]').text()).toContain(
      'AWS photo storage is not configured. Photo uploads are unavailable.'
    );
    expect(fetchLibrary).not.toHaveBeenCalled();
  });
});
