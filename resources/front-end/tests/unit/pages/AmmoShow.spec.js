import { describe, expect, it, vi, beforeEach } from 'vitest';
import { mount, flushPromises, RouterLinkStub } from '@vue/test-utils';

const fetchOne = vi.fn();
const fetchForAmmo = vi.fn();

vi.mock('@/stores/ammunition', () => ({
  useAmmunitionStore: () => ({ fetchOne }),
}));

vi.mock('@/stores/inventories', () => ({
  useInventoriesStore: () => ({ fetchForAmmo }),
}));

// Charts require a canvas context that happy-dom doesn't provide.
vi.mock('vue-chartjs', () => ({ Bar: { template: '<div />' } }));

import AmmoShow from '@/pages/ammunition/AmmoShow.vue';

const ammo = {
  id: 1,
  label: 'American Eagle',
  manufacturer: 'Federal',
  on_hand: 850,
  caliber: { id: 1, label: '9mm' },
  purpose: { id: 1, label: 'Range' },
  used_by_firearms: [],
  pictures_count: 0,
  thumbnail_urls: [],
};

const ledger = [
  {
    id: 1,
    order_id: 12,
    inventory_date: '2026-05-20',
    type: 'BUY',
    rounds: 500,
    balance: 850,
    cost: 150,
    store_label: 'Cabela',
  },
  { id: 2, inventory_date: '2026-05-12', type: 'FIRED', rounds: -150, balance: 350, cost: 0 },
];

function findButton(wrapper, text) {
  return wrapper.findAll('button').find((b) => b.text().includes(text));
}

// Simulate the server: apply the type filter and sort the way the API does.
function serverRespond(_id, params = {}) {
  // The charts/cost stats request the full history (per_page 200) — never filtered.
  if (params.per_page === 200) {
    return Promise.resolve({ data: ledger, meta: { total: ledger.length, last_page: 1 } });
  }
  let rows = [...ledger];
  if (params['filter[type]']) {
    rows = rows.filter((r) => r.type === params['filter[type]']);
  }
  const asc = params.sort === 'inventory_date,rounds';
  rows.sort((a, b) =>
    asc
      ? a.inventory_date.localeCompare(b.inventory_date)
      : b.inventory_date.localeCompare(a.inventory_date)
  );
  return Promise.resolve({ data: rows, meta: { total: rows.length, last_page: 1 } });
}

async function mountShow() {
  fetchOne.mockResolvedValue({ data: ammo });
  fetchForAmmo.mockImplementation(serverRespond);
  const wrapper = mount(AmmoShow, {
    props: { ammunitionId: 1 },
    global: {
      stubs: {
        'router-link': RouterLinkStub,
        AddStockModal: true,
        EditInventoryModal: true,
        AppBreadcrumb: true,
        NotesPanel: true,
      },
    },
  });
  await flushPromises();
  return wrapper;
}

describe('AmmoShow inventory & usage controls', () => {
  beforeEach(() => {
    fetchOne.mockReset();
    fetchForAmmo.mockReset();
  });

  it('renders the type filter and sort buttons matching the design', async () => {
    const wrapper = await mountShow();

    // Bordered dropdown trigger defaults to "All", sort button defaults to "Newest".
    expect(findButton(wrapper, 'All')).toBeTruthy();
    expect(findButton(wrapper, 'Newest')).toBeTruthy();
    // The old segmented pills are gone.
    expect(findButton(wrapper, 'ADJUST')).toBeFalsy();
  });

  it('keeps notes in the left rail and inventory in the right column', async () => {
    const wrapper = await mountShow();
    const detailGrid = wrapper
      .findAll('div')
      .find((element) => element.classes().includes('grid-cols-[344px_1fr]'));

    expect(detailGrid).toBeTruthy();
    expect(detailGrid.element.children).toHaveLength(2);
    expect(detailGrid.element.children[0].querySelector('notes-panel-stub')).not.toBeNull();
    expect(detailGrid.element.children[1].textContent).toContain('Inventory & usage');
  });

  it('links purchase activity to its order', async () => {
    const wrapper = await mountShow();
    const purchaseLink = wrapper
      .findAllComponents(RouterLinkStub)
      .find((link) => link.text().includes('Purchase · Cabela'));

    expect(purchaseLink).toBeTruthy();
    expect(purchaseLink.props('to')).toEqual({ name: 'OrderShow', params: { order_id: 12 } });
  });

  it('triggers a fresh filtered API call when a type is chosen', async () => {
    const wrapper = await mountShow();

    expect(wrapper.text()).toContain('Cabela');
    expect(wrapper.text()).toContain('Range session');
    fetchForAmmo.mockClear();

    await findButton(wrapper, 'All').trigger('click');
    await findButton(wrapper, 'Fired').trigger('click');
    await flushPromises();

    // Fresh API call, server-side filter, page reset to 1.
    expect(fetchForAmmo).toHaveBeenCalledWith(
      1,
      expect.objectContaining({ 'filter[type]': 'FIRED', page: 1 })
    );
    expect(wrapper.text()).toContain('Range session');
    expect(wrapper.text()).not.toContain('Cabela');
  });

  it('triggers a fresh API call with reversed sort when toggled', async () => {
    const wrapper = await mountShow();

    const newestRowsHtml = wrapper.html();
    expect(newestRowsHtml.indexOf('May 20')).toBeLessThan(newestRowsHtml.indexOf('May 12'));
    fetchForAmmo.mockClear();

    await findButton(wrapper, 'Newest').trigger('click');
    await flushPromises();

    expect(fetchForAmmo).toHaveBeenCalledWith(
      1,
      expect.objectContaining({ sort: 'inventory_date,rounds', page: 1 })
    );
    expect(findButton(wrapper, 'Oldest')).toBeTruthy();
    const oldestRowsHtml = wrapper.html();
    expect(oldestRowsHtml.indexOf('May 12')).toBeLessThan(oldestRowsHtml.indexOf('May 20'));
  });
});
