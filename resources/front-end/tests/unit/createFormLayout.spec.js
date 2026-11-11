import { describe, expect, it } from 'vitest';
import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';

const simpleFormPages = [
  'resources/front-end/src/pages/ammunition/AmmoCreate.vue',
  'resources/front-end/src/pages/ammunition/AmmoEdit.vue',
  'resources/front-end/src/pages/firearms/FirearmsCreate.vue',
  'resources/front-end/src/pages/firearms/FirearmsEdit.vue',
  'resources/front-end/src/pages/accessories/lights/LightCreate.vue',
  'resources/front-end/src/pages/accessories/lights/LightEdit.vue',
  'resources/front-end/src/pages/accessories/misc/MiscCreate.vue',
  'resources/front-end/src/pages/accessories/misc/MiscEdit.vue',
  'resources/front-end/src/pages/accessories/optics/OpticCreate.vue',
  'resources/front-end/src/pages/accessories/optics/OpticEdit.vue',
  'resources/front-end/src/pages/accessories/suppressors/SuppressorCreate.vue',
  'resources/front-end/src/pages/accessories/suppressors/SuppressorEdit.vue',
  'resources/front-end/src/pages/locations/LocationsCreate.vue',
  'resources/front-end/src/pages/ranges/RangesCreate.vue',
  'resources/front-end/src/pages/ranges/RangesEdit.vue',
  'resources/front-end/src/pages/stores/StoresCreate.vue',
];

const handoffWidthPages = {
  'max-w-[860px]': [
    'resources/front-end/src/pages/orders/OrderCreate.vue',
    'resources/front-end/src/pages/orders/OrderEdit.vue',
  ],
  'max-w-[760px]': [
    'resources/front-end/src/pages/training/TrainingCreate.vue',
    'resources/front-end/src/pages/training/TrainingEdit.vue',
    'resources/front-end/src/pages/magazines/MagazinesCreate.vue',
    'resources/front-end/src/pages/magazines/MagazineBatchCreate.vue',
    'resources/front-end/src/pages/magazines/MagazinesEdit.vue',
  ],
};

function outerShellClasses(page) {
  const source = readFileSync(resolve(process.cwd(), page), 'utf8');
  const match = source.match(/<template>\s*<div\b[^>]*class="([^"]+)"/s);

  expect(match, `${page} needs a root page shell`).not.toBeNull();

  return match[1];
}

describe('create form layout', () => {
  it.each(simpleFormPages)('%s uses the shared simple-form page shell', (page) => {
    const classes = outerShellClasses(page);

    expect(classes).toContain('mx-auto');
    expect(classes).toContain('max-w-[640px]');
    expect(classes).toContain('px-4');
    expect(classes).toContain('sm:px-8');
    expect(classes).toContain('py-6');
    expect(classes).toContain('pb-16');
  });

  it.each(Object.entries(handoffWidthPages))(
    'keeps %s forms at their handoff width with responsive page gutters',
    (width, pages) => {
      for (const page of pages) {
        const classes = outerShellClasses(page);

        expect(classes).toContain('mx-auto');
        expect(classes).toContain(width);
        expect(classes).toContain('px-4');
        expect(classes).toContain('sm:px-8');
        expect(classes).toContain('py-6');
        expect(classes).toContain('pb-16');
      }
    }
  );

  it('uses the same shell for route pairs and keeps ammo free of nested width constraints', () => {
    const pairs = [
      [
        'resources/front-end/src/pages/ammunition/AmmoCreate.vue',
        'resources/front-end/src/pages/ammunition/AmmoEdit.vue',
      ],
      [
        'resources/front-end/src/pages/firearms/FirearmsCreate.vue',
        'resources/front-end/src/pages/firearms/FirearmsEdit.vue',
      ],
      [
        'resources/front-end/src/pages/orders/OrderCreate.vue',
        'resources/front-end/src/pages/orders/OrderEdit.vue',
      ],
      [
        'resources/front-end/src/pages/training/TrainingCreate.vue',
        'resources/front-end/src/pages/training/TrainingEdit.vue',
      ],
      [
        'resources/front-end/src/pages/magazines/MagazinesCreate.vue',
        'resources/front-end/src/pages/magazines/MagazinesEdit.vue',
      ],
    ];

    for (const [createPage, editPage] of pairs) {
      expect(outerShellClasses(createPage)).toBe(outerShellClasses(editPage));
    }

    const ammoEdit = readFileSync(
      resolve(process.cwd(), 'resources/front-end/src/pages/ammunition/AmmoEdit.vue'),
      'utf8'
    );
    expect(ammoEdit).not.toContain('max-w-2xl');
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
    expect(source).toContain('v-model="form.type"');
    expect(source).toContain('<option value="handgun">Handgun</option>');
    expect(source).toContain('<option value="rifle">Rifle</option>');
    expect(source).toContain('<option value="shotgun">Shotgun</option>');
    expect(source.match(/class="flex h-10 items-center/g)).toHaveLength(3);
    expect(source.match(/class="h-auto min-w-0 flex-1/g)).toHaveLength(3);
  });

  it('keeps accessory order and line-cost fields optional and context-aware', () => {
    const source = readFileSync(
      resolve(
        process.cwd(),
        'resources/front-end/src/components/accessories/AccessoryFormCard.vue'
      ),
      'utf8'
    );

    expect(source).toContain("axiosInstance.get('/orders')");
    expect(source).toContain('v-model="form.order_id"');
    expect(source).toContain('cost: form.cost || null');
    expect(source).toContain('orderContext');
    expect(source).toContain(
      '...(props.orderContext ? {} : { order_id: form.order_id || null, cost: form.cost || null })'
    );
    expect(source).toMatch(/v-if="!orderContext"[\s\S]*v-model="form.order_id"/);
    expect(source).toContain('v-model="form.model_number"');
    expect(source).toContain('model_number: form.model_number || null');
  });
});
