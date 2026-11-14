import { describe, expect, it, vi, beforeEach } from 'vitest';
import { mount, flushPromises, RouterLinkStub } from '@vue/test-utils';

const fetchOne = vi.fn();
const fetchStats = vi.fn();
const fetchForAmmo = vi.fn();

vi.mock('@/stores/ammunition', () => ({
  useAmmunitionStore: () => ({ fetchOne, fetchStats }),
}));

vi.mock('@/stores/inventories', () => ({
  useInventoriesStore: () => ({ fetchForAmmo }),
}));

// Charts require a canvas context that happy-dom doesn't provide.
vi.mock('vue-chartjs', () => ({
  Bar: {
    name: 'AmmoCostChart',
    props: ['data', 'options'],
    template: '<div data-testid="ammo-cost-chart" />',
  },
  Line: {
    name: 'AmmoOnHandChart',
    props: ['data', 'options'],
    template: '<div data-testid="ammo-on-hand-chart" />',
  },
}));

import AmmoShow from '@/pages/ammunition/AmmoShow.vue';

const ammo = {
  id: 1,
  label: 'American Eagle',
  manufacturer: 'Federal',
  on_hand: 850,
  caliber: { id: 1, label: '9mm' },
  purpose: { id: 1, label: 'Range' },
  primer_type: { id: 1, label: 'Boxer' },
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

async function mountShow(
  ammoOverrides = {},
  statsOverrides = {},
  inventoryResponder = serverRespond
) {
  fetchOne.mockResolvedValue({ data: { ...ammo, ...ammoOverrides } });
  fetchStats.mockResolvedValue({
    data: {
      months: Array.from({ length: 12 }, (_, index) => ({
        key: `2025-${String(index + 1).padStart(2, '0')}`,
        label: 'May',
        on_hand: index === 11 ? 850 : 350,
        purchase_cost_per_round: index === 11 ? 0.3 : 0,
      })),
      average_purchase_cost_per_round: 0.3,
      estimated_current_value: 255,
      peak_on_hand: 850,
      purchase_cost_range: { min: 0.3, max: 0.3 },
      ...statsOverrides,
    },
  });
  fetchForAmmo.mockImplementation(inventoryResponder);
  const wrapper = mount(AmmoShow, {
    props: { ammunitionId: 1 },
    global: {
      stubs: {
        'router-link': RouterLinkStub,
        AddStockModal: true,
        EditInventoryModal: true,
        LoadingState: true,
        DeleteInventoryAdjustmentModal: {
          props: ['entry'],
          emits: ['close', 'deleted'],
          template:
            '<div data-testid="delete-adjustment-modal"><span>{{ entry.id }}</span><button @click="$emit(\'deleted\', entry.id)">Confirm delete</button></div>',
        },
        AppBreadcrumb: true,
        NotesPanel: {
          props: { hideEmptySearch: Boolean },
          template:
            '<div data-testid="notes-panel-stub" :data-hide-empty-search="hideEmptySearch ? \'enabled\' : undefined" />',
        },
      },
    },
  });
  await flushPromises();
  return wrapper;
}

describe('AmmoShow inventory & usage controls', () => {
  beforeEach(() => {
    fetchOne.mockReset();
    fetchStats.mockReset();
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

  it('labels centerfire primer data as the primer system', async () => {
    const wrapper = await mountShow();

    expect(wrapper.text()).toContain('Primer system');
    expect(wrapper.text()).toContain('Boxer');
  });

  it('loads chart and value statistics from the dedicated endpoint', async () => {
    const wrapper = await mountShow();

    expect(fetchStats).toHaveBeenCalledWith(1);
    expect(fetchForAmmo).not.toHaveBeenCalledWith(1, expect.objectContaining({ per_page: 200 }));
    expect(wrapper.text()).toContain('$0.3000');
    expect(wrapper.text()).toContain('$255.00');
    expect(wrapper.text()).toContain('peak 850');
  });

  it('renders all on-hand snapshots as a stepped line with a zero baseline', async () => {
    const months = Array.from({ length: 12 }, (_, index) => ({
      key: `2025-${String(index + 1).padStart(2, '0')}`,
      label: 'May',
      on_hand: 350,
      purchase_cost_per_round: 0.3,
    }));
    const wrapper = await mountShow({}, { months, peak_on_hand: 350 });
    const line = wrapper.findComponent({ name: 'AmmoOnHandChart' });
    const data = line.props('data');
    const options = line.props('options');

    expect(line.exists()).toBe(true);
    expect(data.datasets[0].data).toHaveLength(12);
    expect(data.datasets[0].data).toEqual(Array(12).fill(350));
    expect(data.datasets[0].stepped).toBe(true);
    expect(data.datasets[0].backgroundColor).toBeUndefined();
    expect(data.datasets[0].pointRadius({ dataIndex: 0 })).toBe(2);
    expect(data.datasets[0].pointRadius({ dataIndex: 11 })).toBe(4);
    expect(options.scales.y.beginAtZero).toBe(true);
    expect(options.interaction).toEqual({ mode: 'index', intersect: false });
    expect(options.plugins.tooltip.callbacks.title([{ dataIndex: 0 }])).toBe('January 2025');
    expect(options.plugins.tooltip.callbacks.label({ parsed: { y: 350 } })).toBe('350 rounds');
    expect(wrapper.text()).toContain('No net change since Jan 2025');
  });

  it('uses metric-specific full-month tooltips for zero values and prices', async () => {
    const months = [
      { key: '2025-01', label: 'Jan', on_hand: 0, purchase_cost_per_round: 0 },
      { key: '2025-02', label: 'Feb', on_hand: 0, purchase_cost_per_round: 0.3 },
    ];
    const wrapper = await mountShow({}, { months, peak_on_hand: 0 });
    const line = wrapper.findComponent({ name: 'AmmoOnHandChart' });
    const cost = wrapper.findComponent({ name: 'AmmoCostChart' });
    const lineOptions = line.props('options');
    const barOptions = cost.props('options');

    expect(line.props('data').datasets[0].pointRadius({ dataIndex: 1 })).toBe(4);
    expect(cost.props('data').datasets[0].backgroundColor).toBe('#c2a14d');
    expect(cost.props('data').datasets[0].hoverBackgroundColor).toBe('#b08a2e');
    expect(lineOptions.plugins.tooltip.callbacks.title([{ dataIndex: 0 }])).toBe('January 2025');
    expect(lineOptions.plugins.tooltip.callbacks.label({ parsed: { y: 0 } })).toBe('0 rounds');
    expect(barOptions.plugins.tooltip.callbacks.title([{ dataIndex: 1 }])).toBe('February 2025');
    expect(barOptions.plugins.tooltip.callbacks.label({ parsed: { y: 0 } })).toBe(
      'No purchase price recorded'
    );
    expect(barOptions.plugins.tooltip.callbacks.label({ parsed: { y: 0.3 } })).toBe(
      '$0.3000 per round'
    );
  });

  it('renders an empty on-hand history without a misleading change', async () => {
    const wrapper = await mountShow({}, { months: [], peak_on_hand: 0 });

    expect(
      wrapper.findComponent({ name: 'AmmoOnHandChart' }).props('data').datasets[0].data
    ).toEqual([]);
    expect(wrapper.get('[role="img"]').attributes('aria-label')).toBe(
      'No on-hand history is available.'
    );
    expect(wrapper.text()).not.toContain('since first monthly snapshot');
  });

  it('keeps notes in the left rail and inventory in the right column', async () => {
    const wrapper = await mountShow();
    const detailGrid = wrapper
      .findAll('div')
      .find((element) => element.classes().includes('grid-cols-[344px_1fr]'));

    expect(detailGrid).toBeTruthy();
    expect(detailGrid.element.children).toHaveLength(2);
    expect(
      detailGrid.element.children[0].querySelector('[data-testid="notes-panel-stub"]')
    ).not.toBeNull();
    expect(detailGrid.element.children[1].textContent).toContain('Inventory & usage');
  });

  it('configures the notes panel to hide search before any notes exist', async () => {
    const wrapper = await mountShow();

    expect(
      wrapper.get('[data-testid="notes-panel-stub"]').attributes('data-hide-empty-search')
    ).toBe('enabled');
  });

  it('shows shotgun details in the specs card', async () => {
    const wrapper = await mountShow({
      purpose: null,
      shell_length: { id: 1, label: '2¾ in' },
      shell_type: { id: 2, label: 'Buckshot' },
      shot_material: { id: 3, label: 'Lead' },
      shot_weight: { id: 4, label: '7/8 oz' },
    });

    expect(wrapper.text()).toContain('Shell length');
    expect(wrapper.text()).toContain('2¾ in');
    expect(wrapper.text()).toContain('Shell type');
    expect(wrapper.text()).toContain('Buckshot');
    expect(wrapper.text()).toContain('Shot material');
    expect(wrapper.text()).toContain('Lead');
    expect(wrapper.text()).toContain('Shot weight');
    expect(wrapper.text()).toContain('7/8 oz');
    expect(wrapper.text()).not.toContain('No specs recorded.');
  });

  it('shows FPS in the specs card when it is recorded', async () => {
    const wrapper = await mountShow({
      purpose: null,
      primer_type: null,
      fps: '1,150',
    });

    expect(wrapper.text()).toContain('FPS');
    expect(wrapper.text()).toContain('1,150');
    expect(wrapper.text()).toContain('feet per second');
    expect(wrapper.text()).not.toContain('No specs recorded.');
  });

  it('links purchase activity to its order', async () => {
    const wrapper = await mountShow();
    const purchaseLink = wrapper
      .findAllComponents(RouterLinkStub)
      .find((link) => link.text().includes('Purchase · Cabela'));

    expect(purchaseLink).toBeTruthy();
    expect(purchaseLink.props('to')).toEqual({ name: 'OrderShow', params: { order_id: 12 } });
  });

  it('offers deletion only for adjustments and refreshes the current filtered sort', async () => {
    const entries = [
      { id: 3, inventory_date: '2026-05-21', type: 'ADJUST', rounds: -20, balance: 830 },
      ...ledger,
    ];
    const responder = vi.fn(() =>
      Promise.resolve({ data: entries, meta: { total: entries.length, last_page: 1 } })
    );
    const wrapper = await mountShow({}, {}, responder);

    expect(wrapper.find('button[aria-label="Delete adjustment from 2026-05-21"]').exists()).toBe(
      true
    );
    expect(wrapper.findAll('button[aria-label^="Delete adjustment"]')).toHaveLength(1);
    expect(wrapper.find('button[aria-label="Delete adjustment from 2026-05-20"]').exists()).toBe(
      false
    );
    expect(wrapper.find('button[aria-label="Delete adjustment from 2026-05-12"]').exists()).toBe(
      false
    );

    await findButton(wrapper, 'All').trigger('click');
    await findButton(wrapper, 'Adjust').trigger('click');
    await findButton(wrapper, 'Newest').trigger('click');
    await flushPromises();
    await wrapper.get('button[aria-label="Delete adjustment from 2026-05-21"]').trigger('click');
    await wrapper.get('[data-testid="delete-adjustment-modal"] button').trigger('click');
    await flushPromises();

    expect(wrapper.find('[data-testid="delete-adjustment-modal"]').exists()).toBe(false);
    expect(fetchOne).toHaveBeenCalledTimes(2);
    expect(fetchStats).toHaveBeenCalledTimes(2);
    expect(responder).toHaveBeenLastCalledWith(
      1,
      expect.objectContaining({
        'filter[type]': 'ADJUST',
        sort: 'inventory_date,rounds',
      })
    );
  });

  it('clamps to the final ledger page after deleting its only row', async () => {
    const adjustment = {
      id: 3,
      inventory_date: '2026-05-21',
      type: 'ADJUST',
      rounds: -20,
      balance: 830,
    };
    let request = 0;
    const responder = vi.fn((_id, params) => {
      request += 1;
      if (request === 1) {
        return Promise.resolve({ data: ledger, meta: { total: 11, last_page: 2 } });
      }
      if (request === 2) {
        return Promise.resolve({ data: [adjustment], meta: { total: 11, last_page: 2 } });
      }
      if (request === 3) {
        return Promise.resolve({ data: [], meta: { total: 10, last_page: 1 } });
      }

      return Promise.resolve({
        data: ledger,
        meta: { total: 10, last_page: 1, page: params.page },
      });
    });
    const wrapper = await mountShow({}, {}, responder);

    await findButton(wrapper, 'Next').trigger('click');
    await flushPromises();
    await wrapper.get('button[aria-label="Delete adjustment from 2026-05-21"]').trigger('click');
    await wrapper.get('[data-testid="delete-adjustment-modal"] button').trigger('click');
    await flushPromises();

    expect(responder).toHaveBeenLastCalledWith(1, expect.objectContaining({ page: 1 }));
    expect(findButton(wrapper, 'Next')).toBeFalsy();
  });

  it('removes a confirmed adjustment locally when its refresh fails and retries without deleting again', async () => {
    const adjustment = {
      id: 3,
      inventory_date: '2026-05-21',
      type: 'ADJUST',
      rounds: -20,
      balance: 830,
    };
    const responder = vi
      .fn()
      .mockResolvedValueOnce({ data: [adjustment], meta: { total: 1, last_page: 1 } })
      .mockRejectedValueOnce(new Error('Refresh unavailable'))
      .mockResolvedValueOnce({ data: [], meta: { total: 0, last_page: 1 } });
    const wrapper = await mountShow({}, {}, responder);

    await wrapper.get('button[aria-label="Delete adjustment from 2026-05-21"]').trigger('click');
    await wrapper.get('[data-testid="delete-adjustment-modal"] button').trigger('click');
    await flushPromises();

    expect(wrapper.text()).toContain('Adjustment deleted, but inventory totals could not refresh.');
    expect(wrapper.find('button[aria-label="Delete adjustment from 2026-05-21"]').exists()).toBe(
      false
    );

    await findButton(wrapper, 'Retry refresh').trigger('click');
    await flushPromises();

    expect(responder).toHaveBeenCalledTimes(3);
    expect(wrapper.text()).not.toContain(
      'Adjustment deleted, but inventory totals could not refresh.'
    );
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
