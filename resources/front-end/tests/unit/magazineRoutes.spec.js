import { describe, expect, it } from 'vitest';
import { createMemoryHistory, createRouter } from 'vue-router';
import routes from '@/router/routes';

const magazineRoute = routes
  .flatMap((route) => route.children ?? [])
  .find((route) => route.path === '/accessories/magazines');

function childPath(name) {
  return magazineRoute.children.find((route) => route.name === name)?.path;
}

describe('magazine routes', () => {
  it('uses the accessories magazine URL hierarchy', () => {
    expect(magazineRoute).toBeTruthy();
    expect(childPath('MagazinesIndex')).toBe('');
    expect(childPath('CompatibleMagazines')).toBe('compatible/:firearm_id');
    expect(childPath('MagazineGroupShow')).toBe('groups/:group');
    expect(childPath('MagazinesEdit')).toBe(':magazine_id');
  });

  it('resolves canonical all, compatible, group, and edit URLs', () => {
    const router = createRouter({ history: createMemoryHistory(), routes });

    expect(router.resolve({ name: 'MagazinesIndex' }).href).toBe('/accessories/magazines');
    expect(router.resolve({ name: 'CompatibleMagazines', params: { firearm_id: 21 } }).href).toBe(
      '/accessories/magazines/compatible/21'
    );
    expect(router.resolve({ name: 'MagazineGroupShow', params: { group: 'pmag-gl9' } }).href).toBe(
      '/accessories/magazines/groups/pmag-gl9'
    );
    expect(router.resolve({ name: 'MagazinesEdit', params: { magazine_id: 12 } }).href).toBe(
      '/accessories/magazines/12'
    );
  });
});
