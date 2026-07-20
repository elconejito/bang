import { describe, expect, it, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';

vi.mock('vue-router', () => ({ useRoute: () => ({ query: {} }) }));

import MagazineGroupCard from '@/components/magazines/MagazineGroupCard.vue';

const group = {
  key: 'magpul-gl9-17-9mm',
  manufacturer: 'Magpul',
  model_name: 'PMAG GL9',
  capacity: 17,
  calibers: [{ label: '9mm' }],
  summary: { total: 6, in_gun: 1, loaded: 3, empty: 2 },
};

describe('MagazineGroupCard', () => {
  it('shows the shared markers and text labels for every group state', () => {
    const wrapper = mount(MagazineGroupCard, {
      props: { group },
      global: { stubs: { 'router-link': { template: '<a><slot /></a>' } } },
    });

    expect(wrapper.get('[data-testid="magazine-group-state-in_gun"]').text()).toContain(
      '1 in a gun'
    );
    expect(wrapper.get('[data-testid="magazine-group-state-loaded"]').text()).toContain(
      '3 loaded spare'
    );
    expect(wrapper.get('[data-testid="magazine-group-state-empty"]').text()).toContain('2 empty');
    expect(wrapper.get('[data-testid="magazine-group-state-in_gun"] > span').classes()).toEqual(
      expect.arrayContaining(['h-[11px]', 'w-[11px]', 'bg-[#2f7d57]'])
    );
    expect(wrapper.get('[data-testid="magazine-group-state-loaded"] > span').classes()).toEqual(
      expect.arrayContaining(['h-[11px]', 'w-[11px]', 'bg-[#c2a14d]'])
    );
    expect(wrapper.get('[data-testid="magazine-group-state-empty"] > span').classes()).toEqual(
      expect.arrayContaining(['h-[11px]', 'w-[11px]', 'border-[1.5px]', 'border-[#b6bcc1]'])
    );
  });

  it('keeps the dedicated group page legend aligned with the card and table markers', () => {
    const source = readFileSync(
      resolve(process.cwd(), 'resources/front-end/src/pages/magazines/MagazinesIndex.vue'),
      'utf8'
    );

    expect(source).toContain('In a gun');
    expect(source).toContain('Loaded spare');
    expect(source).toContain('/>Empty');
    expect(source.match(/h-\[11px\] w-\[11px\]/g)).toHaveLength(3);
    expect(source).toContain('bg-[#2f7d57]');
    expect(source).toContain('bg-[#c2a14d]');
    expect(source).toContain('border-[1.5px] border-[#b6bcc1]');
  });
});
