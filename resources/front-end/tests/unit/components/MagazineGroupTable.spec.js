import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import MagazineGroupTable from '@/components/magazines/MagazineGroupTable.vue';

const magazine = {
  id: 12,
  id_marking: 'GL9-01',
  capacity: 17,
  display_status: 'empty',
  loaded_rounds: 0,
  loaded_ammunition: null,
};

describe('MagazineGroupTable', () => {
  it('provides separate icon actions for details, state management, and editing', async () => {
    const wrapper = mount(MagazineGroupTable, {
      props: { magazines: [magazine] },
      global: {
        stubs: {
          'router-link': {
            props: ['to'],
            template: '<a :data-to="JSON.stringify(to)"><slot /></a>',
          },
        },
      },
    });

    await wrapper.get('[aria-label="Manage state"]').trigger('click');

    expect(wrapper.emitted('change-state')).toEqual([[magazine]]);
    expect(wrapper.get('[aria-label="View details"]').attributes('data-to')).toContain(
      'MagazinesShow'
    );
    expect(wrapper.get('[aria-label="Edit magazine"]').attributes('data-to')).toContain(
      'MagazinesEdit'
    );
  });
});
