import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';
import { describe, expect, it } from 'vitest';

const stylesheet = readFileSync(
  resolve(process.cwd(), 'resources/front-end/src/styles/tailwind.css'),
  'utf8'
);
const ordersIndex = readFileSync(
  resolve(process.cwd(), 'resources/front-end/src/pages/orders/OrdersIndex.vue'),
  'utf8'
);
const nativeSelectRule = stylesheet.match(
  /(select:not\(\[multiple\]\):not\(\[size\]\):not\(\.appearance-none\))\s*\{([^}]*)\}/
);

describe('native select caret styling', () => {
  it('applies the shared caret only to native single-value selects', () => {
    expect(nativeSelectRule).not.toBeNull();

    const selector = nativeSelectRule[1];
    const plainSelect = document.createElement('select');
    const paddedSelect = document.createElement('select');
    paddedSelect.className = 'px-3';
    const customCaretSelect = document.createElement('select');
    customCaretSelect.className = 'appearance-none';
    const multipleSelect = document.createElement('select');
    multipleSelect.multiple = true;
    const listbox = document.createElement('select');
    listbox.size = 2;

    expect(plainSelect.matches(selector)).toBe(true);
    expect(paddedSelect.matches(selector)).toBe(true);
    expect(customCaretSelect.matches(selector)).toBe(false);
    expect(multipleSelect.matches(selector)).toBe(false);
    listbox.setAttribute('size', '2');
    expect(listbox.matches(selector)).toBe(false);
  });

  it('reserves space for the inset SVG caret', () => {
    expect(nativeSelectRule).not.toBeNull();

    const declarations = nativeSelectRule[2];
    expect(declarations).toMatch(/appearance:\s*none/);
    expect(declarations).toMatch(/background-image:\s*url\(["']data:image\/svg\+xml/);
    expect(declarations).toMatch(/background-position:\s*right\s+0\.75rem\s+center/);
    expect(declarations).toMatch(/background-repeat:\s*no-repeat/);
    expect(declarations).toMatch(/background-size:\s*1rem\s+1rem/);
    expect(declarations).toMatch(/padding-right:\s*2\.5rem/);
  });

  it('lets the Orders store filter use the shared caret instead of opting out', () => {
    expect(nativeSelectRule).not.toBeNull();

    const storeSelect = ordersIndex.match(
      /<select\s+v-model="storeId"\s+aria-label="Filter by store"\s+class="([^"]+)"/s
    );
    expect(storeSelect).not.toBeNull();

    const select = document.createElement('select');
    select.className = storeSelect[1];
    expect(select.matches(nativeSelectRule[1])).toBe(true);
  });

  it('restores the browser control in forced-color mode', () => {
    expect(stylesheet).toMatch(
      /@media\s*\(forced-colors:\s*active\)\s*\{\s*select:not\(\[multiple\]\):not\(\[size\]\):not\(\.appearance-none\)\s*\{\s*appearance:\s*auto;\s*background-image:\s*none;/s
    );
  });
});
