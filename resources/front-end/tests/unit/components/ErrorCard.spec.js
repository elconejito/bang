import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import { defineComponent } from 'vue';
import ErrorCard from '@/components/status/ErrorCard.vue';

describe('ErrorCard', () => {
  it('announces a semantic failure, lists field errors, and exposes an optional retry action', async () => {
    const error = Object.assign(new Error('Unable to load ammunition.'), {
      response: { data: { message: 'Unable to load ammunition.' } },
      errorBag: {
        caliber: ['Choose a caliber.'],
        label: ['A label is required.'],
      },
    });
    const wrapper = mount(ErrorCard, {
      props: { error, retryLabel: 'Try again' },
    });

    const alert = wrapper.get('[role="alert"]');
    expect(alert.attributes('aria-live')).toBe('assertive');
    expect(alert.classes()).toEqual(
      expect.arrayContaining(['border-caution-border', 'bg-caution-bg', 'text-caution'])
    );
    expect(alert.get('h2').text()).toBe('Something went wrong');
    expect(alert.attributes('aria-labelledby')).toBe(alert.get('h2').attributes('id'));
    expect(alert.text()).toContain('Unable to load ammunition.');
    expect(alert.findAll('li')).toHaveLength(2);
    expect(alert.get('button').attributes('type')).toBe('button');

    await alert.get('button').trigger('click');
    expect(wrapper.emitted('retry')).toHaveLength(1);
  });

  it('uses a unique accessible heading relationship for each alert', () => {
    const wrapper = mount(
      defineComponent({
        components: { ErrorCard },
        template: '<div><ErrorCard /><ErrorCard /></div>',
      })
    );
    const alerts = wrapper.findAll('[role="alert"]');

    expect(alerts[0].attributes('aria-labelledby')).not.toBe(
      alerts[1].attributes('aria-labelledby')
    );
  });
});
