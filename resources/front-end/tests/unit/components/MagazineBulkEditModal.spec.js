import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';
import MagazineBulkEditModal from '@/components/magazines/MagazineBulkEditModal.vue';

const stores = vi.hoisted(() => ({
  fetchAmmunition: vi.fn(),
  fetchCalibers: vi.fn(),
  fetchColors: vi.fn(),
  fetchFirearms: vi.fn(),
}));

vi.mock('@/stores/ammunition', () => ({
  useAmmunitionStore: () => ({ fetchAll: stores.fetchAmmunition }),
}));
vi.mock('@/stores/calibers', () => ({
  useCalibersStore: () => ({ fetchAll: stores.fetchCalibers }),
}));
vi.mock('@/stores/colors', () => ({
  useColorsStore: () => ({ fetchAll: stores.fetchColors }),
}));
vi.mock('@/stores/firearms', () => ({
  useFirearmsStore: () => ({ fetchAll: stores.fetchFirearms }),
}));

const magazines = [
  { id: 12, capacity: 17, current_firearm: { id: 4 } },
  { id: 13, capacity: 19, current_firearm: null },
];

function mountModal() {
  return mount(MagazineBulkEditModal, {
    props: {
      magazines,
      locations: [{ id: 8, label: 'Safe shelf' }],
    },
  });
}

describe('MagazineBulkEditModal', () => {
  beforeEach(() => {
    stores.fetchAmmunition.mockResolvedValue({
      data: [{ id: 21, manufacturer: 'Federal', label: '124gr', caliber_id: 2 }],
    });
    stores.fetchCalibers.mockResolvedValue({ data: [{ id: 2, label: '9mm' }] });
    stores.fetchColors.mockResolvedValue({ data: [{ id: 6, label: 'Blue' }] });
    stores.fetchFirearms.mockResolvedValue({
      data: [{ id: 4, manufacturer: 'Glock', label: '19' }],
    });
  });

  it('omits unchecked fields and maps nullable placement and empty contents', async () => {
    const wrapper = mountModal();
    await flushPromises();

    await wrapper.get('#bulk-magazine-label-apply').setValue(true);
    await wrapper.get('#bulk-magazine-color-apply').setValue(true);
    await wrapper.get('#bulk-magazine-placement-apply').setValue(true);
    await wrapper.get('input[name="bulk-placement"][value="unassigned"]').setValue(true);
    await wrapper.get('#bulk-magazine-contents-apply').setValue(true);
    await wrapper.get('input[name="bulk-contents"][value="empty"]').setValue(true);
    await wrapper.get('[data-testid="bulk-magazine-submit"]').trigger('click');

    expect(wrapper.emitted('save')).toEqual([
      [
        {
          label: null,
          color_id: null,
          location_id: null,
          loaded_ammunition_id: null,
          loaded_rounds: 0,
        },
      ],
    ]);
    expect(wrapper.emitted('save')[0][0]).not.toHaveProperty('manufacturer');
    expect(wrapper.emitted('save')[0][0]).not.toHaveProperty('capacity');
  });

  it('maps loaded contents and placement location while warning about firearm ejection', async () => {
    const wrapper = mountModal();
    await flushPromises();

    await wrapper.get('#bulk-magazine-placement-apply').setValue(true);
    await wrapper.get('input[name="bulk-placement"][value="location"]').setValue(true);
    await wrapper.get('#bulk-magazine-location').setValue('8');
    await wrapper.get('#bulk-magazine-contents-apply').setValue(true);
    await wrapper.get('input[name="bulk-contents"][value="loaded"]').setValue(true);
    await wrapper.get('#bulk-magazine-ammunition').setValue('21');
    await wrapper.get('#bulk-magazine-rounds').setValue(10);

    expect(wrapper.text()).toContain('eject 1 selected magazine from firearms');
    await wrapper.get('[data-testid="bulk-magazine-submit"]').trigger('click');

    expect(wrapper.emitted('save')).toEqual([
      [
        {
          location_id: 8,
          loaded_ammunition_id: 21,
          loaded_rounds: 10,
        },
      ],
    ]);
  });

  it('requires a loaded ammunition choice and rounds when applying loaded contents', async () => {
    const wrapper = mountModal();
    await flushPromises();

    await wrapper.get('#bulk-magazine-contents-apply').setValue(true);
    await wrapper.get('input[name="bulk-contents"][value="loaded"]').setValue(true);
    await wrapper.get('[data-testid="bulk-magazine-submit"]').trigger('click');

    expect(wrapper.emitted('save')).toBeUndefined();
    expect(wrapper.get('[role="alert"]').text()).toContain('Loaded contents need ammunition');
  });
});
