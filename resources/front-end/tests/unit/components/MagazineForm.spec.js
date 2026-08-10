import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const stores = vi.hoisted(() => ({
  fetchCalibers: vi.fn(),
  fetchFirearms: vi.fn(),
  updateMagazine: vi.fn(),
}));

vi.mock('@/stores/calibers', () => ({
  useCalibersStore: () => ({ fetchAll: stores.fetchCalibers }),
}));

vi.mock('@/stores/firearms', () => ({
  useFirearmsStore: () => ({ fetchAll: stores.fetchFirearms }),
}));

vi.mock('@/stores/colors', () => ({
  useColorsStore: () => ({ fetchAll: vi.fn().mockResolvedValue({ data: [] }) }),
}));

vi.mock('@/stores/magazines', () => ({
  useMagazinesStore: () => ({ create: vi.fn(), update: stores.updateMagazine }),
}));

import MagazineForm from '@/components/magazines/MagazineForm.vue';

const magazine = {
  id: 12,
  manufacturer: 'Magpul',
  model_name: 'PMAG GL9',
  model_number: 'MAG-123',
  capacity: 17,
  status: 'loaded',
  loaded_ammunition_id: 5,
  calibers: [],
  firearms: [],
};

describe('MagazineForm', () => {
  beforeEach(() => {
    stores.fetchCalibers.mockResolvedValue({ data: [] });
    stores.fetchFirearms.mockResolvedValue({ data: [] });
    stores.updateMagazine.mockReset();
    stores.updateMagazine.mockResolvedValue({ data: magazine });
  });

  it('edits specifications without exposing or submitting magazine state', async () => {
    const wrapper = mount(MagazineForm, { props: { item: magazine } });
    await flushPromises();

    expect(wrapper.text()).not.toContain('Status');
    expect(wrapper.text()).not.toContain('Loaded with');
    expect(wrapper.get('[data-testid="magazine-form-panel"]').classes()).toContain('bg-white');

    for (const placeholder of [
      'Custom display name',
      'e.g. GL9-01',
      'Manufacturer serial number',
    ]) {
      expect(
        wrapper.get(`input[placeholder="${placeholder}"]`).element.closest('label')
          .firstElementChild.classList
      ).toContain('inline-flex');
    }

    expect(wrapper.get('button[type="submit"]').text()).toContain('Save changes');
    await wrapper.get('form').trigger('submit');
    await flushPromises();

    expect(stores.updateMagazine).toHaveBeenCalledWith(
      magazine.id,
      expect.objectContaining({ model_number: 'MAG-123' })
    );
    expect(stores.updateMagazine.mock.calls[0][1]).not.toHaveProperty('status');
    expect(stores.updateMagazine.mock.calls[0][1]).not.toHaveProperty('loaded_ammunition_id');
  });

  it('shows only firearms that use a selected caliber and allows a taller list', async () => {
    stores.fetchCalibers.mockResolvedValue({
      data: [
        { id: 9, label: '9mm' },
        { id: 45, label: '.45 ACP' },
      ],
    });
    stores.fetchFirearms.mockResolvedValue({
      data: [
        { id: 1, manufacturer: 'Glock', label: '19', calibers: [{ id: 9 }] },
        { id: 2, manufacturer: 'Colt', label: '1911', calibers: [{ id: 45 }] },
      ],
    });

    const wrapper = mount(MagazineForm);
    await flushPromises();

    await wrapper.get('input[type="checkbox"][value="9"]').setValue(true);

    const firearmList = wrapper.get('fieldset:last-of-type > div');
    expect(firearmList.classes()).toContain('max-h-80');
    expect(firearmList.text()).toContain('Glock 19');
    expect(firearmList.text()).not.toContain('Colt 1911');
  });
});
