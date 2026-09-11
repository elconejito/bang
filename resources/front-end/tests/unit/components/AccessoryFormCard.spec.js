import { describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const fetchAll = vi.fn().mockResolvedValue({ data: [] });

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
  useOpticsStore: () => ({ create: vi.fn(), update: vi.fn() }),
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
  axiosInstance: { get: vi.fn().mockResolvedValue({ data: { data: [] } }) },
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
});
