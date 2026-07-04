import { describe, expect, it, vi, beforeEach } from 'vitest';
import { mount, flushPromises } from '@vue/test-utils';

const fetchAll = vi.fn();

vi.mock('@/stores/ammunition', () => ({
  useAmmunitionStore: () => ({ fetchAll }),
}));

vi.mock('vue-router', () => ({
  useRoute: () => ({ query: {} }),
  useRouter: () => ({ replace: vi.fn() }),
}));

import AmmoIndex from '@/pages/ammunition/AmmoIndex.vue';

const inStock = {
  id: 1,
  manufacturer: 'Federal',
  label: 'American Eagle',
  on_hand: 250,
  reorder_min: null,
  caliber: { id: 1, label: '9mm' },
  purpose: { id: 1, label: 'Range' },
};

const zeroStock = {
  id: 2,
  manufacturer: 'Hornady',
  label: 'Critical Defense',
  on_hand: 0,
  reorder_min: null,
  caliber: { id: 1, label: '9mm' },
  purpose: { id: 2, label: 'Defense' },
};

function findToggle(wrapper, text) {
  return wrapper.findAll('button').find((b) => b.text().includes(text));
}

async function mountIndex() {
  // Mirror the controller: the in_stock filter excludes zero-stock loads.
  fetchAll.mockImplementation((params = {}) =>
    Promise.resolve({
      data: params['filter[in_stock]'] ? [inStock] : [inStock, zeroStock],
    })
  );
  const wrapper = mount(AmmoIndex, {
    global: {
      stubs: {
        'router-link': true,
        AmmoCard: true,
        AddStockModal: true,
      },
    },
  });
  await flushPromises();
  return wrapper;
}

const nineA = {
  id: 1,
  manufacturer: 'Federal',
  label: 'American Eagle',
  on_hand: 250,
  reorder_min: null,
  caliber: { id: 1, label: '9mm' },
  purpose: { id: 1, label: 'Range' },
};

const nineB = {
  id: 2,
  manufacturer: 'Blazer',
  label: 'Brass',
  on_hand: 100,
  reorder_min: null,
  caliber: { id: 1, label: '9mm' },
  purpose: { id: 1, label: 'Range' },
};

const fortyFive = {
  id: 3,
  manufacturer: 'Federal',
  label: 'HST',
  on_hand: 50,
  reorder_min: null,
  caliber: { id: 2, label: '.45 ACP' },
  purpose: { id: 2, label: 'Defense' },
};

function findExactButton(wrapper, text) {
  return wrapper.findAll('button').find((b) => b.text().trim() === text);
}

async function mountWithCalibers() {
  fetchAll.mockResolvedValue({ data: [nineA, nineB, fortyFive] });
  const wrapper = mount(AmmoIndex, {
    global: {
      stubs: {
        'router-link': true,
        AmmoCard: true,
        AddStockModal: true,
      },
    },
  });
  await flushPromises();
  return wrapper;
}

describe('AmmoIndex caliber filter', () => {
  beforeEach(() => {
    fetchAll.mockReset();
  });

  it('filters loads across multiple selected calibers', async () => {
    const wrapper = await mountWithCalibers();
    expect(wrapper.findAll('ammo-card-stub')).toHaveLength(3);

    await findExactButton(wrapper, 'Caliber').trigger('click');
    await findExactButton(wrapper, '9mm').trigger('click');
    await flushPromises();
    expect(wrapper.findAll('ammo-card-stub')).toHaveLength(2);

    // Multi-select keeps the prior caliber selected.
    await findExactButton(wrapper, '.45 ACP').trigger('click');
    await flushPromises();
    expect(wrapper.findAll('ammo-card-stub')).toHaveLength(3);
  });

  it('clears the caliber selection with "All calibers"', async () => {
    const wrapper = await mountWithCalibers();

    await findExactButton(wrapper, 'Caliber').trigger('click');
    await findExactButton(wrapper, '.45 ACP').trigger('click');
    await flushPromises();
    expect(wrapper.findAll('ammo-card-stub')).toHaveLength(1);

    await findExactButton(wrapper, 'All calibers').trigger('click');
    await flushPromises();
    expect(wrapper.findAll('ammo-card-stub')).toHaveLength(3);
  });
});

describe('AmmoIndex zero-stock toggle', () => {
  beforeEach(() => {
    fetchAll.mockReset();
  });

  it('requests only in-stock loads by default', async () => {
    const wrapper = await mountIndex();

    expect(fetchAll).toHaveBeenCalledWith({ 'filter[in_stock]': 1 });
    expect(wrapper.findAll('ammo-card-stub')).toHaveLength(1);
    expect(findToggle(wrapper, 'Show zero stock')).toBeTruthy();
  });

  it('refetches without the filter when zero stock is shown', async () => {
    const wrapper = await mountIndex();
    fetchAll.mockClear();

    await findToggle(wrapper, 'Show zero stock').trigger('click');
    await flushPromises();

    expect(fetchAll).toHaveBeenCalledWith({});
    expect(wrapper.findAll('ammo-card-stub')).toHaveLength(2);
    expect(findToggle(wrapper, 'Hide zero stock')).toBeTruthy();
  });
});
