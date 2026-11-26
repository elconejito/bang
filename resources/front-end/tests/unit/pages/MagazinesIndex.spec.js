import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';
import { reactive } from 'vue';
import MagazinesIndex from '@/pages/magazines/MagazinesIndex.vue';

const mocks = vi.hoisted(() => ({
  fetchAll: vi.fn(),
  fetchOne: vi.fn(),
  push: vi.fn(),
  routeState: { params: {}, query: {} },
}));
const route = reactive(mocks.routeState);

vi.mock('vue-router', () => ({
  useRoute: () => route,
  useRouter: () => ({ push: mocks.push }),
}));
vi.mock('@/stores/magazineGroups', () => ({
  useMagazineGroupsStore: () => ({ fetchAll: mocks.fetchAll }),
}));
vi.mock('@/stores/firearms', () => ({
  useFirearmsStore: () => ({ fetchOne: mocks.fetchOne }),
}));

const groups = [
  {
    key: 'glock-17',
    manufacturer: 'Glock',
    model_name: '17 round',
    capacity: 17,
    calibers: [{ label: '9mm' }],
    summary: { total: 7, in_gun: 1, loaded: 2, empty: 4 },
  },
  {
    key: 'magpul-d60',
    manufacturer: 'Magpul',
    model_name: 'D-60',
    capacity: 60,
    calibers: [{ label: '.223 / 5.56' }],
    summary: { total: 2, in_gun: 0, loaded: 0, empty: 2 },
  },
  {
    key: 'glock-33',
    manufacturer: ' GLOCK ',
    model_name: '33 round',
    capacity: 33,
    calibers: [{ label: '9mm' }],
    summary: { total: 13, in_gun: 0, loaded: 3, empty: 10 },
  },
];

function mountPage() {
  return mount(MagazinesIndex, {
    global: {
      stubs: {
        AppBreadcrumb: true,
        LoadingCard: true,
        ErrorCard: true,
        EmptyState: true,
        'router-link': {
          props: ['to'],
          template: '<a :data-route="to.name" :data-group="to.params?.group"><slot /></a>',
        },
      },
    },
  });
}

describe('MagazinesIndex manufacturer sections', () => {
  beforeEach(() => {
    mocks.routeState.params = {};
    mocks.routeState.query = {};
    mocks.push.mockReset();
    mocks.fetchOne.mockReset();
    mocks.fetchAll.mockReset().mockResolvedValue({
      data: groups,
      meta: { groups: 3, magazines: 22 },
    });
  });

  it('combines manufacturer spelling variants under one heading with aggregate counts', async () => {
    const wrapper = mountPage();
    await flushPromises();

    const sections = wrapper.findAll('[data-testid="magazine-manufacturer-section"]');

    expect(sections).toHaveLength(2);
    expect(sections.map((section) => section.attributes('data-manufacturer'))).toEqual([
      'Glock',
      'Magpul',
    ]);
    expect(sections[0].text()).toContain('2 TYPES · 20 MAGS');
    expect(sections[1].text()).toContain('1 TYPE · 2 MAGS');
  });

  it('keeps group links in the catalog order supplied by the API', async () => {
    const wrapper = mountPage();
    await flushPromises();

    const glockSection = wrapper.get('[data-manufacturer="Glock"]');
    const groupLinks = glockSection.findAll('[data-route="MagazineGroupShow"]');

    expect(groupLinks.map((link) => link.attributes('data-group'))).toEqual([
      'glock-17',
      'glock-33',
    ]);
    expect(wrapper.findAll('[data-route="MagazineGroupShow"]')).toHaveLength(3);
  });

  it('keeps the responsive card grid inside every manufacturer section', async () => {
    const wrapper = mountPage();
    await flushPromises();

    wrapper.findAll('[data-testid="magazine-manufacturer-section"]').forEach((section) => {
      expect(section.get('.grid').classes()).toEqual(
        expect.arrayContaining(['grid-cols-1', 'sm:grid-cols-2', 'lg:grid-cols-3'])
      );
    });
  });
});
