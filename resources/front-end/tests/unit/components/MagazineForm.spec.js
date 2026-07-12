import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const updateMagazine = vi.fn();

vi.mock('@/stores/calibers', () => ({
  useCalibersStore: () => ({ fetchAll: vi.fn().mockResolvedValue({ data: [] }) }),
}));

vi.mock('@/stores/firearms', () => ({
  useFirearmsStore: () => ({ fetchAll: vi.fn().mockResolvedValue({ data: [] }) }),
}));

vi.mock('@/stores/magazines', () => ({
  useMagazinesStore: () => ({ create: vi.fn(), update: updateMagazine }),
}));

import MagazineForm from '@/components/magazines/MagazineForm.vue';

const magazine = {
  id: 12,
  manufacturer: 'Magpul',
  model_name: 'PMAG GL9',
  capacity: 17,
  status: 'loaded',
  loaded_ammunition_id: 5,
  calibers: [],
  firearms: [],
};

describe('MagazineForm', () => {
  beforeEach(() => {
    updateMagazine.mockReset();
    updateMagazine.mockResolvedValue({ data: magazine });
  });

  it('edits specifications without exposing or submitting magazine state', async () => {
    const wrapper = mount(MagazineForm, { props: { item: magazine } });
    await flushPromises();

    expect(wrapper.text()).not.toContain('Status');
    expect(wrapper.text()).not.toContain('Loaded with');

    const saveButton = wrapper
      .findAll('button')
      .find((button) => button.text().includes('Save changes'));
    await saveButton.trigger('click');
    await flushPromises();

    expect(updateMagazine).toHaveBeenCalledWith(
      magazine.id,
      expect.not.objectContaining({
        status: expect.anything(),
        loaded_ammunition_id: expect.anything(),
      })
    );
  });
});
