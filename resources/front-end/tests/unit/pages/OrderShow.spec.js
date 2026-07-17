import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount, RouterLinkStub } from '@vue/test-utils';

const fetchOne = vi.fn();

vi.mock('@/stores/orders', () => ({
  useOrdersStore: () => ({ fetchOne }),
}));

import OrderShow from '@/pages/orders/OrderShow.vue';

describe('OrderShow', () => {
  beforeEach(() => {
    fetchOne.mockReset();
  });

  it('links purchased ammunition to its detail page', async () => {
    fetchOne.mockResolvedValue({
      data: {
        id: 44,
        order_date: '2020-07-24',
        rounds: 100,
        total_cost: 55,
        store: { id: 3, label: 'NRA Range' },
        items: [
          {
            id: 302,
            ammunition_id: 1,
            rounds: 100,
            cost: 55,
            cost_per_round: 0.55,
            ammunition: {
              id: 1,
              manufacturer: 'PMC',
              label: 'X-TAC XP193',
              caliber: { id: 1, label: '5.56 NATO' },
            },
          },
        ],
      },
    });

    const wrapper = mount(OrderShow, {
      props: { orderId: 44 },
      global: {
        stubs: {
          'router-link': RouterLinkStub,
          AppBreadcrumb: true,
          NotesPanel: true,
        },
      },
    });
    await flushPromises();

    const ammunitionLink = wrapper
      .findAllComponents(RouterLinkStub)
      .find((link) => link.text().includes('PMC · X-TAC XP193'));

    expect(ammunitionLink).toBeTruthy();
    expect(ammunitionLink.props('to')).toEqual({
      name: 'AmmoShow',
      params: { ammunition_id: 1 },
    });
  });
});
