import { describe, expect, it, vi, beforeEach } from 'vitest';
import { mount, flushPromises } from '@vue/test-utils';

const fetchOne = vi.fn();
const update = vi.fn();

vi.mock('@/stores/optics', () => ({
  useOpticsStore: () => ({ fetchOne, update }),
}));

import OpticShow from '@/pages/accessories/optics/OpticShow.vue';

const optic = {
  id: 1,
  type: 'optic',
  label: 'Holosun 507c',
  manufacturer: 'Holosun',
  optic_type: 'red_dot',
  battery_type: 'CR1632',
  firearm_id: 7,
  firearm: { id: 7, label: 'Nightstand', manufacturer: 'Glock' },
  mounted_since: '2024-04-30',
  location: null,
  pictures_count: 0,
  thumbnail_urls: [],
};

async function mountShow() {
  fetchOne.mockResolvedValue({ data: optic });
  const wrapper = mount(OpticShow, {
    props: { opticId: 1 },
    global: {
      stubs: {
        'router-link': { template: '<a><slot /></a>' },
        AppBreadcrumb: true,
        AccessoryEventTimeline: true,
        MoveAccessoryModal: true,
      },
    },
  });
  await flushPromises();
  return wrapper;
}

function findButton(wrapper, text) {
  return wrapper.findAll('button').find((b) => b.text().includes(text));
}

describe('OpticShow', () => {
  beforeEach(() => {
    fetchOne.mockReset();
    update.mockReset();
  });

  it('renders the Move button and mounted-on detail', async () => {
    const wrapper = await mountShow();

    expect(findButton(wrapper, 'Move')).toBeTruthy();
    expect(wrapper.text()).toContain('MOUNTED ON');
    expect(wrapper.text()).toContain('Nightstand');
    expect(wrapper.text()).toContain('since Apr 30');
  });

  it('updates + refetches on move', async () => {
    update.mockResolvedValue({ data: optic });
    const wrapper = await mountShow();

    await findButton(wrapper, 'Move').trigger('click');
    await flushPromises();

    const modal = wrapper.findComponent({ name: 'MoveAccessoryModal' });
    expect(modal.exists()).toBe(true);

    fetchOne.mockClear();
    modal.vm.$emit('move', 9);
    await flushPromises();

    expect(update).toHaveBeenCalledWith(1, { firearm_id: 9 });
    expect(fetchOne).toHaveBeenCalledWith(1);
  });
});
