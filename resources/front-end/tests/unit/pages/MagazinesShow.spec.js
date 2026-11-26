import { describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const magazine = {
  id: 12,
  manufacturer: 'Magpul',
  model_name: 'PMAG GL9',
  id_marking: 'GL9-01',
  capacity: 17,
  display_status: 'in_gun',
  loaded_rounds: 15,
  loaded_ammunition: { id: 8, manufacturer: 'Federal', label: 'HST' },
  current_firearm: { id: 4, manufacturer: 'Glock', label: '19' },
  calibers: [{ id: 9, label: '9mm' }],
  firearms: [],
  thumbnail_urls: [],
  pictures_count: 0,
};

vi.mock('@/stores/magazines', () => ({
  useMagazinesStore: () => ({ fetchOne: vi.fn().mockResolvedValue({ data: magazine }) }),
}));

import MagazinesShow from '@/pages/magazines/MagazinesShow.vue';

describe('MagazinesShow', () => {
  it('includes every value shown in the magazine group row', async () => {
    const wrapper = mount(MagazinesShow, {
      props: { magazineId: magazine.id },
      global: {
        stubs: {
          AppBreadcrumb: true,
          AccessoryEventTimeline: true,
          NotesPanel: {
            name: 'NotesPanel',
            props: { hideEmptySearch: Boolean },
            template:
              '<div data-testid="notes-panel" :data-hide-empty-search="String(hideEmptySearch)" />',
          },
          'router-link': {
            props: ['to'],
            template:
              '<a :data-route-name="to?.name" :data-route-id="to?.params?.firearm_id" :data-route-location="to?.params?.location_id"><slot /></a>',
          },
        },
      },
    });
    await flushPromises();

    expect(wrapper.text()).toContain('GL9-01');
    expect(wrapper.text()).toContain('In firearm');
    expect(wrapper.text()).toContain('Federal HST');
    expect(wrapper.text()).toContain('15 / 17');
    expect(wrapper.text()).toContain('Glock 19');
    const statusLink = wrapper.get('[data-testid="status-firearm-link"]');
    expect(statusLink.attributes('data-route-name')).toBe('FirearmsShow');
    expect(statusLink.attributes('data-route-id')).toBe('4');
    expect(wrapper.getComponent({ name: 'NotesPanel' }).props('hideEmptySearch')).toBe(true);
  });

  it('links its stored location to the location record', async () => {
    magazine.current_firearm = null;
    magazine.location = { id: 6, label: 'Safe', full_label: 'Home · Safe' };

    const wrapper = mount(MagazinesShow, {
      props: { magazineId: magazine.id },
      global: {
        stubs: {
          AppBreadcrumb: true,
          AccessoryEventTimeline: true,
          NotesPanel: true,
          'router-link': {
            props: ['to'],
            template:
              '<a :data-route-name="to?.name" :data-route-location="to?.params?.location_id"><slot /></a>',
          },
        },
      },
    });
    await flushPromises();

    expect(wrapper.get('[data-testid="location-link"]').text()).toBe('Home · Safe');
    expect(wrapper.get('[data-testid="location-link"]').attributes('data-route-name')).toBe(
      'LocationsShow'
    );
    expect(wrapper.get('[data-testid="location-link"]').attributes('data-route-location')).toBe(
      '6'
    );

    magazine.current_firearm = { id: 4, manufacturer: 'Glock', label: '19' };
    delete magazine.location;
  });
});
