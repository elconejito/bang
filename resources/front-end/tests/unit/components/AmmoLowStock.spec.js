import { describe, expect, it, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import AmmoCard from '@/components/ammunition/AmmoCard.vue';
import AmmoTable from '@/components/ammunition/AmmoTable.vue';

vi.mock('vue-router', () => ({ useRouter: () => ({ push: vi.fn() }) }));

const cases = [
  ['zero stock without a reorder amount', 0, null, false],
  ['stock without a reorder amount', 25, undefined, false],
  ['zero stock with a zero reorder amount', 0, 0, false],
  ['zero stock below a reorder amount', 0, 50, true],
  ['stock below a reorder amount', 25, 50, true],
  ['stock equal to a reorder amount', 50, 50, false],
  ['stock above a reorder amount', 75, 50, false],
];

describe.each([
  ['card', AmmoCard],
  ['table', AmmoTable],
])('Ammo %s low stock', (_name, component) => {
  it.each(cases)('handles %s', (_description, onHand, reorderMin, expectedLow) => {
    const ammo = {
      id: 1,
      label: 'Range load',
      manufacturer: 'Federal',
      on_hand: onHand,
      reorder_min: reorderMin,
    };
    const wrapper = mount(component, {
      props:
        component === AmmoCard
          ? { ammo }
          : {
              groups: [
                {
                  caliberId: 1,
                  caliberLabel: '9mm',
                  items: [ammo],
                  totalRounds: onHand,
                  isLow: false,
                },
              ],
            },
      global: { stubs: { 'router-link': true } },
    });
    expect(wrapper.findAll('span').some((span) => span.text().trim() === 'LOW')).toBe(expectedLow);
    expect(wrapper.text().includes('MIN ')).toBe(expectedLow);
  });
});
