import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const fetchStores = vi.fn();
const fetchItemOptions = vi.fn();

vi.mock('@/stores/gunStores', () => ({
  useGunStoresStore: () => ({ fetchAll: fetchStores }),
}));
vi.mock('@/stores/orders', () => ({
  useOrdersStore: () => ({ fetchItemOptions }),
}));
vi.mock('@/components/orders/OrderAssetCreateDialog.vue', () => ({
  default: {
    props: ['type'],
    emits: ['complete', 'cancel'],
    template: '<div data-testid="asset-dialog" />',
  },
}));

import OrderForm from '@/components/orders/OrderForm.vue';

const options = [
  { type: 'ammunition', id: 1, label: '9mm · Federal · HST · 124gr' },
  { type: 'ammunition', id: 2, label: '5.56 · PMC · Bronze · 55gr' },
  { type: 'firearm', id: 7, label: 'Glock 19', manufacturer: 'Glock', secondary_label: 'Gen 5' },
];

function mountForm(props = {}) {
  return mount(OrderForm, {
    props,
    global: {
      components: { LoadingState: { template: '<div>Loading</div>' } },
    },
  });
}

async function ready(props = {}) {
  const wrapper = mountForm(props);
  await flushPromises();
  return wrapper;
}

describe('OrderForm', () => {
  beforeEach(() => {
    fetchStores.mockReset().mockResolvedValue({ data: [{ id: 3, label: 'Range Store' }] });
    fetchItemOptions
      .mockReset()
      .mockResolvedValue({ data: options.map((option) => ({ ...option })) });
  });

  it('submits mixed ammunition and asset payloads and totals ammunition rounds only', async () => {
    const wrapper = await ready();
    await wrapper.get('select').setValue('3');
    const selects = wrapper.findAll('select');
    await selects[2].setValue('1');
    await wrapper.get('input[type="number"]').setValue('100');
    await wrapper.findAll('input[type="number"]')[1].setValue('250');
    const add = wrapper.findAll('button').find((button) => button.text().includes('Add item'));
    await add.trigger('click');
    const rows = wrapper.findAll('[class*="overflow-hidden rounded border"]');
    await rows[1].findAll('select')[0].setValue('firearm');
    await rows[1].findAll('select')[1].setValue('7');
    await rows[1].find('input[type="number"]').setValue('250');
    expect(wrapper.text()).toContain('100');
    expect(wrapper.text()).toContain('$500.00');
    await wrapper.get('form').trigger('submit');
    expect(wrapper.emitted('submit')[0][0].items).toEqual([
      { type: 'ammunition', ammunition_id: 1, rounds: 100, cost: 250 },
      { type: 'firearm', asset_id: 7, cost: 250 },
    ]);
  });

  it('preserves saved typed ids while editing and clears them on category change', async () => {
    const wrapper = await ready({
      initialOrder: {
        id: 4,
        store_id: 3,
        order_date: '2026-09-01',
        items: [{ id: 81, type: 'ammunition', ammunition_id: 1, rounds: 20, cost: 10 }],
      },
    });
    const row = wrapper.findAll('[class*="overflow-hidden rounded border"]')[0];
    expect(row.findAll('select')[1].element.value).toBe('1');
    await wrapper.get('form').trigger('submit');
    expect(wrapper.emitted('submit')[0][0].items[0].id).toBe(81);
    await row.findAll('select')[0].setValue('firearm');
    await wrapper.get('form').trigger('submit');
    expect(row.findAll('select')[1].element.value).toBe('');
    await row.findAll('select')[1].setValue('7');
    await wrapper.get('form').trigger('submit');
    expect(wrapper.emitted('submit')[1][0].items[0]).toEqual({
      type: 'firearm',
      asset_id: 7,
      cost: 10,
    });
  });

  it('disables duplicate options and keeps a selected option visible during search', async () => {
    const wrapper = await ready();
    await wrapper.get('select').setValue('3');
    await wrapper.findAll('select')[2].setValue('1');
    await wrapper
      .findAll('button')
      .find((button) => button.text().includes('Add item'))
      .trigger('click');
    const rows = wrapper.findAll('[class*="overflow-hidden rounded border"]');
    await rows[1].findAll('select')[1].setValue('2');
    await wrapper.get('[aria-label="Search order items"]').setValue('PMC');
    expect(rows[0].findAll('option').some((option) => option.element.value === '1')).toBe(true);
    expect(rows[1].find('option[value="1"]').element.disabled).toBe(true);
  });

  it('opens the reusable asset dialog to create into the first asset row', async () => {
    const wrapper = await ready();
    await wrapper.get('select').setValue('3');
    await wrapper.findAll('select')[1].setValue('firearm');
    await wrapper
      .findAll('button')
      .find((button) => button.text().includes('Create new'))
      .trigger('click');
    expect(wrapper.find('[data-testid="asset-dialog"]').exists()).toBe(true);
    wrapper
      .findComponent('[data-testid="asset-dialog"]')
      .vm.$emit('complete', { id: 8, label: 'New firearm', manufacturer: 'CZ' });
    await flushPromises();
    expect(wrapper.find('[data-testid="asset-dialog"]').exists()).toBe(false);
    await wrapper.get('input[type="number"]').setValue('500');
    await wrapper.get('form').trigger('submit');
    expect(wrapper.emitted('submit')[0][0].items).toEqual([
      { type: 'firearm', asset_id: 8, cost: 500 },
    ]);
  });

  it('shows an option loading error', async () => {
    fetchItemOptions.mockRejectedValue(new Error('offline'));
    const wrapper = mountForm();
    await flushPromises();
    expect(wrapper.text()).toContain('offline');
  });
});
