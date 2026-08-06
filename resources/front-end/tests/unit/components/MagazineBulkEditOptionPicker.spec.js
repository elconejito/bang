import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import MagazineBulkEditOptionPicker from '@/components/magazines/MagazineBulkEditOptionPicker.vue';

const options = [
  { id: 1, label: '9mm' },
  { id: 2, label: '.45 ACP' },
];

describe('MagazineBulkEditOptionPicker', () => {
  it('filters options and selects multiple values with the keyboard', async () => {
    const wrapper = mount(MagazineBulkEditOptionPicker, {
      props: {
        modelValue: [],
        options,
        multiple: true,
        label: 'Calibers',
        inputId: 'caliber-picker',
      },
    });
    const input = wrapper.get('#caliber-picker');

    await input.trigger('focus');
    await input.setValue('45');
    await input.trigger('keydown', { key: 'Enter' });

    expect(wrapper.emitted('update:modelValue')).toEqual([[['2']]]);
  });

  it('closes its list on Escape without bubbling to the dialog', async () => {
    const wrapper = mount(MagazineBulkEditOptionPicker, {
      props: {
        modelValue: [],
        options,
        multiple: true,
        label: 'Calibers',
        inputId: 'caliber-picker',
      },
    });
    const input = wrapper.get('#caliber-picker');
    await input.trigger('focus');

    const event = new KeyboardEvent('keydown', { key: 'Escape', bubbles: true, cancelable: true });
    input.element.dispatchEvent(event);
    await wrapper.vm.$nextTick();

    expect(event.defaultPrevented).toBe(true);
    expect(wrapper.find('[role="listbox"]').exists()).toBe(false);
  });
});
