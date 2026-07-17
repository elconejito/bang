import { describe, expect, it } from 'vitest';
import { createMemoryHistory, createRouter } from 'vue-router';
import routes from '@/router/routes';

describe('accessory category routes', () => {
  it('resolves each filtered accessory view beneath accessories', () => {
    const router = createRouter({ history: createMemoryHistory(), routes });

    expect(router.resolve({ name: 'AccessoriesSuppressors' }).href).toBe(
      '/accessories/suppressors'
    );
    expect(router.resolve({ name: 'AccessoriesOptics' }).href).toBe('/accessories/optics');
    expect(router.resolve({ name: 'AccessoriesLights' }).href).toBe('/accessories/lights');
    expect(router.resolve({ name: 'AccessoriesMisc' }).href).toBe('/accessories/misc');
    expect(router.resolve({ name: 'MagazinesIndex' }).href).toBe('/accessories/magazines');
  });
});
