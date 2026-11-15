import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const fetchAll = vi.fn();
const create = vi.fn();
const update = vi.fn();
const remove = vi.fn();

vi.mock('@/stores/notes', () => ({
  useNotesStore: () => ({ fetchAll, create, update, remove }),
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
    update.mockReset();
    remove.mockReset();
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
  it('edits in place, preserves creation date, and displays the update date', async () => {
    const wrapper = await mountPanel();
    const article = wrapper.findAll('article')[0];
    await article.find('[aria-label="Edit note"]').trigger('click');
    expect(article.find('textarea').element.value).toContain('Newest note');
    await article.find('textarea').setValue('Updated note');
    update.mockResolvedValue({
      data: {
        ...firstPage.data[0],
        note: 'Updated note',
        updated_at: '2026-07-14T12:00:00.000000Z',
      },
    });
    await article
      .findAll('button')
      .find((button) => button.text() === 'Save')
      .trigger('click');
    await flushPromises();
    expect(update).toHaveBeenCalledWith('firearms', 7, 2, { note: 'Updated note' });
    expect(article.text()).toContain('Updated note');
    expect(article.findAll('time').map((time) => time.attributes('datetime'))).toEqual([
      '2026-07-13T12:00:00.000000Z',
      '2026-07-14T12:00:00.000000Z',
    ]);
    expect(article.text()).toContain('Updated at');
  });

  it('cancels edits and prevents empty saves', async () => {
    const wrapper = await mountPanel();
    const article = wrapper.findAll('article')[0];
    await article.find('[aria-label="Edit note"]').trigger('click');
    await article.find('textarea').setValue('   ');
    expect(
      article
        .findAll('button')
        .find((button) => button.text() === 'Save')
        .attributes('disabled')
    ).toBeDefined();
    await article
      .findAll('button')
      .find((button) => button.text() === 'Cancel')
      .trigger('click');
    expect(article.text()).toContain('Newest note');
    expect(update).not.toHaveBeenCalled();
    expect(article.text()).not.toContain('Updated at');
  });

  it('keeps the draft and shows errors when saving fails', async () => {
    const wrapper = await mountPanel();
    const article = wrapper.findAll('article')[0];
    update.mockRejectedValue(new Error('offline'));
    await article.find('[aria-label="Edit note"]').trigger('click');
    await article.find('textarea').setValue('Keep this draft');
    await article
      .findAll('button')
      .find((button) => button.text() === 'Save')
      .trigger('click');
    await flushPromises();
    expect(article.find('textarea').element.value).toBe('Keep this draft');
    expect(article.find('[role="alert"]').text()).toContain('could not be saved');
  });

  it('requires confirmation and allows cancelling deletion', async () => {
    const wrapper = await mountPanel();
    const article = wrapper.findAll('article')[0];
    await article.find('[aria-label="Delete note"]').trigger('click');
    expect(article.text()).toContain('This cannot be undone');
    expect(remove).not.toHaveBeenCalled();
    await article
      .findAll('button')
      .find((button) => button.text() === 'Cancel')
      .trigger('click');
    expect(article.text()).toContain('Newest note');
    expect(remove).not.toHaveBeenCalled();
  });

  it('deletes a confirmed note and returns from an empty last page', async () => {
    const wrapper = await mountPanel();
    fetchAll.mockResolvedValueOnce({
      data: [firstPage.data[1]],
      meta: { ...firstPage.meta, current_page: 2 },
    });
    await wrapper.find('[aria-label="Next notes page"]').trigger('click');
    await flushPromises();
    remove.mockResolvedValue(undefined);
    fetchAll
      .mockResolvedValueOnce({ data: [], meta: { current_page: 2, last_page: 1, total: 1 } })
      .mockResolvedValueOnce({
        data: [firstPage.data[0]],
        meta: { current_page: 1, last_page: 1, total: 1 },
      });
    const article = wrapper.find('article');
    await article.find('[aria-label="Delete note"]').trigger('click');
    await article
      .findAll('button')
      .find((button) => button.text() === 'Delete note')
      .trigger('click');
    await flushPromises();
    expect(remove).toHaveBeenCalledWith('firearms', 7, 1);
    expect(fetchAll).toHaveBeenLastCalledWith('firearms', 7, expect.objectContaining({ page: 1 }));
    expect(wrapper.text()).toContain('Newest note');
  });

  it('retains the note when deletion fails', async () => {
    const wrapper = await mountPanel();
    remove.mockRejectedValue(new Error('offline'));
    const article = wrapper.find('article');
    await article.find('[aria-label="Delete note"]').trigger('click');
    await article
      .findAll('button')
      .find((button) => button.text() === 'Delete note')
      .trigger('click');
    await flushPromises();
    expect(article.find('[role="alert"]').text()).toContain('could not be deleted');
    await article
      .findAll('button')
      .find((button) => button.text() === 'Cancel')
      .trigger('click');
    expect(article.text()).toContain('Newest note');
  });
  it('refreshes active search results after an edit changes a matching note', async () => {
    vi.useFakeTimers();
    const wrapper = await mountPanel();
    await wrapper.find('input[type="search"]').setValue('Newest');
    await vi.advanceTimersByTimeAsync(250);
    await flushPromises();
    const article = wrapper.find('article');
    await article.find('[aria-label="Edit note"]').trigger('click');
    await article.find('textarea').setValue('No longer matches');
    update.mockResolvedValue({ data: { ...firstPage.data[0], note: 'No longer matches' } });
    fetchAll.mockResolvedValue({ data: [], meta: { current_page: 1, last_page: 1, total: 0 } });
    await article
      .findAll('button')
      .find((button) => button.text() === 'Save')
      .trigger('click');
    await flushPromises();
    expect(fetchAll).toHaveBeenLastCalledWith(
      'firearms',
      7,
      expect.objectContaining({ search: 'Newest' })
    );
    expect(wrapper.text()).toContain('No notes match your search');
    vi.useRealTimers();
  });

  it('blocks duplicate submissions while an edit is saving', async () => {
    const wrapper = await mountPanel();
    const article = wrapper.find('article');
    let finishUpdate;
    update.mockImplementation(
      () =>
        new Promise((resolve) => {
          finishUpdate = resolve;
        })
    );
    await article.find('[aria-label="Edit note"]').trigger('click');
    await article.find('textarea').setValue('Pending edit');
    const saveButton = article.findAll('button').find((button) => button.text() === 'Save');
    await saveButton.trigger('click');
    await article.find('textarea').trigger('keydown', { key: 'Enter', ctrlKey: true });
    expect(update).toHaveBeenCalledTimes(1);
    expect(saveButton.attributes('disabled')).toBeDefined();
    finishUpdate({ data: { ...firstPage.data[0], note: 'Pending edit' } });
    await flushPromises();
    expect(article.text()).toContain('Pending edit');
  });
});
