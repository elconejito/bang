import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const fetchForEntity = vi.fn();
const detachFromEntity = vi.fn();
const setPrimaryForEntity = vi.fn();
const reorderEntity = vi.fn();
vi.mock('@/stores/pictures', () => ({
  usePicturesStore: () => ({
    fetchForEntity,
    detachFromEntity,
    setPrimaryForEntity,
    reorderEntity,
    uploadToEntity: vi.fn(),
  }),
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

  it('disables removal of a primary when other photos remain', async () => {
    const wrapper = await mountGallery([picture(1, true), picture(2)]);
    const button = wrapper.get('[aria-label="Choose another primary before removing Photo 1"]');
    expect(button.attributes('disabled')).toBeDefined();
  });
});
