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
        {
          id: 1,
          manufacturer: 'Federal',
          label: 'HST',
          weight: 124,
          caliber_id: 9,
          caliber: { label: '9mm' },
        },
        {
          id: 2,
          manufacturer: 'Federal',
          label: 'HST',
          weight: 230,
          caliber_id: 45,
          caliber: { label: '.45 ACP' },
        },
      ],
    });
    fetchLocations.mockResolvedValue({ data: [] });
  });

  it('makes compatible ammunition available for an empty magazine', async () => {
    const wrapper = mount(MagazineStateModal, { props: { magazine } });
    await flushPromises();

    const ammunitionSelect = wrapper.get('#magazine-ammunition');

    expect(ammunitionSelect.attributes('disabled')).toBeUndefined();
    expect(ammunitionSelect.text()).toContain('9mm · Federal · HST · 124 gr');
    expect(ammunitionSelect.findAll('option')).toHaveLength(2);
    expect(wrapper.get('.modal-scrim').exists()).toBe(true);
    expect(wrapper.get('.modal-shell').exists()).toBe(true);
  });

  it('fills an empty magazine when ammunition is selected and preserves an edited quantity', async () => {
    const wrapper = mount(MagazineStateModal, { props: { magazine } });
    await flushPromises();

    await wrapper.get('#magazine-ammunition').setValue('1');
    expect(wrapper.get('#magazine-rounds').element.value).toBe('17');

    await wrapper.get('#magazine-rounds').setValue('');
    await wrapper.get('#magazine-rounds').setValue(9);
    expect(wrapper.get('#magazine-ammunition').element.value).toBe('1');
    await wrapper.get('#magazine-ammunition').setValue('');
    await wrapper.get('#magazine-ammunition').setValue('1');

    expect(wrapper.get('#magazine-rounds').element.value).toBe('9');
    await wrapper.get('#magazine-rounds').setValue(0);
    expect(wrapper.get('#magazine-ammunition').element.value).toBe('');
  });

  it('opens an existing partial magazine with its recorded quantity', async () => {
    fetchMagazine.mockResolvedValue({
      data: { ...magazine, loaded_rounds: 4, loaded_ammunition_id: 1 },
    });
    const wrapper = mount(MagazineStateModal, { props: { magazine } });
    await flushPromises();

    expect(wrapper.get('#magazine-rounds').element.value).toBe('4');
  });
});
