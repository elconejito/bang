import { describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const update = vi.fn().mockResolvedValue({});

vi.mock('@/stores/inventories', () => ({
  useInventoriesStore: () => ({ update }),
}));

vi.mock('@/plugins/axios', () => ({
  axiosInstance: {
    get: vi.fn().mockResolvedValue({
      data: { data: [{ id: 7, label: 'Local Store' }] },
    }),
  },
}));

import EditInventoryModal from '@/components/ammunition/EditInventoryModal.vue';

describe('EditInventoryModal', () => {
  it('uses the purchase layout and includes the current store', async () => {
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
      global: { stubs: { Teleport: true, ReferenceItemModal: true } },
    });
    await flushPromises();

    expect(wrapper.text()).toContain('Purchase details');
    expect(wrapper.text()).toContain('Store / FFL');
    expect(wrapper.text()).toContain('$ total');
    expect(wrapper.get('select').element.value).toBe('7');
    expect(wrapper.get('input[placeholder="optional"]').element.value).toBe('ABC-1');
  });
});
