import { afterEach, describe, expect, it, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import NoteCard from '@/components/notes/NoteCard.vue';

describe('NoteCard dates', () => {
  afterEach(() => vi.useRealTimers());

  it('shows relative dates through 24 hours and full dates after that for creation and updates', () => {
    vi.useFakeTimers();
    vi.setSystemTime(new Date('2026-07-15T12:00:00Z'));
    const wrapper = mount(NoteCard, {
      props: {
        note: {
          id: 1,
          note: 'A note',
          created_at: '2026-07-14T11:59:59Z',
          updated_at: '2026-07-14T12:00:00Z',
        },
        updateNote: vi.fn(),
        deleteNote: vi.fn(),
      },
    });

    const dates = wrapper.findAll('time');
    expect(dates[0].text()).toBe(
      new Date('2026-07-14T11:59:59Z').toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      })
    );
    expect(dates[1].text()).toBe('a day ago');
  });
});
