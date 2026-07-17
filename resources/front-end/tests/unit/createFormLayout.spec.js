import { describe, expect, it } from 'vitest';
import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';

const createPages = [
  'resources/front-end/src/pages/accessories/lights/LightCreate.vue',
  'resources/front-end/src/pages/accessories/misc/MiscCreate.vue',
  'resources/front-end/src/pages/accessories/optics/OpticCreate.vue',
  'resources/front-end/src/pages/accessories/suppressors/SuppressorCreate.vue',
  'resources/front-end/src/pages/ammunition/AmmoCreate.vue',
  'resources/front-end/src/pages/firearms/FirearmsCreate.vue',
  'resources/front-end/src/pages/locations/LocationsCreate.vue',
  'resources/front-end/src/pages/magazines/MagazinesCreate.vue',
  'resources/front-end/src/pages/ranges/RangesCreate.vue',
  'resources/front-end/src/pages/stores/StoresCreate.vue',
  'resources/front-end/src/pages/training/TrainingCreate.vue',
];

describe('create form layout', () => {
  it.each(createPages)('%s uses the centered add-form page shell', (page) => {
    const source = readFileSync(resolve(process.cwd(), page), 'utf8');

    expect(source).toContain('mx-auto');
    expect(source).toContain('px-8 py-6 pb-16');
  });

  it('uses the shared form card and action-bar treatment for firearms', () => {
    const source = readFileSync(
      resolve(process.cwd(), 'resources/front-end/src/components/firearms/FirearmFormCard.vue'),
      'utf8'
    );

    expect(source).toContain('flex flex-col gap-5');
    expect(source).toContain('grid grid-cols-2 gap-4');
    expect(source).toContain('border-t border-line pt-5');
    expect(source).toContain('bg-white px-3 py-[9px]');
    expect(source.match(/class="flex h-10 items-center/g)).toHaveLength(4);
    expect(source.match(/class="h-auto min-w-0 flex-1/g)).toHaveLength(4);
  });

  it('lets accessory forms select and submit a purchase store', () => {
    const source = readFileSync(
      resolve(
        process.cwd(),
        'resources/front-end/src/components/accessories/AccessoryFormCard.vue'
      ),
      'utf8'
    );

    expect(source).toContain('useGunStoresStore');
    expect(source).toContain('gunStoresStore.fetchAll()');
    expect(source).toContain('v-model="form.purchase_store_id"');
    expect(source).toContain('purchase_store_id: form.purchase_store_id || null');
    expect(source).toContain("openQuickAdd('store')");
  });
});
