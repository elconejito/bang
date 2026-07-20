import { describe, expect, it, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';

vi.mock('vue-router', () => ({
  useRoute: () => ({ query: {} }),
  useRouter: () => ({ push: vi.fn() }),
}));

import AmmoCard from '@/components/ammunition/AmmoCard.vue';
import SuppressorCard from '@/components/accessories/SuppressorCard.vue';
import OpticCard from '@/components/accessories/OpticCard.vue';
import LightCard from '@/components/accessories/LightCard.vue';
import MiscCard from '@/components/accessories/MiscCard.vue';
import MagGroupCard from '@/components/accessories/MagGroupCard.vue';
import MagazineGroupCard from '@/components/magazines/MagazineGroupCard.vue';
import TrainingCard from '@/components/training/TrainingCard.vue';

const subtleListShadow =
  'hover:shadow-[0_1px_2px_rgba(20,22,26,0.05),0_8px_20px_rgba(20,22,26,0.07)]';
const componentStubs = {
  RouterLink: { template: '<a><slot /></a>' },
};

describe('list card hover feedback', () => {
  it.each([
    [
      'ammunition',
      AmmoCard,
      {
        ammo: {
          id: 1,
          label: 'HST',
          manufacturer: 'Federal',
          on_hand: 100,
          reorder_min: 50,
        },
      },
    ],
    [
      'suppressors',
      SuppressorCard,
      { suppressor: { id: 1, label: 'Omega 9K', manufacturer: 'SilencerCo' } },
    ],
    ['optics', OpticCard, { optic: { id: 1, label: '507C', manufacturer: 'Holosun' } }],
    ['lights', LightCard, { light: { id: 1, label: 'TLR-7', manufacturer: 'Streamlight' } }],
    ['misc accessories', MiscCard, { misc: { id: 1, label: 'Sling', manufacturer: 'Ferro' } }],
    [
      'accessory magazine groups',
      MagGroupCard,
      {
        group: {
          model_name: 'PMAG',
          manufacturer: 'Magpul',
          capacity: 30,
          magazines: [],
        },
      },
    ],
    [
      'magazine groups',
      MagazineGroupCard,
      {
        group: {
          key: 'magpul-30',
          model_name: 'PMAG',
          manufacturer: 'Magpul',
          capacity: 30,
          calibers: [],
          summary: { total: 0, in_gun: 0, loaded: 0, empty: 0 },
        },
      },
    ],
  ])('%s use the standard subtle list elevation', (_name, component, props) => {
    const wrapper = mount(component, { props, global: { stubs: componentStubs } });

    expect(wrapper.classes()).toEqual(
      expect.arrayContaining([
        'transition-[border-color,box-shadow]',
        'duration-150',
        subtleListShadow,
      ])
    );
    expect(wrapper.classes()).not.toContain('hover:shadow-md');
    expect(wrapper.classes()).not.toContain('transition-all');
  });

  it('keeps the smaller session-card elevation from the handoff', () => {
    const wrapper = mount(TrainingCard, {
      props: {
        session: {
          id: 1,
          label: 'Range Day',
          session_date: '2026-07-18',
          total_rounds: 100,
          firearms_used: [],
        },
      },
    });

    expect(wrapper.classes()).toEqual(
      expect.arrayContaining([
        'transition-[border-color,box-shadow]',
        'duration-150',
        'hover:shadow-[0_1px_2px_rgba(20,22,26,0.05),0_6px_16px_rgba(20,22,26,0.06)]',
      ])
    );
    expect(wrapper.classes()).not.toContain('transition-all');
  });

  it('uses the same subtle token on reference-data index cards', () => {
    const pages = [
      'resources/front-end/src/pages/locations/LocationsIndex.vue',
      'resources/front-end/src/pages/stores/StoresIndex.vue',
      'resources/front-end/src/pages/ranges/RangesIndex.vue',
    ];

    for (const page of pages) {
      const source = readFileSync(resolve(process.cwd(), page), 'utf8');

      expect(source).toContain(subtleListShadow);
      expect(source).not.toContain('hover:shadow-md');
      expect(source).not.toContain('transition-all');
    }
  });
});
