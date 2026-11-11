import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const fetchAll = vi.fn();

vi.mock('@/stores/ammunition', () => ({
  useAmmunitionStore: () => ({ fetchAll }),
}));

vi.mock('vue-router', () => ({
  useRoute: () => ({ query: {} }),
  useRouter: () => ({ replace: vi.fn() }),
}));

import AmmoIndex from '@/pages/ammunition/AmmoIndex.vue';

const ammo = [
  {
    id: 1,
    manufacturer: 'Federal',
    label: 'American Eagle',
    on_hand: 100,
    reorder_min: null,
    caliber: { id: 1, label: '9mm' },
  },
  {
    id: 2,
    manufacturer: 'Blazer',
    label: 'Brass',
    on_hand: 200,
    reorder_min: null,
    caliber: { id: 1, label: '9mm' },
  },
  {
    id: 3,
    manufacturer: 'Federal',
    label: 'HST',
    on_hand: 300,
    reorder_min: null,
    caliber: { id: 1, label: '9mm' },
  },
  {
    id: 4,
    manufacturer: 'Federal',
    label: 'Premium Personal Defense',
    on_hand: 400,
    reorder_min: null,
    caliber: { id: 1, label: '9mm' },
  },
];

function findExactButton(wrapper, text) {
  return wrapper.findAll('button').find((button) => button.text().trim() === text);
}

async function mountIndex() {
  fetchAll.mockResolvedValue({ data: ammo });

  const wrapper = mount(AmmoIndex, {
    global: {
      stubs: {
        'router-link': true,
        AmmoCard: {
          name: 'AmmoCard',
          props: ['ammo'],
          template: '<div data-testid="ammo-card" />',
        },
        AmmoTable: {
          name: 'AmmoTable',
          props: ['groups'],
          template: '<div data-testid="ammo-table" />',
        },
        AddStockModal: true,
      },
    },
  });
  await flushPromises();

  return wrapper;
}

describe('AmmoIndex sorting', () => {
  beforeEach(() => {
    fetchAll.mockReset();
    localStorage.clear();
  });

  it('offers only On hand and Name sort options', async () => {
    const wrapper = await mountIndex();

    await findExactButton(wrapper, 'On hand').trigger('click');

    expect(findExactButton(wrapper, 'On hand')).toBeTruthy();
    expect(findExactButton(wrapper, 'Name')).toBeTruthy();
    expect(findExactButton(wrapper, 'Manufacturer')).toBeUndefined();
    expect(findExactButton(wrapper, 'Load name')).toBeUndefined();
  });

  it('sorts names by manufacturer and then label', async () => {
    const wrapper = await mountIndex();

    await findExactButton(wrapper, 'On hand').trigger('click');
    await findExactButton(wrapper, 'Name').trigger('click');

    expect(
      wrapper.findAllComponents({ name: 'AmmoCard' }).map((card) => card.props('ammo').id)
    ).toEqual([2, 1, 3, 4]);
  });

  it('restores descending on-hand order after sorting by name', async () => {
    const wrapper = await mountIndex();

    await findExactButton(wrapper, 'On hand').trigger('click');
    await findExactButton(wrapper, 'Name').trigger('click');
    await findExactButton(wrapper, 'Name').trigger('click');
    await findExactButton(wrapper, 'On hand').trigger('click');

    expect(
      wrapper.findAllComponents({ name: 'AmmoCard' }).map((card) => card.props('ammo').id)
    ).toEqual([4, 3, 2, 1]);
  });

  it('passes name-sorted groups to the table view', async () => {
    const wrapper = await mountIndex();

    await findExactButton(wrapper, 'On hand').trigger('click');
    await findExactButton(wrapper, 'Name').trigger('click');
    await findExactButton(wrapper, 'Table').trigger('click');

    expect(
      wrapper
        .getComponent({ name: 'AmmoTable' })
        .props('groups')[0]
        .items.map((item) => item.id)
    ).toEqual([2, 1, 3, 4]);
  });
});
