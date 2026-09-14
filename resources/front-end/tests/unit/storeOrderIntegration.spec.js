import { describe, expect, it } from 'vitest';
import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';

describe('store order integration', () => {
  const source = readFileSync(
    resolve(process.cwd(), 'resources/front-end/src/pages/stores/StoresShow.vue'),
    'utf8'
  );

  it('starts a preselected order from the store detail page', () => {
    expect(source).toContain("name: 'OrderCreate', query: { store_id: storeId }");
    expect(source).toContain('Add order');
  });

  it('links purchase history entries to their order details', () => {
    expect(source).toContain("name: 'OrderShow', params: { order_id: order.id }");
  });

  it('shows item counts for mixed purchases alongside ammunition rounds', () => {
    expect(source).toContain('order.items_count ?? 0');
    expect(source).toContain('order.rounds.toLocaleString()');
  });
});
