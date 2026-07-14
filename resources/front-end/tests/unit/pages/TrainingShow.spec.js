import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const fetchOne = vi.fn();

vi.mock('@/stores/training', () => ({
  useTrainingStore: () => ({ fetchOne, deleteTarget: vi.fn() }),
}));

import TrainingShow from '@/pages/training/TrainingShow.vue';

const session = {
  id: 5,
  label: 'Practice session',
  description: 'Worked transitions and reloads.',
  session_date: '2026-07-13',
  range: null,
  total_rounds: 50,
  firearms_count: 1,
  ammo_cost: 15,
  target_count: 0,
  targets: [],
  lines: [
    {
      id: 9,
      firearm: { label: 'Nightstand', manufacturer: 'Glock', model: '19', calibers: [] },
      ammunition: { manufacturer: 'Federal', label: 'American Eagle' },
      suppressor: null,
      rounds: 50,
      deduct_ammo: true,
      add_firearm_count: true,
      add_suppressor_count: false,
      estimated_cost: 15,
    },
  ],
};

describe('TrainingShow notes', () => {
  beforeEach(() => {
    fetchOne.mockReset();
    fetchOne.mockResolvedValue({ data: session });
  });

  it('keeps the session summary distinct and expands notes for a session line', async () => {
    const wrapper = mount(TrainingShow, {
      props: { trainingId: 5 },
      global: {
        stubs: {
          'router-link': { template: '<a><slot /></a>' },
          AppBreadcrumb: true,
          AddSessionLineModal: true,
          EditSessionLineModal: true,
          AddTargetModal: true,
          NotesPanel: {
            props: ['entityType', 'entityId'],
            template: '<div class="notes-panel-stub">{{ entityType }}-{{ entityId }}</div>',
          },
        },
      },
    });
    await flushPromises();

    expect(wrapper.text()).toContain('Session summary');
    expect(wrapper.text()).toContain('training-5');
    expect(wrapper.text()).not.toContain('session-lines-9');

    await wrapper
      .findAll('button')
      .find((button) => button.text().includes('Notes'))
      .trigger('click');

    expect(wrapper.text()).toContain('session-lines-9');
  });
});
