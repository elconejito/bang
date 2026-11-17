import { describe, expect, it, vi, beforeEach } from 'vitest';
import { mount, flushPromises } from '@vue/test-utils';

const shotgunStores = vi.hoisted(() =>
  Object.fromEntries(
    ['shellType', 'shotMaterial'].map((type) => [
      type,
      { create: vi.fn(), update: vi.fn(), remove: vi.fn() },
    ])
  )
);
vi.mock('@/stores/shellTypes', () => ({ useShellTypesStore: () => shotgunStores.shellType }));
vi.mock('@/stores/shotMaterials', () => ({
  useShotMaterialsStore: () => shotgunStores.shotMaterial,
}));

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
const bulletTypeCreate = vi.fn();
const bulletTypeUpdate = vi.fn();
const bulletTypeRemove = vi.fn();

vi.mock('@/stores/calibers', () => ({
  useCalibersStore: () => ({ create: caliberCreate, update: caliberUpdate, remove: caliberRemove }),
}));

vi.mock('@/stores/purposes', () => ({
  usePurposesStore: () => ({ create: purposeCreate, update: vi.fn(), remove: vi.fn() }),
}));

vi.mock('@/stores/bulletTypes', () => ({
  useBulletTypesStore: () => ({
    create: bulletTypeCreate,
    update: bulletTypeUpdate,
    remove: bulletTypeRemove,
  }),
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

describe('ReferenceItemModal — bullet type', () => {
  beforeEach(() => {
    bulletTypeCreate.mockReset();
    bulletTypeUpdate.mockReset();
    bulletTypeRemove.mockReset();
  });

  it('requires and sends a label and abbreviation', async () => {
    bulletTypeCreate.mockResolvedValue({
      data: { id: 12, label: 'Full Metal Jacket', abbreviation: 'FMJ' },
    });
    const wrapper = mountModal({ type: 'bulletType', mode: 'add' });
    await flushPromises();

    await wrapper.find('#ref-label').setValue('Full Metal Jacket');
    expect(findButton(wrapper, 'Add bullet type').attributes('disabled')).toBeDefined();

    await wrapper.find('#ref-abbreviation').setValue('FMJ');
    await findButton(wrapper, 'Add bullet type').trigger('click');
    await flushPromises();

    expect(bulletTypeCreate).toHaveBeenCalledWith({
      label: 'Full Metal Jacket',
      abbreviation: 'FMJ',
    });
    expect(wrapper.emitted('saved')[0]).toEqual([
      { id: 12, label: 'Full Metal Jacket', abbreviation: 'FMJ' },
    ]);
  });

  it('prepopulates and updates the label and abbreviation', async () => {
    bulletTypeUpdate.mockResolvedValue({
      data: { id: 12, label: 'Jacketed Hollow Point', abbreviation: 'JHP' },
    });
    const wrapper = mountModal({
      type: 'bulletType',
      mode: 'edit',
      item: { id: 12, label: 'Full Metal Jacket', abbreviation: 'FMJ', loads_count: 0 },
    });
    await flushPromises();

    expect(wrapper.find('#ref-label').element.value).toBe('Full Metal Jacket');
    expect(wrapper.find('#ref-abbreviation').element.value).toBe('FMJ');

    await wrapper.find('#ref-label').setValue('Jacketed Hollow Point');
    await wrapper.find('#ref-abbreviation').setValue('JHP');
    await findButton(wrapper, 'Save changes').trigger('click');
    await flushPromises();

    expect(bulletTypeUpdate).toHaveBeenCalledWith(12, {
      label: 'Jacketed Hollow Point',
      abbreviation: 'JHP',
    });
    expect(wrapper.emitted('saved')[0]).toEqual([
      { id: 12, label: 'Jacketed Hollow Point', abbreviation: 'JHP' },
    ]);
  });

  it('blocks deletion when a bullet type is in use', () => {
    const wrapper = mountModal({
      type: 'bulletType',
      mode: 'edit',
      item: { id: 12, label: 'Full Metal Jacket', abbreviation: 'FMJ', loads_count: 2 },
    });

    expect(wrapper.text()).toContain('Used by 2 loads');
    expect(findButton(wrapper, 'Delete')).toBeUndefined();
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
      description: null,
    });
    expect(wrapper.emitted('saved')[0]).toEqual([{ id: 3, label: 'Bedroom Safe' }]);
  });

  it('waits for the parent location list before exposing editable location fields', async () => {
    let resolveLocations;
    locationFetchAll.mockReturnValue(
      new Promise((resolve) => {
        resolveLocations = resolve;
      })
    );

    const wrapper = mountModal({ type: 'location', mode: 'add' });

    expect(wrapper.find('#ref-label').exists()).toBe(false);
    expect(findButton(wrapper, 'Add location').attributes('disabled')).toBeDefined();

    resolveLocations({ data: [{ id: 4, label: 'Gun Safe', parent_location_id: null }] });
    await flushPromises();

    expect(wrapper.find('#ref-label').exists()).toBe(true);
    expect(wrapper.find('#ref-parent-location').text()).toContain('Gun Safe');
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
    await wrapper.find('#ref-location-description').setValue('Upper shelf for magazines');
    await wrapper.find('#ref-parent-location').setValue('4');
    await findButton(wrapper, 'Add location').trigger('click');
    await flushPromises();

    expect(locationCreate).toHaveBeenCalledWith({
      label: 'Top Shelf',
      parent_location_id: 4,
      description: 'Upper shelf for magazines',
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

describe.each([
  ['shellType', 'shell type'],
  ['shotMaterial', 'shot material'],
])('ReferenceItemModal — %s', (type, singular) => {
  beforeEach(() => {
    Object.values(shotgunStores[type]).forEach((method) => method.mockReset());
  });

  it('requires a label and creates an entry', async () => {
    shotgunStores[type].create.mockResolvedValue({ data: { id: 15, label: 'Custom option' } });
    const wrapper = mountModal({ type, mode: 'add' });
    await flushPromises();
    expect(findButton(wrapper, `Add ${singular}`).attributes('disabled')).toBeDefined();
    await wrapper.get('#ref-label').setValue(' Custom option ');
    await findButton(wrapper, `Add ${singular}`).trigger('click');
    await flushPromises();
    expect(shotgunStores[type].create).toHaveBeenCalledWith({ label: 'Custom option' });
    expect(wrapper.emitted('saved')[0]).toEqual([{ id: 15, label: 'Custom option' }]);
  });

  it('prepopulates and updates a used entry while blocking deletion', async () => {
    shotgunStores[type].update.mockResolvedValue({ data: { id: 15, label: 'Renamed option' } });
    const wrapper = mountModal({
      type,
      mode: 'edit',
      item: { id: 15, label: 'Custom option', loads_count: 2 },
    });
    await flushPromises();
    expect(wrapper.get('#ref-label').element.value).toBe('Custom option');
    expect(wrapper.text()).toContain('Used by 2 loads');
    expect(findButton(wrapper, 'Delete')).toBeUndefined();
    await wrapper.get('#ref-label').setValue('Renamed option');
    await findButton(wrapper, 'Save changes').trigger('click');
    await flushPromises();
    expect(shotgunStores[type].update).toHaveBeenCalledWith(15, { label: 'Renamed option' });
  });

  it('deletes an unused entry', async () => {
    const wrapper = mountModal({
      type,
      mode: 'edit',
      item: { id: 15, label: 'Custom option', loads_count: 0 },
    });
    await flushPromises();
    await findButton(wrapper, 'Delete').trigger('click');
    await flushPromises();
    expect(shotgunStores[type].remove).toHaveBeenCalledWith(15);
    expect(wrapper.emitted('deleted')[0]).toEqual([15]);
  });

  it('keeps the editor open when saving fails', async () => {
    shotgunStores[type].create.mockRejectedValue(new Error('Unable to save'));
    const wrapper = mountModal({ type, mode: 'add' });
    await flushPromises();
    await wrapper.get('#ref-label').setValue('Custom option');
    await findButton(wrapper, `Add ${singular}`).trigger('click');
    await flushPromises();
    expect(wrapper.emitted('saved')).toBeUndefined();
    expect(wrapper.get('#ref-label').element.value).toBe('Custom option');
    expect(findButton(wrapper, `Add ${singular}`).attributes('disabled')).toBeUndefined();
  });
});
