import { beforeEach, describe, expect, it } from 'vitest';
import { nextTick } from 'vue';
import { usePersistentViewMode } from '@/composables/usePersistentViewMode';

describe('usePersistentViewMode', () => {
  beforeEach(() => localStorage.clear());

  it('persists and restores an allowed view mode', async () => {
    const viewMode = usePersistentViewMode('example');
    viewMode.value = 'table';
    await nextTick();

    expect(localStorage.getItem('bang:view-mode:example')).toBe('table');
    expect(usePersistentViewMode('example').value).toBe('table');
  });

  it('ignores an unsupported saved value', () => {
    localStorage.setItem('bang:view-mode:example', 'invalid');

    expect(usePersistentViewMode('example').value).toBe('grid');
  });
});
