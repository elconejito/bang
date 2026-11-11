import { flushPromises, mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';

const { get, update } = vi.hoisted(() => ({ get: vi.fn(), update: vi.fn() }));

vi.mock('@/plugins/axios', () => ({ axiosInstance: { get, post: vi.fn() } }));
vi.mock('@/stores/inventories', () => ({ useInventoriesStore: () => ({ update }) }));

import AddStockModal from '@/components/ammunition/AddStockModal.vue';
import EditInventoryModal from '@/components/ammunition/EditInventoryModal.vue';

function deferred() {
  let resolve;
  let reject;
  const promise = new Promise((resolvePromise, rejectPromise) => {
    resolve = resolvePromise;
    reject = rejectPromise;
  });

  return { promise, resolve, reject };
}

const global = { stubs: { Teleport: true, ReferenceItemModal: true } };

describe('inventory store lookup gates', () => {
  beforeEach(() => {
    get.mockReset();
    update.mockReset();
  });

  it('keeps Add Stock controls hidden while stores load, while still allowing close', async () => {
    const stores = deferred();
    get.mockReturnValue(stores.promise);
    const wrapper = mount(AddStockModal, {
      props: { ammo: { id: 1, label: 'Training ammo', caliber: { label: '9mm' }, on_hand: 100 } },
      global,
    });

    expect(wrapper.text()).toContain('Loading store options…');
    expect(wrapper.find('input').exists()).toBe(false);
    expect(wrapper.findAll('button').some((button) => button.text().includes('Add rounds'))).toBe(
      false
    );

    await wrapper.find('.modal-shell button').trigger('click');
    expect(wrapper.emitted('close')).toHaveLength(1);

    stores.resolve({ data: { data: [] } });
    await flushPromises();

    expect(wrapper.text()).not.toContain('Loading store options…');
    expect(wrapper.find('input').exists()).toBe(true);
  });

  it('keeps the edit form gated after a failed lookup, then retries with its selected store intact', async () => {
    get.mockRejectedValueOnce(new Error('Stores unavailable')).mockResolvedValue({
      data: { data: [{ id: 7, label: 'Local Store' }] },
    });
    const wrapper = mount(EditInventoryModal, {
      props: {
        entry: {
          id: 4,
          type: 'BUY',
          inventory_date: '2026-06-01',
          rounds: 100,
          cost: 30,
          store_id: 7,
          order_ref: 'ABC-1',
        },
      },
      global,
    });
    await flushPromises();

    expect(wrapper.text()).toContain('Could not load store options');
    expect(wrapper.find('input').exists()).toBe(false);
    await wrapper.get('form').trigger('submit');
    expect(update).not.toHaveBeenCalled();

    await wrapper
      .findAll('button')
      .find((button) => button.text() === 'Try again')
      .trigger('click');
    await flushPromises();

    expect(get).toHaveBeenCalledTimes(2);
    expect(wrapper.find('input').exists()).toBe(true);
    expect(wrapper.get('select').element.value).toBe('7');
  });
});
