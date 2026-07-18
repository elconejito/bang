import { describe, expect, it, vi, beforeEach } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const fetchMountableAccessories = vi.fn();
const mountAccessories = vi.fn();

vi.mock('@/stores/firearms', () => ({
  useFirearmsStore: () => ({ fetchMountableAccessories, mountAccessories }),
}));

import MountAccessoriesModal from '@/components/firearms/MountAccessoriesModal.vue';

describe('MountAccessoriesModal', () => {
  beforeEach(() => {
    fetchMountableAccessories.mockReset();
    mountAccessories.mockReset();
  });

  it('groups searchable candidates and submits the selected mount tokens', async () => {
    fetchMountableAccessories.mockResolvedValue({
      data: [
        { type: 'Optic', id: 4, label: '507C', subtitle: 'Holosun · Optic' },
        { type: 'Light', id: 7, label: 'X300', subtitle: 'SureFire · Light' },
      ],
    });
    mountAccessories.mockResolvedValue();
    const wrapper = mount(MountAccessoriesModal, { props: { firearmId: 9 } });
    await flushPromises();

    expect(wrapper.text()).toContain('OPTICS');
    expect(wrapper.text()).toContain('LIGHTS');
    await wrapper.findAll('input[type="checkbox"]')[0].setValue(true);
    await wrapper.get('input[placeholder="Search accessories"]').setValue('507');
    expect(wrapper.text()).toContain('507C');
    expect(wrapper.text()).not.toContain('X300');

    await wrapper
      .findAll('button')
      .find((button) => button.text().includes('Mount selected'))
      .trigger('click');
    await flushPromises();

    expect(mountAccessories).toHaveBeenCalledWith(9, [{ type: 'Optic', id: 4 }]);
    expect(wrapper.emitted('mounted')).toHaveLength(1);
  });

  it('shows an empty state and Add new accessory affordance', async () => {
    fetchMountableAccessories.mockResolvedValue({ data: [] });
    const wrapper = mount(MountAccessoriesModal, { props: { firearmId: 9 } });
    await flushPromises();

    expect(wrapper.text()).toContain('No unmounted accessories available');
    await wrapper
      .findAll('button')
      .find((button) => button.text().includes('Add new accessory'))
      .trigger('click');
    expect(wrapper.emitted('add-new')).toHaveLength(1);
  });
});
