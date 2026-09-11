import { describe, expect, it, vi } from 'vitest';
import { mount } from '@vue/test-utils';

vi.mock('vue-router', () => ({
  useRouter: () => ({ push: vi.fn() }),
}));

import LightCard from '@/components/accessories/LightCard.vue';
import MiscCard from '@/components/accessories/MiscCard.vue';
import OpticCard from '@/components/accessories/OpticCard.vue';
import SuppressorCard from '@/components/accessories/SuppressorCard.vue';

const longFirearmName = 'Daniel Defense DDM4 V7 Special Configuration';

const cards = [
  { component: SuppressorCard, prop: 'suppressor' },
  { component: OpticCard, prop: 'optic' },
  { component: LightCard, prop: 'light' },
  { component: MiscCard, prop: 'misc' },
];

describe('accessory mounted status badges', () => {
  it.each(cards)(
    'keeps the mounted badge compact and truncatable for $prop cards',
    ({ component, prop }) => {
      const wrapper = mount(component, {
        props: {
          [prop]: {
            id: 1,
            label: 'Accessory',
            manufacturer: 'Maker',
            firearm: { label: longFirearmName },
          },
        },
        global: {
          stubs: {
            'router-link': { props: ['to'], template: '<a><slot /></a>' },
          },
        },
      });

      const badge = wrapper.findAll('span').find((span) => span.text().includes('ON ·'));

      expect(badge).toBeDefined();
      expect(badge.classes()).toEqual(
        expect.arrayContaining(['max-w-[68%]', 'gap-1', 'px-[6px]', 'py-[1px]'])
      );
      expect(badge.find('.truncate').exists()).toBe(true);
      expect(badge.text()).toContain(longFirearmName);
    }
  );
});
