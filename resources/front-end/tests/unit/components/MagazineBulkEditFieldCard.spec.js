import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import MagazineBulkEditFieldCard from '@/components/magazines/MagazineBulkEditFieldCard.vue';

describe('MagazineBulkEditFieldCard', () => {
  it('shows status and reveals its control only when applied', async () => {
    const wrapper = mount(MagazineBulkEditFieldCard, {
      props: {
        name: 'label',
        title: 'Nickname',
        inputId: 'bulk-label',
        status: 'KEEP',
        summary: '2 distinct values across 2 magazines',
        apply: false,
      },
      slots: { default: '<input data-testid="field-control" />' },
    });

    expect(wrapper.text()).toContain('KEEP');
    expect(wrapper.find('[data-testid="field-control"]').exists()).toBe(false);

    await wrapper.get('#bulk-label-apply').setValue(true);

    expect(wrapper.emitted('update:apply')).toEqual([[true]]);
  });
});
