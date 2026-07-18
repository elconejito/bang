import { beforeEach, describe, expect, it, vi } from 'vitest';
import { mount } from '@vue/test-utils';

const { push } = vi.hoisted(() => ({ push: vi.fn() }));

vi.mock('vue-router', () => ({
  useRouter: () => ({ push }),
}));

import AmmoCard from '@/components/ammunition/AmmoCard.vue';
import FirearmCard from '@/components/firearms/FirearmCard.vue';
import TrainingCard from '@/components/training/TrainingCard.vue';

describe('index card Edit actions', () => {
  beforeEach(() => push.mockReset());

  it('opens firearm editing directly', async () => {
    const wrapper = mount(FirearmCard, {
      props: {
        firearm: {
          id: 1,
          label: 'Nightstand',
          manufacturer: 'Glock',
          model: '19',
          calibers: [],
          mounted_accessories: [],
          rounds_fired: 0,
        },
      },
    });

    await wrapper.get('[aria-label="Edit Nightstand"]').trigger('click');

    expect(push).toHaveBeenCalledWith({ name: 'FirearmsEdit', params: { firearm_id: 1 } });
  });

  it('opens ammunition editing directly', async () => {
    const wrapper = mount(AmmoCard, {
      props: {
        ammo: {
          id: 2,
          label: 'American Eagle',
          manufacturer: 'Federal',
          on_hand: 100,
          weight: 124,
          bullet_type: { label: 'FMJ' },
        },
      },
    });

    expect(wrapper.text()).toContain('American Eagle · 124gr · FMJ');

    await wrapper.get('[aria-label="Edit American Eagle"]').trigger('click');

    expect(push).toHaveBeenCalledWith({ name: 'AmmoEdit', params: { ammunition_id: 2 } });
  });

  it('opens training editing directly without opening details', async () => {
    const wrapper = mount(TrainingCard, {
      props: {
        session: {
          id: 3,
          label: 'Range Day',
          session_date: '2026-07-18',
          total_rounds: 50,
          firearms_used: [],
        },
      },
    });

    await wrapper.get('[aria-label="Edit Range Day"]').trigger('click');

    expect(push).toHaveBeenCalledOnce();
    expect(push).toHaveBeenCalledWith({ name: 'TrainingEdit', params: { training_id: 3 } });
  });
});
