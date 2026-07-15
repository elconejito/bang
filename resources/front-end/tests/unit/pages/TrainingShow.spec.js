import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const fetchOne = vi.fn();

vi.mock('@/stores/training', () => ({
  useTrainingStore: () => ({ fetchOne, deleteTarget: vi.fn() }),
}));

import TrainingShow from '@/pages/training/TrainingShow.vue';

const RouterLinkStub = {
  props: ['to'],
  template: `
    <a :data-route-name="to.name" :data-route-id="to.params?.firearm_id ?? to.params?.ammunition_id">
      <slot />
    </a>
  `,
};

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
      firearm_id: 1,
      firearm: { id: 1, label: 'Nightstand', manufacturer: 'Glock', model: '19', calibers: [] },
      ammunition_id: 10,
      ammunition: { id: 10, manufacturer: 'Federal', label: 'American Eagle' },
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
          'router-link': RouterLinkStub,
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
    expect(wrapper.get('[data-route-name="FirearmsShow"][data-route-id="1"]').text()).toContain(
      'Nightstand'
    );
    expect(wrapper.get('[data-route-name="AmmoShow"][data-route-id="10"]').text()).toContain(
      'Federal American Eagle'
    );

    await wrapper
      .findAll('button')
      .find((button) => button.text().includes('Notes'))
      .trigger('click');

    expect(wrapper.text()).toContain('session-lines-9');
  });

  it('totals firearm counts and ammunition deductions by item', async () => {
    fetchOne.mockResolvedValueOnce({
      data: {
        ...session,
        total_rounds: 145,
        firearms_count: 2,
        lines: [
          session.lines[0],
          { ...session.lines[0], id: 10, rounds: 25 },
          {
            ...session.lines[0],
            id: 11,
            firearm_id: 2,
            firearm: {
              id: 2,
              label: 'Competition Rifle',
              manufacturer: 'Daniel Defense',
              model: 'DDM4',
              calibers: [],
            },
            ammunition_id: 11,
            ammunition: { id: 11, manufacturer: 'IMI', label: 'Razor Core' },
            rounds: 30,
          },
          {
            ...session.lines[0],
            id: 12,
            rounds: 40,
            deduct_ammo: false,
            add_firearm_count: false,
          },
        ],
      },
    });

    const wrapper = mount(TrainingShow, {
      props: { trainingId: 5 },
      global: {
        stubs: {
          'router-link': RouterLinkStub,
          AppBreadcrumb: true,
          AddSessionLineModal: true,
          EditSessionLineModal: true,
          AddTargetModal: true,
          NotesPanel: true,
        },
      },
    });
    await flushPromises();

    const firearmCounts = wrapper.findAll('[data-testid="firearm-count"]');
    const ammoDeductions = wrapper.findAll('[data-testid="ammo-deduction"]');

    expect(firearmCounts).toHaveLength(2);
    expect(firearmCounts[0].text()).toContain('Nightstand+75');
    expect(firearmCounts[1].text()).toContain('Competition Rifle+30');
    expect(ammoDeductions).toHaveLength(2);
    expect(ammoDeductions[0].text()).toContain('American Eagle−75');
    expect(ammoDeductions[1].text()).toContain('Razor Core−30');
  });
});
