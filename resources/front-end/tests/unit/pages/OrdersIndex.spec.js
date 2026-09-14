import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const { fetchOrders } = vi.hoisted(() => ({
  fetchOrders: vi.fn(),
}));

vi.mock('@/stores/orders', () => ({
  useOrdersStore: () => ({ fetchAll: fetchOrders }),
}));

import OrdersIndex from '@/pages/orders/OrdersIndex.vue';

const orders = [
  {
    id: 1,
    store_id: 1,
    store: { id: 1, label: 'Alpha Supply' },
    order_date: '2026-08-10',
    order_ref: 'ALPHA-100',
    rounds: 100,
    total_cost: 45.5,
    items: [
      {
        ammunition: {
          manufacturer: 'Federal',
          label: 'HST',
          caliber: { label: '9mm' },
        },
      },
    ],
  },
  {
    id: 2,
    store_id: 2,
    store: { id: 2, label: 'Bravo Outfitters' },
    order_date: '2026-09-10',
    order_ref: 'BRAVO-200',
    rounds: 500,
    total_cost: 199.99,
    items: [
      {
        ammunition: {
          manufacturer: 'PMC',
          label: 'Bronze',
          caliber: { label: '5.56 NATO' },
        },
      },
    ],
  },
];

function mountPage() {
  return mount(OrdersIndex, {
    global: {
      components: {
        LoadingState: { template: '<div>Loading</div>' },
      },
      stubs: {
        'router-link': {
          props: ['to'],
          template: '<a :data-route="to.name"><slot /></a>',
        },
      },
    },
  });
}

describe('OrdersIndex', () => {
  beforeEach(() => {
    fetchOrders.mockReset().mockResolvedValue({ data: orders });
  });

  it('lists every store order and opens the add order form', async () => {
    const wrapper = mountPage();
    await flushPromises();

    expect(fetchOrders).toHaveBeenCalledOnce();
    expect(wrapper.findAll('[data-testid="order-row"]')).toHaveLength(2);
    expect(wrapper.text()).toContain('Alpha Supply');
    expect(wrapper.text()).toContain('Bravo Outfitters');
    expect(wrapper.get('[data-route="OrderCreate"]').text()).toContain('Add Order');
  });

  it('filters orders by search, store, and date range', async () => {
    const wrapper = mountPage();
    await flushPromises();

    await wrapper.get('[aria-label="Search orders"]').setValue('Federal');
    expect(wrapper.findAll('[data-testid="order-row"]')).toHaveLength(1);
    expect(wrapper.text()).toContain('Alpha Supply');

    await wrapper.get('[aria-label="Search orders"]').setValue('');
    await wrapper.get('[aria-label="Filter by store"]').setValue('2');
    expect(wrapper.findAll('[data-testid="order-row"]')).toHaveLength(1);
    expect(wrapper.text()).toContain('Bravo Outfitters');

    await wrapper.get('[aria-label="Filter by store"]').setValue('');
    await wrapper.get('[aria-label="Orders from date"]').setValue('2026-09-01');
    await wrapper.get('[aria-label="Orders through date"]').setValue('2026-09-30');
    expect(wrapper.findAll('[data-testid="order-row"]')).toHaveLength(1);
    expect(wrapper.text()).toContain('BRAVO-200');
  });
});
