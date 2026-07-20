import { describe, expect, it } from 'vitest';
import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';

const source = readFileSync(
  resolve(process.cwd(), 'resources/front-end/src/pages/training/TrainingIndex.vue'),
  'utf8'
);
const createFormSource = readFileSync(
  resolve(process.cwd(), 'resources/front-end/src/components/training/TrainingForm.vue'),
  'utf8'
);
const editFormSource = readFileSync(
  resolve(process.cwd(), 'resources/front-end/src/components/training/EditTrainingForm.vue'),
  'utf8'
);

describe('TrainingIndex summary strip', () => {
  it('uses the handoff summary typography, compact value-label spacing, and responsive cards', () => {
    expect(source).toContain('grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4');
    expect(source).toContain('text-[26px]');
    expect(source).toContain('mt-[3px]');
    expect(source).toContain('tracking-[0.06em]');
    expect(source).toContain('break-words');
    expect(source).toContain('tabular-nums');
    expect(source.match(/data-testid="training-summary-stat"/g)).toHaveLength(1);
  });

  it('keeps create and edit controls in one column before the form reaches the small breakpoint', () => {
    expect(createFormSource).toContain('grid grid-cols-1 gap-4 sm:grid-cols-2');
    expect(createFormSource).toContain('sm:col-span-2');
    expect(editFormSource).toContain('grid grid-cols-1 gap-4 px-4 pb-5 pt-4 sm:grid-cols-2');
    expect(editFormSource.match(/sm:col-span-2/g)).toHaveLength(2);
    expect(editFormSource).toContain('justify-end gap-3 border-t border-line pt-4');
  });
});
