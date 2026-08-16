import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';
import MagazineBulkStateModal from '@/components/magazines/MagazineBulkStateModal.vue';

const fetchAmmunition = vi.hoisted(() => vi.fn());

vi.mock('@/stores/ammunition', () => ({
  useAmmunitionStore: () => ({ fetchAll: fetchAmmunition }),
}));

const magazines = [
  {
    id: 12,
    id_marking: 'GL9-01',
    capacity: 17,
    calibers: [{ id: 2, label: '9mm' }],
    current_firearm: { id: 4, manufacturer: 'Glock', label: '19' },
    loaded_ammunition: null,
    loaded_rounds: 0,
  },
  {
    id: 13,
    capacity: 19,
    calibers: [{ id: 2, label: '9mm' }],
    current_firearm: null,
    loaded_ammunition: null,
    loaded_rounds: 0,
  },
];

function mountModal(overrides = {}) {
  return mount(MagazineBulkStateModal, {
    props: {
      magazines,
      locations: [{ id: 8, label: 'Safe shelf' }],
      ...overrides,
    },
  });
}

describe('MagazineBulkStateModal', () => {
  beforeEach(() => {
    fetchAmmunition.mockResolvedValue({
      data: [
        { id: 21, manufacturer: 'Federal', label: '124gr', caliber_id: 2 },
        { id: 22, manufacturer: 'Federal', label: '55gr', caliber_id: 3 },
      ],
    });
  });

  it('maps location and loaded changes without edit-form fields', async () => {
    const wrapper = mountModal();
    await flushPromises();

    await wrapper.get('#bulk-magazine-state-placement-apply').setValue(true);
    await wrapper.get('input[name="bulk-placement"][value="location"]').setValue(true);
    await wrapper.get('#bulk-magazine-state-location').setValue('8');
    await wrapper.get('#bulk-magazine-state-contents-apply').setValue(true);
    await wrapper.get('input[name="bulk-contents"][value="loaded"]').setValue(true);
    await wrapper.get('#bulk-magazine-state-ammunition').setValue('21');
    await wrapper.get('#bulk-magazine-state-rounds').setValue(10);

    expect(wrapper.text()).toContain('eject 1 selected magazine from firearms');
    expect(wrapper.find('option[value="22"]').exists()).toBe(false);
    await wrapper.get('[data-testid="bulk-magazine-state-submit"]').trigger('click');

    expect(wrapper.emitted('save')).toEqual([
      [{ location_id: 8, loaded_ammunition_id: 21, loaded_rounds: 10 }],
    ]);
    expect(wrapper.emitted('save')[0][0]).not.toHaveProperty('manufacturer');
  });

  it('maps unassigned and empty state explicitly', async () => {
    const wrapper = mountModal();
    await flushPromises();

    await wrapper.get('#bulk-magazine-state-placement-apply').setValue(true);
    await wrapper.get('#bulk-magazine-state-contents-apply').setValue(true);
    await wrapper.get('[data-testid="bulk-magazine-state-submit"]').trigger('click');

    expect(wrapper.emitted('save')).toEqual([
      [{ location_id: null, loaded_ammunition_id: null, loaded_rounds: 0 }],
    ]);
  });

  it('requires ammunition and rounds for a loaded state', async () => {
    const wrapper = mountModal();
    await flushPromises();

    await wrapper.get('#bulk-magazine-state-contents-apply').setValue(true);
    await wrapper.get('input[name="bulk-contents"][value="loaded"]').setValue(true);
    await wrapper.get('[data-testid="bulk-magazine-state-submit"]').trigger('click');

    expect(wrapper.emitted('save')).toBeUndefined();
    expect(wrapper.get('[role="alert"]').text()).toContain('Loaded contents need ammunition');
  });

  it('uses readable current-value summaries', async () => {
    const wrapper = mountModal({ magazines: [magazines[0]] });
    await flushPromises();

    expect(wrapper.text()).toContain('All selected: Empty');
    expect(wrapper.text()).toContain('All selected: In firearm');
    expect(wrapper.text()).not.toContain('firearm:4');
  });
});
