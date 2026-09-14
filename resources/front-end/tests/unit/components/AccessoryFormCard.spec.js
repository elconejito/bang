import { describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const fetchAll = vi.fn().mockResolvedValue({ data: [] });
const create = vi.fn().mockResolvedValue({ data: { id: 90 } });

vi.mock('@/stores/firearms', () => ({
  useFirearmsStore: () => ({ fetchAll }),
}));
vi.mock('@/stores/locations', () => ({
  useLocationsStore: () => ({ fetchAll }),
}));
vi.mock('@/stores/gunStores', () => ({
  useGunStoresStore: () => ({ fetchAll }),
}));
vi.mock('@/stores/colors', () => ({
  useColorsStore: () => ({ fetchAll }),
}));
vi.mock('@/stores/suppressors', () => ({
  useSuppressorsStore: () => ({ create: vi.fn(), update: vi.fn() }),
}));
vi.mock('@/stores/optics', () => ({
  useOpticsStore: () => ({ create, update: vi.fn() }),
}));
vi.mock('@/stores/lights', () => ({
  useLightsStore: () => ({ create: vi.fn(), update: vi.fn() }),
}));
vi.mock('@/stores/miscAccessories', () => ({
  useMiscAccessoriesStore: () => ({ create: vi.fn(), update: vi.fn() }),
}));
vi.mock('@/stores/mounts', () => ({
  useMountsStore: () => ({ create: vi.fn(), update: vi.fn() }),
}));
vi.mock('@/plugins/axios', () => ({
  axiosInstance: {
    get: vi.fn().mockResolvedValue({ data: { data: [{ id: 12, order_ref: 'PO-12' }] } }),
  },
}));
vi.mock('@/components/reference/useQuickAdd', () => ({
  useQuickAdd: () => ({
    quickAddType: { value: null },
    openQuickAdd: vi.fn(),
    closeQuickAdd: vi.fn(),
  }),
}));

import AccessoryFormCard from '@/components/accessories/AccessoryFormCard.vue';

const typePlaceholders = {
  suppressor: ['e.g. SilencerCo', 'e.g. Omega 9K'],
  optic: ['e.g. Holosun', 'e.g. 507C'],
  light: ['e.g. SureFire', 'e.g. X300'],
  misc: ['e.g. Blue Force Gear', 'e.g. Sling'],
};

describe('AccessoryFormCard', () => {
  it.each(Object.entries(typePlaceholders))(
    'uses %s-specific manufacturer and label placeholders',
    async (type, placeholders) => {
      const wrapper = mount(AccessoryFormCard, {
        props: { type },
        global: {
          stubs: {
            FormError: true,
            LoadingState: true,
            ReferenceItemModal: true,
          },
        },
      });
      await flushPromises();

      const textInputs = wrapper.findAll('input[type="text"]');
      expect(textInputs[0].attributes('placeholder')).toBe(placeholders[0]);
      expect(textInputs[1].attributes('placeholder')).toBe(placeholders[1]);
    }
  );

  it('includes optional order and cost in a standalone create payload', async () => {
    const wrapper = mount(AccessoryFormCard, {
      props: { type: 'optic' },
      global: { stubs: { FormError: true, LoadingState: true, ReferenceItemModal: true } },
    });
    await flushPromises();
    const textInputs = wrapper.findAll('input[type="text"]');
    await textInputs[0].setValue('Holosun');
    await textInputs[1].setValue('507C');
    const order = wrapper
      .findAll('select')
      .find((select) => select.find('option[value="12"]').exists());
    await order.setValue('12');
    const cost = wrapper.findAll('input[type="number"]').at(-1);
    await cost.setValue('499.95');
    await wrapper
      .findAll('button')
      .find((button) => button.text().includes('Add accessory'))
      .trigger('click');
    expect(create).toHaveBeenCalledWith(expect.objectContaining({ order_id: 12, cost: 499.95 }));
  });

  it('omits order and cost controls and payload fields in order context', async () => {
    const wrapper = mount(AccessoryFormCard, {
      props: { type: 'optic', orderContext: true },
      global: { stubs: { FormError: true, LoadingState: true, ReferenceItemModal: true } },
    });
    await flushPromises();
    expect(wrapper.text()).not.toContain('Line cost');
    const textInputs = wrapper.findAll('input[type="text"]');
    await textInputs[0].setValue('Holosun');
    await textInputs[1].setValue('507C');
    await wrapper
      .findAll('button')
      .find((button) => button.text().includes('Add accessory'))
      .trigger('click');
    expect(create).toHaveBeenLastCalledWith(
      expect.not.objectContaining({ order_id: expect.anything(), cost: expect.anything() })
    );
  });
});
