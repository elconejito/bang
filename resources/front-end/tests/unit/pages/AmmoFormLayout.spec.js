import { flushPromises, mount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vitest';

const fetchOne = vi.fn();

vi.mock('vue-router', () => ({
  useRoute: () => ({ query: {} }),
  useRouter: () => ({ push: vi.fn() }),
}));

vi.mock('@/stores/ammunition', () => ({
  useAmmunitionStore: () => ({ fetchOne }),
}));

import AmmoCreate from '@/pages/ammunition/AmmoCreate.vue';
import AmmoEdit from '@/pages/ammunition/AmmoEdit.vue';

const ammo = { id: 4, label: 'American Eagle' };
const pageShell = 'mx-auto max-w-[640px] px-4 py-6 pb-16 sm:px-8';
const cardShell = 'rounded border border-line bg-white p-6';

function mountPage(component, props = {}) {
  return mount(component, {
    props,
    global: {
      stubs: {
        AppBreadcrumb: true,
        PageHeader: { props: ['title'], template: '<h1>{{ title }}</h1>' },
        AmmoFormCard: {
          name: 'AmmoFormCard',
          props: ['ammo', 'preselectedCaliberId'],
          template: '<form />',
        },
      },
    },
  });
}

describe('Ammo create and edit form layout', () => {
  it('uses the same centered shell and card after the edit data loads', async () => {
    fetchOne.mockResolvedValue({ data: ammo });

    const create = mountPage(AmmoCreate);
    const edit = mountPage(AmmoEdit, { ammunitionId: ammo.id });
    await flushPromises();

    expect(create.classes()).toEqual(expect.arrayContaining(pageShell.split(' ')));
    expect(edit.classes()).toEqual(expect.arrayContaining(pageShell.split(' ')));
    expect(create.find('div.rounded').classes()).toEqual(
      expect.arrayContaining(cardShell.split(' '))
    );
    expect(edit.find('div.rounded').classes()).toEqual(
      expect.arrayContaining(cardShell.split(' '))
    );
    expect(edit.getComponent({ name: 'AmmoFormCard' }).props('ammo')).toEqual(ammo);
    expect(edit.text()).toContain('Edit American Eagle');
  });
});
