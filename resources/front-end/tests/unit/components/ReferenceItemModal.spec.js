import { describe, expect, it, vi, beforeEach } from 'vitest';
import { mount, flushPromises } from '@vue/test-utils';

const caliberCreate = vi.fn();
const caliberUpdate = vi.fn();
const caliberRemove = vi.fn();
const purposeCreate = vi.fn();

vi.mock('@/stores/calibers', () => ({
  useCalibersStore: () => ({ create: caliberCreate, update: caliberUpdate, remove: caliberRemove }),
}));

vi.mock('@/stores/purposes', () => ({
  usePurposesStore: () => ({ create: purposeCreate, update: vi.fn(), remove: vi.fn() }),
}));

vi.mock('@/stores/reference', () => ({
  useReferenceStore: () => ({
    caliberType: [
      { id: 1, label: 'Centerfire' },
      { id: 2, label: 'Rimfire' },
    ],
  }),
}));

import ReferenceItemModal from '@/components/reference/ReferenceItemModal.vue';

function mountModal(props) {
  return mount(ReferenceItemModal, {
    props,
    global: { stubs: { teleport: true, FormError: true } },
  });
}

function findButton(wrapper, text) {
  return wrapper.findAll('button').find((b) => b.text().includes(text));
}

describe('ReferenceItemModal — caliber', () => {
  beforeEach(() => {
    caliberCreate.mockReset();
    caliberUpdate.mockReset();
    caliberRemove.mockReset();
  });

  it('gates save until a label is entered', async () => {
    const wrapper = mountModal({ type: 'caliber', mode: 'add' });
    await flushPromises();

    expect(findButton(wrapper, 'Add caliber').attributes('disabled')).toBeDefined();

    await wrapper.find('#ref-label').setValue('9mm');
    await flushPromises();

    expect(findButton(wrapper, 'Add caliber').attributes('disabled')).toBeUndefined();
  });

  it('falls back to the label as the official name when none is given', async () => {
    caliberCreate.mockResolvedValue({ data: { id: 5, label: '9mm' } });
    const wrapper = mountModal({ type: 'caliber', mode: 'add' });
    await flushPromises();

    await wrapper.find('#ref-label').setValue('9mm');
    await findButton(wrapper, 'Add caliber').trigger('click');
    await flushPromises();

    expect(caliberCreate).toHaveBeenCalledWith({
      label: '9mm',
      caliber: '9mm',
      caliber_type_id: 1,
    });
    expect(wrapper.emitted('saved')[0]).toEqual([{ id: 5, label: '9mm' }]);
  });

  it('sends the official name and the default type when provided', async () => {
    caliberCreate.mockResolvedValue({ data: { id: 6 } });
    const wrapper = mountModal({ type: 'caliber', mode: 'add' });
    await flushPromises();

    await wrapper.find('#ref-label').setValue('9mm');
    await wrapper.find('#ref-official').setValue('9×19mm Parabellum');
    await findButton(wrapper, 'Add caliber').trigger('click');
    await flushPromises();

    // The type select defaults to the first available caliber type.
    expect(caliberCreate).toHaveBeenCalledWith({
      label: '9mm',
      caliber: '9×19mm Parabellum',
      caliber_type_id: 1,
    });
  });

  it('blocks deletion and shows the in-use note when the caliber is used', () => {
    const wrapper = mountModal({
      type: 'caliber',
      mode: 'edit',
      item: {
        id: 1,
        label: '9mm',
        caliber: '9×19mm Parabellum',
        caliber_type_id: 1,
        firearms_count: 3,
        loads_count: 6,
      },
    });

    expect(wrapper.text()).toContain('Used by 3 firearms · 6 loads');
    expect(wrapper.text()).toContain('In use');
    expect(findButton(wrapper, 'Delete')).toBeUndefined();
  });

  it('allows deletion when the caliber is unused', async () => {
    const wrapper = mountModal({
      type: 'caliber',
      mode: 'edit',
      item: { id: 2, label: '.45 ACP', caliber: '.45 Auto', firearms_count: 0, loads_count: 0 },
    });

    await findButton(wrapper, 'Delete').trigger('click');
    await flushPromises();

    expect(caliberRemove).toHaveBeenCalledWith(2);
    expect(wrapper.emitted('deleted')[0]).toEqual([2]);
  });
});

describe('ReferenceItemModal — purpose', () => {
  beforeEach(() => {
    purposeCreate.mockReset();
  });

  it('sends only the label for purposes', async () => {
    purposeCreate.mockResolvedValue({ data: { id: 9, label: 'Duty' } });
    const wrapper = mountModal({ type: 'purpose', mode: 'add' });
    await flushPromises();

    // No caliber-specific fields for purposes.
    expect(wrapper.find('#ref-official').exists()).toBe(false);
    expect(wrapper.find('#ref-type').exists()).toBe(false);

    await wrapper.find('#ref-label').setValue('Duty');
    await findButton(wrapper, 'Add purpose').trigger('click');
    await flushPromises();

    expect(purposeCreate).toHaveBeenCalledWith({ label: 'Duty' });
  });
});
