import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import AccessoriesTable from '@/components/accessories/AccessoriesTable.vue';

const RouterLink = {
  props: ['to'],
  template: '<a :data-route="to.name" :data-params="JSON.stringify(to.params)"><slot /></a>',
};

describe('AccessoriesTable', () => {
  it('renders accessory details and preserves detail and mount routes', () => {
    const wrapper = mount(AccessoriesTable, {
      props: {
        type: 'suppressors',
        items: [
          {
            id: 3,
            label: 'Omega 9K',
            manufacturer: 'SilencerCo',
            serial: 'ABC1234',
            is_nfa: true,
            caliber: { label: '9mm' },
            mount_type: 'Direct thread',
            location: { label: 'Safe' },
          },
        ],
      },
      global: { stubs: { 'router-link': RouterLink } },
    });

    expect(wrapper.text()).toContain('Omega 9K');
    expect(wrapper.text()).toContain('9mm · Direct thread');
    expect(wrapper.text()).toContain('OFF · Safe');
    expect(wrapper.text()).toContain('NFA');
    expect(wrapper.get('[data-route="SuppressorShow"]').attributes('data-params')).toContain('3');
    expect(wrapper.get('[data-route="SuppressorEdit"]').text()).toContain('Mount');
  });

  it('preserves magazine group navigation and status summary', () => {
    const wrapper = mount(AccessoriesTable, {
      props: {
        type: 'magazines',
        items: [
          {
            key: 'opaque-key',
            model_name: 'PMAG',
            manufacturer: 'Magpul',
            calibers: [{ label: '5.56 NATO' }],
            capacity: 30,
            summary: { total: 4, in_gun: 1, loaded: 2, empty: 1 },
          },
        ],
      },
      global: { stubs: { 'router-link': RouterLink } },
    });

    expect(wrapper.text()).toContain('1 in firearm · 2 loaded · 1 empty');
    const links = wrapper.findAll('[data-route="MagazineGroupShow"]');
    expect(links).toHaveLength(2);
    expect(links[0].attributes('data-params')).toContain('opaque-key');
  });

  it('uses Move and Edit actions for mounted and fitted accessories', () => {
    const wrapper = mount(AccessoriesTable, {
      props: {
        type: 'misc',
        items: [
          { id: 1, label: 'Grip', manufacturer: 'BCM', firearm: { label: 'Rifle' } },
          { id: 2, label: 'Holster', manufacturer: 'Safariland', sub_type: 'holster' },
        ],
      },
      global: { stubs: { 'router-link': RouterLink } },
    });

    expect(wrapper.text()).toContain('ON · Rifle');
    expect(wrapper.text()).toContain('Move');
    expect(wrapper.text()).toContain('FITS · Unassigned');
    expect(wrapper.text()).toContain('Edit');
  });
});
