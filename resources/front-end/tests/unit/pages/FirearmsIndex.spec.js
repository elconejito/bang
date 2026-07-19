import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const fetchAll = vi.fn();

vi.mock('@/stores/firearms', () => ({
  useFirearmsStore: () => ({ fetchAll }),
}));

import FirearmsIndex from '@/pages/firearms/FirearmsIndex.vue';

const firearms = [
  {
    id: 1,
    label: 'Glock 19',
    manufacturer: 'Glock',
    model: '19',
    calibers: [{ id: 1, label: '9mm' }],
    location: { id: 1, label: 'Main Safe' },
  },
  {
    id: 2,
    label: 'Daniel Defense DDM4',
    manufacturer: 'Daniel Defense',
    model: 'DDM4',
    calibers: [{ id: 2, label: '5.56 NATO' }],
    location: { id: 2, label: 'Rifle Safe' },
  },
  {
    id: 3,
    label: 'Retired Pistol',
    manufacturer: 'Colt',
    model: '1911',
    status: 'archived',
    archive_reason: 'retired',
    calibers: [{ id: 3, label: '.45 ACP' }],
    location: { id: 1, label: 'Main Safe' },
  },
];

function findButton(wrapper, label) {
  return wrapper.findAll('button').find((button) => button.text().trim() === label);
}

async function mountIndex() {
  const wrapper = mount(FirearmsIndex, {
    global: {
      stubs: {
        AppBreadcrumb: true,
        PageHeader: { template: '<div><slot name="actions" /></div>' },
        FirearmList: {
          name: 'FirearmList',
          props: ['firearms'],
          template: '<div data-testid="firearm-list" />',
        },
        FirearmTable: {
          name: 'FirearmTable',
          props: ['firearms'],
          template: '<div data-testid="firearm-table" />',
        },
        ErrorCard: true,
        'router-link': true,
      },
    },
  });
  await flushPromises();

  return wrapper;
}

describe('FirearmsIndex view toggle', () => {
  beforeEach(() => {
    localStorage.clear();
    fetchAll.mockReset();
    fetchAll.mockResolvedValue({ data: firearms });
  });

  it('switches between the grid and table views', async () => {
    const wrapper = await mountIndex();

    expect(wrapper.find('[data-testid="firearm-list"]').exists()).toBe(true);
    expect(wrapper.find('[data-testid="firearm-table"]').exists()).toBe(false);
    expect(findButton(wrapper, 'Grid').attributes('aria-pressed')).toBe('true');

    await findButton(wrapper, 'Table').trigger('click');

    expect(wrapper.find('[data-testid="firearm-list"]').exists()).toBe(false);
    expect(wrapper.find('[data-testid="firearm-table"]').exists()).toBe(true);
    expect(findButton(wrapper, 'Table').attributes('aria-pressed')).toBe('true');
  });

  it('passes the existing filtered firearms to the table', async () => {
    const wrapper = await mountIndex();

    await wrapper.get('input[type="text"]').setValue('Glock');
    await findButton(wrapper, 'Table').trigger('click');

    expect(wrapper.getComponent({ name: 'FirearmTable' }).props('firearms')).toEqual([firearms[0]]);
  });

  it('restores the last selected view', async () => {
    localStorage.setItem('bang:view-mode:firearms', 'table');

    const wrapper = await mountIndex();

    expect(wrapper.find('[data-testid="firearm-table"]').exists()).toBe(true);
    expect(findButton(wrapper, 'Table').attributes('aria-pressed')).toBe('true');
  });

  it('loads all statuses but shows active firearms by default', async () => {
    const wrapper = await mountIndex();

    expect(fetchAll).toHaveBeenCalledWith({ 'filter[status]': 'all' });
    expect(wrapper.getComponent({ name: 'FirearmList' }).props('firearms')).toEqual([
      firearms[1],
      firearms[0],
    ]);

    await findButton(wrapper, 'Active').trigger('click');
    await findButton(wrapper, 'Archived').trigger('click');

    expect(wrapper.getComponent({ name: 'FirearmList' }).props('firearms')).toEqual([firearms[2]]);
  });
});
