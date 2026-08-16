import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';
import MagazineBulkEditModal from '@/components/magazines/MagazineBulkEditModal.vue';

const stores = vi.hoisted(() => ({
  fetchCalibers: vi.fn(),
  fetchColors: vi.fn(),
  fetchFirearms: vi.fn(),
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
  {
    id: 12,
    capacity: 17,
    current_firearm: { id: 4, manufacturer: 'Glock', label: '19' },
  },
  { id: 13, capacity: 19, current_firearm: null },
];

function mountModal(overrides = {}) {
  return mount(MagazineBulkEditModal, {
    props: {
      magazines,
      group: { manufacturer: 'Glock', model_name: 'OEM', capacity: 17, calibers: [] },
      ...overrides,
    },
  });
}

describe('MagazineBulkEditModal', () => {
  beforeEach(() => {
    stores.fetchCalibers.mockResolvedValue({ data: [{ id: 2, label: '9mm' }] });
    stores.fetchColors.mockResolvedValue({ data: [{ id: 6, label: 'Blue' }] });
    stores.fetchFirearms.mockResolvedValue({
      data: [{ id: 4, manufacturer: 'Glock', label: '19' }],
    });
  });

  it('omits unchecked fields and maps nullable edit fields', async () => {
    const wrapper = mountModal();
    await flushPromises();

    await wrapper.get('#bulk-magazine-label-apply').setValue(true);
    await wrapper.get('#bulk-magazine-color-apply').setValue(true);
    await wrapper.get('[data-testid="bulk-magazine-submit"]').trigger('click');

    expect(wrapper.emitted('save')).toEqual([[{ label: null, color_id: null }]]);
    expect(wrapper.emitted('save')[0][0]).not.toHaveProperty('manufacturer');
    expect(wrapper.emitted('save')[0][0]).not.toHaveProperty('capacity');
    expect(wrapper.emitted('save')[0][0]).not.toHaveProperty('location_id');
    expect(wrapper.emitted('save')[0][0]).not.toHaveProperty('loaded_rounds');
  });

  it('can apply serial number and ID marking from the standard edit form', async () => {
    const wrapper = mountModal();
    await flushPromises();

    await wrapper.get('#bulk-magazine-serial_number-apply').setValue(true);
    await wrapper.get('#bulk-magazine-serial_number').setValue('SERIAL-001');
    await wrapper.get('#bulk-magazine-id_marking-apply').setValue(true);
    await wrapper.get('#bulk-magazine-id_marking').setValue('MAG-001');
    await wrapper.get('[data-testid="bulk-magazine-submit"]').trigger('click');

    expect(wrapper.emitted('save')).toEqual([
      [{ serial_number: 'SERIAL-001', id_marking: 'MAG-001' }],
    ]);
  });

  it('treats unchanged group calibers as a no-op', async () => {
    const wrapper = mountModal({
      group: {
        manufacturer: 'Glock',
        model_name: 'OEM',
        capacity: 17,
        calibers: [{ id: 2, label: '9mm' }],
      },
    });
    await flushPromises();

    await wrapper.get('#bulk-magazine-calibers-apply').setValue(true);

    expect(wrapper.text()).toContain('NO CHANGE');
    expect(
      wrapper.get('[data-testid="bulk-magazine-submit"]').attributes('disabled')
    ).toBeDefined();
  });

  it('allows a case-only nickname change that the API will persist', async () => {
    const wrapper = mountModal({
      magazines: [{ ...magazines[0], label: 'Duty' }],
    });
    await flushPromises();

    await wrapper.get('#bulk-magazine-label-apply').setValue(true);
    await wrapper.get('#bulk-magazine-label').setValue('duty');
    await wrapper.get('[data-testid="bulk-magazine-submit"]').trigger('click');

    expect(wrapper.emitted('save')).toEqual([[{ label: 'duty' }]]);
  });

  it('can apply a model number to the selected magazines', async () => {
    const wrapper = mountModal();
    await flushPromises();

    await wrapper.get('#bulk-magazine-model_number-apply').setValue(true);
    await wrapper.get('#bulk-magazine-model_number').setValue('MAG-123');
    await wrapper.get('[data-testid="bulk-magazine-submit"]').trigger('click');

    expect(wrapper.emitted('save')).toEqual([[{ model_number: 'MAG-123' }]]);
  });
});
