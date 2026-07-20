import { beforeEach, describe, expect, it, vi } from 'vitest';
import { mount } from '@vue/test-utils';

const { push } = vi.hoisted(() => ({ push: vi.fn() }));

vi.mock('vue-router', () => ({
  useRouter: () => ({ push }),
}));

import FirearmTable from '@/components/firearms/FirearmTable.vue';

const firearm = {
  id: 7,
  label: 'Nightstand',
  manufacturer: 'Glock',
  model: '19',
  customizer: 'Agency Arms',
  calibers: [{ id: 1, label: '9mm' }],
  mounted_accessories: [
    { id: 2, type: 'Optic', label: 'Holosun 507C' },
    { id: 3, type: 'Suppressor', label: 'Omega 9K' },
    { id: 4, type: 'Light', label: 'TLR-7A' },
    { id: 5, type: 'Misc', label: 'Magwell' },
  ],
  location: { id: 2, label: 'Bedroom Safe' },
  rounds_fired: 1234,
};

describe('FirearmTable', () => {
  beforeEach(() => {
    push.mockReset();
  });

  it('renders the card fields without an image', () => {
    const wrapper = mount(FirearmTable, { props: { firearms: [firearm] } });

    expect(wrapper.text()).toContain('Nightstand');
    expect(wrapper.text()).toContain('Glock · 19');
    expect(wrapper.text()).toContain('Customized by Agency Arms');
    expect(wrapper.text()).toContain('9mm');
    expect(wrapper.text()).toContain('Bedroom Safe');
    expect(wrapper.text()).toContain('1,234');
    expect(wrapper.find('img').exists()).toBe(false);
  });

  it('renders the firearm name as a brass detail link while preserving row navigation', () => {
    const wrapper = mount(FirearmTable, { props: { firearms: [firearm] } });
    const title = wrapper.findAll('router-link').find((link) => link.text() === 'Nightstand');

    expect(title.classes()).toEqual(
      expect.arrayContaining([
        'text-brass-800',
        'hover:text-[#5f4b18]',
        'visited:text-brass-800',
        'focus-visible:text-[#5f4b18]',
      ])
    );
  });

  it('preserves mounted accessory badges and suppressor styling', () => {
    const wrapper = mount(FirearmTable, { props: { firearms: [firearm] } });

    expect(wrapper.text()).toContain('OPTIC');
    expect(wrapper.text()).toContain('SUPPR');
    expect(wrapper.text()).toContain('LIGHT');
    expect(wrapper.text()).toContain('MISC');
    expect(wrapper.get('[title="Omega 9K"]').classes()).toContain('bg-special-bg');
  });

  it('opens firearm details from both the row and Log action', async () => {
    const wrapper = mount(FirearmTable, { props: { firearms: [firearm] } });
    const destination = { name: 'FirearmsShow', params: { firearm_id: firearm.id } };

    await wrapper.get('[role="link"]').trigger('click');
    expect(push).toHaveBeenLastCalledWith(destination);

    push.mockClear();
    await wrapper.get('[aria-label="Log activity for Nightstand"]').trigger('click');
    expect(push).toHaveBeenCalledOnce();
    expect(push).toHaveBeenLastCalledWith(destination);
  });

  it('opens the edit screen directly from the Edit action', async () => {
    const wrapper = mount(FirearmTable, { props: { firearms: [firearm] } });

    await wrapper.get('[aria-label="Edit Nightstand"]').trigger('click');

    expect(push).toHaveBeenCalledOnce();
    expect(push).toHaveBeenCalledWith({
      name: 'FirearmsEdit',
      params: { firearm_id: firearm.id },
    });
  });

  it('preserves loading and empty states when table view is selected', () => {
    const loadingWrapper = mount(FirearmTable, {
      props: { firearms: [], isLoading: true },
    });
    expect(loadingWrapper.find('.animate-pulse').exists()).toBe(true);

    const emptyWrapper = mount(FirearmTable, {
      props: {
        firearms: [],
        emptyTitle: 'No firearms yet',
        emptyMessage: 'Add your first firearm.',
      },
      global: {
        stubs: {
          EmptyState: {
            props: ['title', 'message'],
            template: '<div>{{ title }} {{ message }}</div>',
          },
        },
      },
    });
    expect(emptyWrapper.text()).toContain('No firearms yet');
    expect(emptyWrapper.text()).toContain('Add your first firearm.');
  });

  it('shows archived status and replaces the Log action with View', () => {
    const wrapper = mount(FirearmTable, {
      props: {
        firearms: [{ ...firearm, status: 'archived', archive_reason: 'sold' }],
      },
    });

    expect(wrapper.text()).toContain('Archived · Sold');
    expect(wrapper.get('[aria-label="View Nightstand"]').text()).toBe('View');
    expect(wrapper.find('[aria-label="Edit Nightstand"]').exists()).toBe(false);
  });
});
