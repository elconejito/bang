import { describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';
import { createMemoryHistory, createRouter } from 'vue-router';
import EntityLifecycleActions from '@/components/archive/EntityLifecycleActions.vue';

function buildRouter() {
  return createRouter({
    history: createMemoryHistory(),
    routes: [
      { path: '/', name: 'index', component: { template: '<div />' } },
      { path: '/items', name: 'items', component: { template: '<div />' } },
    ],
  });
}

function mountActions(overrides = {}) {
  const archiveAction = vi.fn().mockResolvedValue({ data: { id: 1, status: 'archived' } });
  const unarchiveAction = vi.fn().mockResolvedValue({ data: { id: 1, status: 'active' } });
  const destroyAction = vi.fn().mockResolvedValue(undefined);
  const wrapper = mount(EntityLifecycleActions, {
    props: {
      entityId: 1,
      entityLabel: 'Test optic',
      status: 'active',
      archiveAction,
      unarchiveAction,
      destroyAction,
      returnRoute: { name: 'items' },
      ...overrides,
    },
    global: { plugins: [buildRouter()] },
  });

  return { wrapper, archiveAction, unarchiveAction };
}

describe('EntityLifecycleActions', () => {
  it('archives with a reason and emits the updated item', async () => {
    const { wrapper, archiveAction } = mountActions();

    await wrapper.get('button').trigger('click');
    wrapper.findComponent({ name: 'ArchiveEntityModal' }).vm.$emit('archive', {
      reason: 'sold',
      description: 'Sold to a friend',
    });
    await flushPromises();

    expect(archiveAction).toHaveBeenCalledWith(1, {
      reason: 'sold',
      description: 'Sold to a friend',
    });
    expect(wrapper.emitted('updated')).toEqual([[{ id: 1, status: 'archived' }]]);
    expect(wrapper.emitted('activity-changed')).toHaveLength(1);
  });

  it('offers unarchive and permanent delete only for archived items', async () => {
    const { wrapper, unarchiveAction } = mountActions({ status: 'archived' });

    expect(wrapper.text()).toContain('Unarchive');
    expect(wrapper.text()).toContain('Delete permanently');
    await wrapper
      .findAll('button')
      .find((button) => button.text().includes('Unarchive'))
      .trigger('click');
    await flushPromises();

    expect(unarchiveAction).toHaveBeenCalledWith(1);
    expect(wrapper.emitted('updated')).toEqual([[{ id: 1, status: 'active' }]]);
  });
});
