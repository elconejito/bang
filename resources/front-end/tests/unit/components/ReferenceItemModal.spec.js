import { describe, expect, it, vi, beforeEach } from 'vitest';
import { mount, flushPromises } from '@vue/test-utils';

const caliberCreate = vi.fn();
const caliberUpdate = vi.fn();
const caliberRemove = vi.fn();
const purposeCreate = vi.fn();
const colorCreate = vi.fn();
const locationFetchAll = vi.fn();
const locationCreate = vi.fn();
const locationRemove = vi.fn();
const storeCreate = vi.fn();
const rangeCreate = vi.fn();
const rangeDestroy = vi.fn();

vi.mock('@/stores/calibers', () => ({
  useCalibersStore: () => ({ create: caliberCreate, update: caliberUpdate, remove: caliberRemove }),
}));

vi.mock('@/stores/purposes', () => ({
  usePurposesStore: () => ({ create: purposeCreate, update: vi.fn(), remove: vi.fn() }),
}));

vi.mock('@/stores/colors', () => ({
  useColorsStore: () => ({ create: colorCreate, update: vi.fn(), remove: vi.fn() }),
}));

vi.mock('@/stores/locations', () => ({
  useLocationsStore: () => ({
    fetchAll: locationFetchAll,
    create: locationCreate,
    update: vi.fn(),
    remove: locationRemove,
  }),
}));

vi.mock('@/stores/gunStores', () => ({
  useGunStoresStore: () => ({ create: storeCreate, update: vi.fn(), remove: vi.fn() }),
}));

vi.mock('@/stores/ranges', () => ({
  useRangesStore: () => ({ create: rangeCreate, update: vi.fn(), destroy: rangeDestroy }),
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

describe('ReferenceItemModal — color', () => {
  beforeEach(() => {
    colorCreate.mockReset();
  });

  it('requires and sends the full and short labels', async () => {
    colorCreate.mockResolvedValue({
      data: { id: 10, label: 'Flat Dark Earth', short_label: 'FDE' },
    });
    const wrapper = mountModal({ type: 'color', mode: 'add' });
    await flushPromises();

    await wrapper.find('#ref-label').setValue('Flat Dark Earth');
    expect(findButton(wrapper, 'Add color').attributes('disabled')).toBeDefined();

    await wrapper.find('#ref-short-label').setValue('FDE');
    await findButton(wrapper, 'Add color').trigger('click');
    await flushPromises();

    expect(colorCreate).toHaveBeenCalledWith({
      label: 'Flat Dark Earth',
      short_label: 'FDE',
    });
  });
});

describe('ReferenceItemModal — facility lists', () => {
  beforeEach(() => {
    locationCreate.mockReset();
    locationFetchAll.mockReset();
    locationFetchAll.mockResolvedValue({ data: [] });
    locationRemove.mockReset();
    storeCreate.mockReset();
    rangeCreate.mockReset();
    rangeDestroy.mockReset();
  });

  it('creates a storage location from just a label', async () => {
    locationCreate.mockResolvedValue({ data: { id: 3, label: 'Bedroom Safe' } });
    const wrapper = mountModal({ type: 'location', mode: 'add' });
    await flushPromises();

    // Label-only — no caliber-specific fields.
    expect(wrapper.find('#ref-official').exists()).toBe(false);
    expect(wrapper.find('#ref-type').exists()).toBe(false);
    expect(findButton(wrapper, 'Add location').attributes('disabled')).toBeDefined();

    await wrapper.find('#ref-label').setValue('Bedroom Safe');
    await findButton(wrapper, 'Add location').trigger('click');
    await flushPromises();

    expect(locationCreate).toHaveBeenCalledWith({
      label: 'Bedroom Safe',
      parent_location_id: null,
    });
    expect(wrapper.emitted('saved')[0]).toEqual([{ id: 3, label: 'Bedroom Safe' }]);
  });

  it('creates a sublocation under an existing location', async () => {
    locationFetchAll.mockResolvedValue({
      data: [{ id: 4, label: 'Gun Safe', full_label: 'Gun Safe', parent_location_id: null }],
    });
    locationCreate.mockResolvedValue({
      data: {
        id: 5,
        label: 'Top Shelf',
        full_label: 'Gun Safe › Top Shelf',
        parent_location_id: 4,
      },
    });
    const wrapper = mountModal({ type: 'location', mode: 'add' });
    await flushPromises();

    await wrapper.find('#ref-label').setValue('Top Shelf');
    await wrapper.find('#ref-parent-location').setValue('4');
    await findButton(wrapper, 'Add location').trigger('click');
    await flushPromises();

    expect(locationCreate).toHaveBeenCalledWith({
      label: 'Top Shelf',
      parent_location_id: 4,
    });
  });

  it('creates a store from just a label', async () => {
    storeCreate.mockResolvedValue({ data: { id: 4, label: 'Bass Pro Shop' } });
    const wrapper = mountModal({ type: 'store', mode: 'add' });
    await flushPromises();

    await wrapper.find('#ref-label').setValue('Bass Pro Shop');
    await findButton(wrapper, 'Add store').trigger('click');
    await flushPromises();

    expect(storeCreate).toHaveBeenCalledWith({ label: 'Bass Pro Shop' });
  });

  it('deletes an unused range through the store destroy method', async () => {
    const wrapper = mountModal({
      type: 'range',
      mode: 'edit',
      item: { id: 7, label: 'Public BLM Land', sessions_count: 0 },
    });
    await flushPromises();

    await findButton(wrapper, 'Delete').trigger('click');
    await flushPromises();

    expect(rangeDestroy).toHaveBeenCalledWith(7);
    expect(wrapper.emitted('deleted')[0]).toEqual([7]);
  });

  it('blocks range deletion and shows the in-use note when sessions exist', () => {
    const wrapper = mountModal({
      type: 'range',
      mode: 'edit',
      item: { id: 8, label: 'Eagle Point Range', sessions_count: 12 },
    });

    expect(wrapper.text()).toContain('Used by 12 sessions');
    expect(wrapper.text()).toContain('In use');
    expect(findButton(wrapper, 'Delete')).toBeUndefined();
  });

  it('derives location usage from the contents arrays', () => {
    const wrapper = mountModal({
      type: 'location',
      mode: 'edit',
      item: {
        id: 9,
        label: 'Range Bag',
        children_count: 1,
        contents: { firearms: [{ id: 1 }], optics: [{ id: 2 }], lights: [], suppressors: [] },
      },
    });

    expect(wrapper.text()).toContain('Used by 3 items');
    expect(findButton(wrapper, 'Delete')).toBeUndefined();
  });
});
