import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';
import { nextTick } from 'vue';

const remove = vi.fn();

vi.mock('@/stores/inventories', () => ({
  useInventoriesStore: () => ({ remove }),
}));

import DeleteInventoryAdjustmentModal from '@/components/ammunition/DeleteInventoryAdjustmentModal.vue';

const entry = {
  id: 42,
  type: 'ADJUST',
  inventory_date: '2026-06-15',
  rounds: -25,
};

function mountModal(item = entry) {
  return mount(DeleteInventoryAdjustmentModal, {
    props: { entry: item },
    global: { stubs: { Teleport: true } },
  });
}

function deferred() {
  let resolve;
  const promise = new Promise((resolvePromise) => {
    resolve = resolvePromise;
  });

  return { promise, resolve };
}

describe('DeleteInventoryAdjustmentModal', () => {
  beforeEach(() => remove.mockReset());

  it('describes the adjustment and lets the user cancel without deleting', () => {
    const wrapper = mountModal();

    expect(wrapper.text()).toContain('Jun 15, 2026');
    expect(wrapper.text()).toContain('-25 rounds');
    expect(wrapper.text()).toContain(
      'On-hand inventory and later ledger balances will recalculate.'
    );
    expect(wrapper.text()).toContain('This cannot be undone.');

    wrapper.get('button:last-child').trigger('click');
    expect(wrapper.emitted('close')).toHaveLength(1);
    expect(remove).not.toHaveBeenCalled();
  });

  it('removes the selected adjustment and emits deleted on success', async () => {
    remove.mockResolvedValue({});
    const wrapper = mountModal();

    await wrapper.get('button:first-child').trigger('click');
    await flushPromises();

    expect(remove).toHaveBeenCalledWith(42);
    expect(wrapper.emitted('deleted')).toHaveLength(1);
    expect(wrapper.emitted('deleted')[0]).toEqual([42]);
    expect(wrapper.emitted('close')).toBeUndefined();
  });

  it('keeps the dialog open after an error so the user can retry', async () => {
    remove.mockRejectedValueOnce(new Error('Delete failed')).mockResolvedValueOnce({});
    const wrapper = mountModal();

    await wrapper.get('button:first-child').trigger('click');
    await flushPromises();

    expect(wrapper.text()).toContain('Delete failed');
    expect(wrapper.emitted('deleted')).toBeUndefined();

    await wrapper.get('button:first-child').trigger('click');
    await flushPromises();

    expect(remove).toHaveBeenCalledTimes(2);
    expect(wrapper.emitted('deleted')).toHaveLength(1);
  });

  it('prevents duplicate delete requests while the first request is pending', async () => {
    const deletion = deferred();
    remove.mockReturnValue(deletion.promise);
    const wrapper = mountModal();
    const deleteButton = wrapper.get('button:first-child');

    await deleteButton.trigger('click');
    await Promise.resolve();
    await nextTick();

    try {
      const pendingDeleteButton = wrapper
        .findAll('button')
        .find((button) => button.text().includes('Deleting…'));

      expect(remove).toHaveBeenCalledTimes(1);
      expect(pendingDeleteButton.attributes('disabled')).toBeDefined();
      expect(wrapper.get('button:last-child').attributes('disabled')).toBeDefined();
      await wrapper.find('.modal-scrim').trigger('click');
      expect(wrapper.emitted('close')).toBeUndefined();
      await pendingDeleteButton.trigger('click');
      expect(remove).toHaveBeenCalledTimes(1);
    } finally {
      deletion.resolve({});
      await flushPromises();
    }

    expect(wrapper.emitted('deleted')).toHaveLength(1);
  });

  it('does not remove entries that are not adjustments', async () => {
    const wrapper = mountModal({ ...entry, type: 'BUY' });

    await wrapper.get('button:first-child').trigger('click');

    expect(remove).not.toHaveBeenCalled();
    expect(wrapper.emitted('deleted')).toBeUndefined();
  });
});
