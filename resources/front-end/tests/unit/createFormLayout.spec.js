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
});
