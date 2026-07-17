import { describe, expect, it } from 'vitest';
import { createMemoryHistory, createRouter } from 'vue-router';
import routes from '@/router/routes';

describe('order routes', () => {
  it('resolves create, detail, and edit URLs', () => {
    const router = createRouter({ history: createMemoryHistory(), routes });

    expect(router.resolve({ name: 'OrderCreate', query: { store_id: 4 } }).href).toBe(
      '/orders/new?store_id=4'
    );
    expect(router.resolve({ name: 'OrderShow', params: { order_id: 12 } }).href).toBe('/orders/12');
    expect(router.resolve({ name: 'OrderEdit', params: { order_id: 12 } }).href).toBe(
      '/orders/12/edit'
    );
  });
});
