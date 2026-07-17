import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const fetchMagazine = vi.fn();
const fetchAmmunition = vi.fn();
const fetchLocations = vi.fn();

vi.mock('@/stores/ammunition', () => ({
  useAmmunitionStore: () => ({ fetchAll: fetchAmmunition }),
}));

vi.mock('@/stores/locations', () => ({
  useLocationsStore: () => ({ fetchAll: fetchLocations }),
}));

vi.mock('@/stores/magazines', () => ({
  useMagazinesStore: () => ({ fetchOne: fetchMagazine }),
}));

vi.mock('@/stores/magazineGroups', () => ({
  useMagazineGroupsStore: () => ({ changeMagazineState: vi.fn() }),
}));

import MagazineStateModal from '@/components/magazines/MagazineStateModal.vue';

const magazine = {
  id: 12,
  id_marking: 'GL9-01',
  capacity: 17,
  loaded_rounds: 0,
  calibers: [{ id: 9, label: '9mm' }],
  firearms: [],
};

describe('MagazineStateModal', () => {
  beforeEach(() => {
    fetchMagazine.mockResolvedValue({ data: magazine });
    fetchAmmunition.mockResolvedValue({
      data: [
        { id: 1, manufacturer: 'Federal', label: 'HST', caliber_id: 9 },
        { id: 2, manufacturer: 'Federal', label: 'HST', caliber_id: 45 },
      ],
    });
    fetchLocations.mockResolvedValue({ data: [] });
  });

  it('makes compatible ammunition available for an empty magazine', async () => {
    const wrapper = mount(MagazineStateModal, { props: { magazine } });
    await flushPromises();

    const ammunitionSelect = wrapper.get('#magazine-ammunition');

    expect(ammunitionSelect.attributes('disabled')).toBeUndefined();
    expect(ammunitionSelect.text()).toContain('Federal HST');
    expect(ammunitionSelect.findAll('option')).toHaveLength(2);
  });
});
