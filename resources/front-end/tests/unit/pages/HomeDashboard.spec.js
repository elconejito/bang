import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const { get } = vi.hoisted(() => ({ get: vi.fn() }));

vi.mock('@/plugins/axios', () => ({ axiosInstance: { get } }));
vi.mock('@/stores/auth', () => ({
  useAuthStore: () => ({ currentUser: { name: 'Harvey Syde' } }),
}));

import HomeDashboard from '@/pages/HomeDashboard.vue';

const dashboard = {
  stats: {
    firearms_count: 3,
    rounds_on_hand: 800,
    rounds_fired_12mo: 200,
    sessions_12mo: 6,
    ammo_cost_12mo: 120,
    days_since_last_session: null,
  },
  ammo_by_caliber: [],
  low_stock_ammo: [],
  pending_nfa: [],
  most_shot_firearms: [],
  recent_activity: [],
};

describe('HomeDashboard responsive layout', () => {
  beforeEach(() => {
    get.mockReset().mockResolvedValue({ data: { data: dashboard } });
  });

  it('uses the responsive layout while dashboard data is loading', () => {
    get.mockReturnValue(new Promise(() => {}));
    const wrapper = mount(HomeDashboard, {
      global: {
        stubs: {
          'router-link': { props: ['to'], template: '<a><slot /></a>' },
        },
      },
    });

    expect(wrapper.get('[data-testid="dashboard-stats"]').classes()).toEqual(
      expect.arrayContaining(['grid-cols-2', 'sm:grid-cols-6', 'lg:grid-cols-5'])
    );
    expect(wrapper.get('[data-testid="dashboard-content"]').classes()).toEqual(
      expect.arrayContaining(['grid-cols-1', 'lg:grid-cols-[1.45fr_1fr]'])
    );
    expect(wrapper.find('.max-w-80').exists()).toBe(true);
  });

  it('stacks loaded content and keeps all five stats balanced on narrow screens', async () => {
    const wrapper = mount(HomeDashboard, {
      global: {
        stubs: {
          'router-link': { props: ['to'], template: '<a><slot /></a>' },
        },
      },
    });
    await flushPromises();

    expect(wrapper.get('[data-testid="dashboard-stats"]').classes()).toEqual(
      expect.arrayContaining(['grid-cols-2', 'sm:grid-cols-6', 'lg:grid-cols-5'])
    );
    expect(wrapper.get('[data-testid="dashboard-content"]').classes()).toEqual(
      expect.arrayContaining(['grid-cols-1', 'lg:grid-cols-[1.45fr_1fr]'])
    );

    const statCells = Array.from(wrapper.get('[data-testid="dashboard-stats"]').element.children);
    expect(statCells).toHaveLength(5);
    expect(statCells[4].className).toContain('col-span-2');
    expect(statCells[3].className).toContain('sm:col-span-3');
    expect(statCells[4].className).toContain('sm:col-span-3');
  });
});
