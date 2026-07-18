import { describe, expect, it, vi, beforeEach } from 'vitest';
import { mount, flushPromises } from '@vue/test-utils';

const fetchOne = vi.fn();
const fetchActivity = vi.fn();
const archive = vi.fn();
const unarchive = vi.fn();
const destroy = vi.fn();
const push = vi.fn();

vi.mock('vue-router', async () => {
  const actual = await vi.importActual('vue-router');
  return { ...actual, useRouter: () => ({ push }) };
});

vi.mock('@/stores/firearms', () => ({
  useFirearmsStore: () => ({ fetchOne, fetchActivity, archive, unarchive, destroy }),
}));

import FirearmsShow from '@/pages/firearms/FirearmsShow.vue';

const firearm = {
  id: 1,
  manufacturer: 'Glock',
  model: 'G19',
  customizer: 'Langdon Tactical',
  custom_package: 'LTT Trigger Job',
  label: 'My G19',
  status: 'active',
  calibers: [{ id: 1, label: '9mm' }],
  primary_photo_url: null,
  mounted_accessories: [
    { id: 5, type: 'Suppressor', label: 'Omega 9K', subtitle: 'Suppressor', is_nfa: true },
    { id: 6, type: 'Optic', label: 'Holosun 507c', subtitle: 'Red dot optic', is_nfa: false },
  ],
  compatible_magazines_count: 6,
};

const entries = [
  {
    type: 'RANGE',
    date: '2024-06-01',
    title: '50 rounds · Range Day',
    subtitle: 'Indoor',
    session_id: 10,
    event_id: null,
  },
  {
    type: 'MOUNT',
    date: '2024-02-01',
    title: 'Mounted Silencer',
    subtitle: null,
    session_id: null,
    event_id: 20,
  },
];

// Simulate the server: filter by type, sort by date, paginate.
function serverRespond(_id, params = {}) {
  let rows = [...entries];
  if (params['filter[type]']) {
    rows = rows.filter((e) => e.type === params['filter[type]']);
  }
  const asc = params.sort === 'date';
  rows.sort((a, b) => (asc ? a.date.localeCompare(b.date) : b.date.localeCompare(a.date)));
  const perPage = params.per_page ?? 10;
  const page = params.page ?? 1;
  const total = rows.length;
  return Promise.resolve({
    data: rows.slice((page - 1) * perPage, page * perPage),
    meta: {
      current_page: page,
      per_page: perPage,
      total,
      last_page: Math.max(Math.ceil(total / perPage), 1),
      range_count: entries.filter((e) => e.type === 'RANGE').length,
      last_session_date: '2024-06-01',
    },
  });
}

function findButton(wrapper, text) {
  return wrapper.findAll('button').find((b) => b.text().includes(text));
}

async function mountShow(firearmData = firearm) {
  fetchOne.mockResolvedValue({ data: firearmData });
  fetchActivity.mockImplementation(serverRespond);
  const wrapper = mount(FirearmsShow, {
    props: { firearmId: 1 },
    global: {
      stubs: {
        'router-link': { template: '<a><slot /></a>' },
        AppBreadcrumb: true,
        NotesPanel: true,
      },
    },
  });
  await flushPromises();
  return wrapper;
}

describe('FirearmsShow activity controls', () => {
  beforeEach(() => {
    fetchOne.mockReset();
    fetchActivity.mockReset();
    archive.mockReset();
    unarchive.mockReset();
    destroy.mockReset();
    push.mockReset();
  });

  it('renders the filter and sort controls and the activity entries', async () => {
    const wrapper = await mountShow();

    expect(findButton(wrapper, 'All')).toBeTruthy();
    expect(findButton(wrapper, 'Newest')).toBeTruthy();
    expect(wrapper.text()).toContain('Range Day');
    expect(wrapper.text()).toContain('Mounted Silencer');
  });

  it('triggers a fresh filtered API call when a type is chosen', async () => {
    const wrapper = await mountShow();
    fetchActivity.mockClear();

    await findButton(wrapper, 'All').trigger('click');
    await findButton(wrapper, 'MOUNT').trigger('click');
    await flushPromises();

    expect(fetchActivity).toHaveBeenCalledWith(
      1,
      expect.objectContaining({ 'filter[type]': 'MOUNT', page: 1 })
    );
    expect(wrapper.text()).toContain('Mounted Silencer');
    expect(wrapper.text()).not.toContain('Range Day');
  });

  it('renders the accessories section with mount action, mounted items and magazines link', async () => {
    const wrapper = await mountShow();

    // Mount action in the header
    expect(findButton(wrapper, 'Mount') || wrapper.text().includes('Mount')).toBeTruthy();
    expect(wrapper.text()).toContain('MOUNTED NOW');

    // Mounted accessory rows with descriptors and NFA badge
    expect(wrapper.text()).toContain('Omega 9K');
    expect(wrapper.text()).toContain('Suppressor');
    expect(wrapper.text()).toContain('NFA');
    expect(wrapper.text()).toContain('Holosun 507c');
    expect(wrapper.text()).toContain('Red dot optic');

    // Compatible magazines link with count
    expect(wrapper.text()).toContain('Compatible magazines');
    expect(wrapper.text()).toContain('6');

    // Holsters are not a built concept and must not appear
    expect(wrapper.text()).not.toContain('Holsters');
  });

  it('renders the customizer and custom package separately from the manufacturer and model', async () => {
    const wrapper = await mountShow();

    expect(wrapper.text()).toContain('Glock G19');
    expect(wrapper.text()).toContain('Customized by Langdon Tactical · LTT Trigger Job');
  });

  it('triggers a fresh API call with reversed sort when toggled', async () => {
    const wrapper = await mountShow();

    const newestHtml = wrapper.html();
    expect(newestHtml.indexOf('Range Day')).toBeLessThan(newestHtml.indexOf('Mounted Silencer'));
    fetchActivity.mockClear();

    await findButton(wrapper, 'Newest').trigger('click');
    await flushPromises();

    expect(fetchActivity).toHaveBeenCalledWith(
      1,
      expect.objectContaining({ sort: 'date', page: 1 })
    );
    expect(findButton(wrapper, 'Oldest')).toBeTruthy();
    const oldestHtml = wrapper.html();
    expect(oldestHtml.indexOf('Mounted Silencer')).toBeLessThan(oldestHtml.indexOf('Range Day'));
  });

  it('archives with a reason and selected accessories to unmount', async () => {
    archive.mockResolvedValue({
      data: {
        ...firearm,
        status: 'archived',
        archived_at: '2026-07-17T12:00:00Z',
        archive_reason: 'sold',
        archive_description: 'Sold to a friend.',
      },
    });
    const wrapper = await mountShow();

    await findButton(wrapper, 'Archive').trigger('click');
    await wrapper.get('#archive-reason').setValue('sold');
    await wrapper.get('#archive-description').setValue('Sold to a friend.');
    await wrapper.findAll('input[type="checkbox"]')[0].setValue(true);
    await findButton(wrapper, 'Archive firearm').trigger('click');
    await flushPromises();

    expect(archive).toHaveBeenCalledWith(1, {
      reason: 'sold',
      description: 'Sold to a friend.',
      unmount_accessories: [{ type: 'suppressor', id: 5 }],
    });
    expect(wrapper.text()).toContain('Archived — Sold');
    expect(wrapper.text()).toContain('Sold to a friend.');
  });

  it('unarchives an archived firearm and records a fresh activity request', async () => {
    const archivedFirearm = {
      ...firearm,
      status: 'archived',
      archived_at: '2026-07-17T12:00:00Z',
      archive_reason: 'repair',
      archive_description: 'Waiting on parts.',
    };
    unarchive.mockResolvedValue({ data: firearm });
    const wrapper = await mountShow(archivedFirearm);
    fetchActivity.mockClear();

    await findButton(wrapper, 'Unarchive').trigger('click');
    await flushPromises();

    expect(unarchive).toHaveBeenCalledWith(1);
    expect(fetchActivity).toHaveBeenCalled();
    expect(wrapper.text()).not.toContain('Archived — Repair');
  });
});
