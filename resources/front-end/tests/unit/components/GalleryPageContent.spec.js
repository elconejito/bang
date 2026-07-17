import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const fetchForEntity = vi.fn();
const detachFromEntity = vi.fn();
const setPrimaryForEntity = vi.fn();
const reorderEntity = vi.fn();
const uploadToEntity = vi.fn();
const authState = {
  pictureUploadsEnabled: false,
  pictureStorage: {
    driver: 'local',
    aws_configured: false,
    uploads_enabled: false,
    notice: 'AWS photo storage is not configured. Photo uploads are unavailable.',
  },
};
vi.mock('@/stores/pictures', () => ({
  usePicturesStore: () => ({
    fetchForEntity,
    detachFromEntity,
    setPrimaryForEntity,
    reorderEntity,
    uploadToEntity,
  }),
}));
vi.mock('@/stores/auth', () => ({
  useAuthStore: () => authState,
}));

import GalleryPageContent from '@/components/gallery/GalleryPageContent.vue';

function picture(id, primary = false) {
  return {
    id,
    name: `Photo ${id}`,
    is_primary: primary,
    card_url: `/card-${id}.webp`,
    large_url: `/large-${id}.webp`,
  };
}

async function mountGallery(pictures) {
  fetchForEntity.mockResolvedValue({ data: pictures });
  const wrapper = mount(GalleryPageContent, {
    props: { entityType: 'firearms', entityId: 1 },
    global: { stubs: { AppBreadcrumb: true, LibraryPickerModal: true, PhotoLightbox: true } },
  });
  await flushPromises();
  return wrapper;
}

describe('GalleryPageContent primary removal rules', () => {
  beforeEach(() => {
    fetchForEntity.mockReset();
    detachFromEntity.mockReset().mockResolvedValue({});
    setPrimaryForEntity.mockReset().mockResolvedValue({});
    reorderEntity.mockReset().mockResolvedValue({});
    uploadToEntity.mockReset().mockResolvedValue({});
    authState.pictureUploadsEnabled = false;
    authState.pictureStorage.uploads_enabled = false;
  });

  it('requires confirmation before removing the only photo', async () => {
    const confirm = vi.fn().mockReturnValue(false);
    window.confirm = confirm;
    const wrapper = await mountGallery([picture(1, true)]);
    await wrapper.get('[aria-label="Remove Photo 1 from item"]').trigger('click');
    expect(confirm).toHaveBeenCalledWith(
      'Remove this photo? This item will return to its default placeholder.'
    );
    expect(detachFromEntity).not.toHaveBeenCalled();
    delete window.confirm;
  });

  it('shows the picture storage notice near the uploader', async () => {
    const wrapper = await mountGallery([]);

    expect(wrapper.text()).toContain(
      'AWS photo storage is not configured. Photo uploads are unavailable.'
    );
    expect(wrapper.get('[aria-label="Upload photos"]').attributes('disabled')).toBeDefined();
    expect(
      wrapper.get('[aria-label="Add photos from library"]').attributes('disabled')
    ).toBeDefined();
  });

  it('disables removal of a primary when other photos remain', async () => {
    const wrapper = await mountGallery([picture(1, true), picture(2)]);
    const button = wrapper.get('[aria-label="Choose another primary before removing Photo 1"]');
    expect(button.attributes('disabled')).toBeDefined();
  });

  it('shows a clear error alert when an upload fails', async () => {
    authState.pictureUploadsEnabled = true;
    authState.pictureStorage.uploads_enabled = true;
    uploadToEntity.mockRejectedValue({
      response: { data: { message: 'The photo could not be uploaded. Please try again.' } },
    });
    const wrapper = await mountGallery([]);
    const input = wrapper.get('input[type="file"]');
    const file = new File(['image'], 'broken.jpg', { type: 'image/jpeg' });
    Object.defineProperty(input.element, 'files', { value: [file] });

    await input.trigger('change');
    await flushPromises();

    const alert = wrapper.get('[role="alert"]');
    expect(alert.text()).toContain('Photo upload failed');
    expect(alert.text()).toContain('broken.jpg');
    expect(alert.text()).toContain('The photo could not be uploaded. Please try again.');
    expect(alert.classes()).toContain('border-caution-border');
  });
});
