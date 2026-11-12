import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const fetchAll = vi.fn();
const create = vi.fn();

vi.mock('@/stores/notes', () => ({
  useNotesStore: () => ({ fetchAll, create }),
}));

import NotesPanel from '@/components/notes/NotesPanel.vue';

const firstPage = {
  data: [
    {
      id: 2,
      note: '<img src=x onerror=alert(1)>Newest note',
      created_at: '2026-07-13T12:00:00.000000Z',
      updated_at: '2026-07-13T12:00:00.000000Z',
    },
    {
      id: 1,
      note: 'Older note',
      created_at: '2026-07-12T12:00:00.000000Z',
      updated_at: '2026-07-12T12:00:00.000000Z',
    },
  ],
  meta: { current_page: 1, last_page: 2, per_page: 10, from: 1, to: 2, total: 12 },
};

async function mountPanel({ props = {}, response = firstPage } = {}) {
  fetchAll.mockResolvedValue(response);
  const wrapper = mount(NotesPanel, {
    props: { entityType: 'firearms', entityId: 7, ...props },
  });
  await flushPromises();
  return wrapper;
}

describe('NotesPanel', () => {
  beforeEach(() => {
    fetchAll.mockReset();
    create.mockReset();
  });

  it('loads newest-first notes and renders note content as text', async () => {
    const wrapper = await mountPanel();

    expect(fetchAll).toHaveBeenCalledWith('firearms', 7, {
      page: 1,
      per_page: 10,
      search: undefined,
    });
    expect(wrapper.text()).toContain('Newest note');
    expect(wrapper.html()).toContain('&lt;img src=x onerror=alert(1)&gt;Newest note');
    expect(wrapper.find('article img').exists()).toBe(false);
  });

  it('creates a note and reloads the first page', async () => {
    const wrapper = await mountPanel();
    create.mockResolvedValue({ data: { id: 3, note: 'Fresh note' } });
    fetchAll.mockClear();

    await wrapper
      .findAll('button')
      .find((button) => button.text().includes('Add Note'))
      .trigger('click');
    await wrapper.find('textarea').setValue('Fresh note');
    await wrapper
      .findAll('button')
      .find((button) => button.text().includes('Save Note'))
      .trigger('click');
    await flushPromises();

    expect(create).toHaveBeenCalledWith('firearms', 7, { note: 'Fresh note' });
    expect(fetchAll).toHaveBeenCalledWith('firearms', 7, expect.objectContaining({ page: 1 }));
  });

  it('searches after a short debounce and requests the next page', async () => {
    vi.useFakeTimers();
    const wrapper = await mountPanel();
    fetchAll.mockClear();

    await wrapper.find('input[type="search"]').setValue('cleaned');
    await vi.advanceTimersByTimeAsync(250);
    await flushPromises();

    expect(fetchAll).toHaveBeenCalledWith(
      'firearms',
      7,
      expect.objectContaining({ page: 1, search: 'cleaned' })
    );

    await wrapper.find('button[aria-label="Next notes page"]').trigger('click');
    expect(fetchAll).toHaveBeenLastCalledWith(
      'firearms',
      7,
      expect.objectContaining({ page: 2, search: 'cleaned' })
    );
    vi.useRealTimers();
  });

  it('hides search when configured and the entity has no notes', async () => {
    const wrapper = await mountPanel({
      props: { entityType: 'ammunition', entityId: 7, hideEmptySearch: true },
      response: {
        data: [],
        meta: { current_page: 1, last_page: 1, per_page: 10, total: 0 },
      },
    });

    expect(wrapper.find('input[type="search"]').exists()).toBe(false);
    expect(wrapper.text()).toContain('Add Note');
  });

  it('shows search when configured and notes exist', async () => {
    const wrapper = await mountPanel({
      props: { entityType: 'ammunition', entityId: 7, hideEmptySearch: true },
    });

    expect(wrapper.find('input[type="search"]').exists()).toBe(true);
  });

  it('keeps search available after a search returns no notes', async () => {
    vi.useFakeTimers();
    fetchAll
      .mockResolvedValueOnce(firstPage)
      .mockResolvedValueOnce({
        data: [],
        meta: { current_page: 1, last_page: 1, per_page: 10, total: 0 },
      })
      .mockResolvedValueOnce(firstPage);
    const wrapper = mount(NotesPanel, {
      props: { entityType: 'ammunition', entityId: 7, hideEmptySearch: true },
    });
    await flushPromises();

    await wrapper.find('input[type="search"]').setValue('missing');
    await vi.advanceTimersByTimeAsync(250);
    await flushPromises();

    expect(wrapper.find('input[type="search"]').exists()).toBe(true);
    expect(wrapper.text()).toContain('No notes match your search');

    await wrapper.find('input[type="search"]').setValue('');
    await vi.advanceTimersByTimeAsync(250);
    await flushPromises();

    expect(wrapper.find('input[type="search"]').exists()).toBe(true);
    vi.useRealTimers();
  });
});
