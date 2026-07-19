import { describe, expect, it, vi, beforeEach } from 'vitest';
import { mount, flushPromises } from '@vue/test-utils';

const fetchForEntity = vi.fn();

vi.mock('@/stores/accessoryEvents', () => ({
  useAccessoryEventsStore: () => ({ fetchForEntity, createForEntity: vi.fn() }),
}));

import AccessoryEventTimeline from '@/components/history/AccessoryEventTimeline.vue';

const entries = [
  {
    id: 'event-5',
    type: 'LOCATION',
    group: 'location',
    date: '2024-06-03',
    title: 'Moved to Main safe',
    subtitle: 'Storage location updated',
  },
  {
    id: 'event-4',
    type: 'ARCHIVED',
    group: 'lifecycle',
    date: '2024-06-02',
    title: 'Archived',
    subtitle: 'sold',
  },
  {
    id: 'range-1',
    type: 'RANGE',
    group: 'range',
    date: '2024-06-01',
    title: '+250 rounds · on Nightstand',
    subtitle: 'Running total → 250 rounds',
  },
  {
    id: 'event-2',
    type: 'MOUNT',
    group: 'mount',
    date: '2024-04-30',
    title: 'Mounted on Nightstand',
    subtitle: 'Moved from Range Toy',
  },
  {
    id: 'event-3',
    type: 'CLEAN',
    group: 'maintenance',
    date: '2024-04-15',
    title: 'Cleaned',
    subtitle: 'At 1,640 rounds',
  },
];

// Simulate the server: filter by group, sort by date, paginate.
function serverRespond(_type, _id, params = {}) {
  let rows = [...entries];
  if (params['filter[group]']) {
    rows = rows.filter((e) => e.group === params['filter[group]']);
  }
  const asc = params.sort === 'date';
  rows.sort((a, b) => (asc ? a.date.localeCompare(b.date) : b.date.localeCompare(a.date)));
  const perPage = params.per_page ?? 8;
  const page = params.page ?? 1;
  const total = rows.length;
  return Promise.resolve({
    data: rows.slice((page - 1) * perPage, page * perPage),
    meta: {
      current_page: page,
      per_page: perPage,
      total,
      last_page: Math.max(Math.ceil(total / perPage), 1),
      range_count: entries.filter((e) => e.group === 'range').length,
      mount_count: entries.filter((e) => e.group === 'mount').length,
    },
  });
}

function findButton(wrapper, text) {
  return wrapper.findAll('button').find((b) => b.text().includes(text));
}

async function mountTimeline(props = {}) {
  fetchForEntity.mockImplementation(serverRespond);
  const wrapper = mount(AccessoryEventTimeline, {
    props: {
      entityType: 'suppressors',
      entityId: 1,
      manualEventTypes: [{ value: 'CLEAN', label: 'Cleaning' }],
      ...props,
      historyLabel: 'MOUNTS · ROUNDS · MAINTENANCE',
    },
    global: { stubs: { LogEventModal: true } },
  });
  await flushPromises();
  return wrapper;
}

describe('AccessoryEventTimeline', () => {
  beforeEach(() => fetchForEntity.mockReset());

  it('renders merged entries with type badges and the filter/sort controls', async () => {
    const wrapper = await mountTimeline();

    expect(wrapper.text()).toContain('RANGE');
    expect(wrapper.text()).toContain('Mounted on Nightstand');
    expect(wrapper.text()).toContain('At 1,640 rounds');
    expect(findButton(wrapper, 'All')).toBeTruthy();
    expect(findButton(wrapper, 'Newest')).toBeTruthy();
  });

  it('uses the approved neutral palette for location activity', async () => {
    const wrapper = await mountTimeline();
    const expectedLocationClasses = ['bg-ink-50', 'border-ink-300', 'text-ink-700'];

    expect(wrapper.get('[data-testid="event-node-LOCATION"]').classes()).toEqual(
      expect.arrayContaining(expectedLocationClasses)
    );
    expect(wrapper.get('[data-testid="event-badge-LOCATION"]').classes()).toEqual(
      expect.arrayContaining(expectedLocationClasses)
    );
  });

  it('triggers a fresh server call filtered by group, resetting to page 1', async () => {
    const wrapper = await mountTimeline();
    fetchForEntity.mockClear();

    await findButton(wrapper, 'All').trigger('click');
    await findButton(wrapper, 'Range').trigger('click');
    await flushPromises();

    expect(fetchForEntity).toHaveBeenCalledWith(
      'suppressors',
      1,
      expect.objectContaining({ 'filter[group]': 'range', page: 1 })
    );
    expect(wrapper.text()).toContain('+250 rounds');
    expect(wrapper.text()).not.toContain('Cleaned');
  });

  it('triggers a fresh server call with reversed sort', async () => {
    const wrapper = await mountTimeline();
    fetchForEntity.mockClear();

    await findButton(wrapper, 'Newest').trigger('click');
    await flushPromises();

    expect(fetchForEntity).toHaveBeenCalledWith(
      'suppressors',
      1,
      expect.objectContaining({ sort: 'date', page: 1 })
    );
    expect(findButton(wrapper, 'Oldest')).toBeTruthy();
  });

  it('supports lifecycle filtering and hides manual logging for archived items', async () => {
    const wrapper = await mountTimeline({ allowLogging: false });

    expect(findButton(wrapper, 'Log')).toBeFalsy();
    await findButton(wrapper, 'All').trigger('click');
    await findButton(wrapper, 'Lifecycle').trigger('click');
    await flushPromises();

    expect(fetchForEntity).toHaveBeenCalledWith(
      'suppressors',
      1,
      expect.objectContaining({ 'filter[group]': 'lifecycle', page: 1 })
    );
    expect(wrapper.text()).toContain('Archived');
  });
});
