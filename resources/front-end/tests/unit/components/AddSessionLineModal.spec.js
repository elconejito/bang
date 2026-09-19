import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const { createLine, fetchAmmunition, fetchFirearms, fetchSuppressors } = vi.hoisted(() => ({
  createLine: vi.fn(),
  fetchAmmunition: vi.fn(),
  fetchFirearms: vi.fn(),
  fetchSuppressors: vi.fn(),
}));

vi.mock('@/stores/sessionLines', () => ({
  useSessionLinesStore: () => ({ create: createLine }),
}));
vi.mock('@/stores/ammunition', () => ({
  useAmmunitionStore: () => ({ fetchAll: fetchAmmunition }),
}));
vi.mock('@/stores/firearms', () => ({
  useFirearmsStore: () => ({ fetchAll: fetchFirearms }),
}));
vi.mock('@/stores/suppressors', () => ({
  useSuppressorsStore: () => ({ fetchAll: fetchSuppressors }),
}));

import AddSessionLineModal from '@/components/training/AddSessionLineModal.vue';

describe('AddSessionLineModal', () => {
  beforeEach(() => {
    createLine.mockReset().mockResolvedValue({ data: { id: 8 } });
    fetchFirearms.mockReset().mockResolvedValue({ data: [] });
    fetchSuppressors.mockReset().mockResolvedValue({ data: [] });
    fetchAmmunition.mockReset().mockResolvedValue({
      data: [
        {
          id: 3,
          caliber: { label: '5.56 NATO' },
          manufacturer: 'PMC',
          label: 'Bronze',
          weight: 55,
          on_hand: 420,
        },
      ],
    });
  });

  it('uses the shared training line fields and detailed ammunition label', async () => {
    const wrapper = mount(AddSessionLineModal, {
      props: { trainingId: 12 },
      global: { stubs: { Teleport: true, LoadingState: true } },
    });
    await flushPromises();

    expect(wrapper.text()).toContain('APPLY TO INVENTORY');
    expect(wrapper.text()).toContain('Firearm · optional');

    const ammunitionSelect = wrapper.findAll('select')[1];
    expect(ammunitionSelect.text()).toContain('5.56 NATO · PMC · Bronze · 55 gr');
    await ammunitionSelect.setValue('3');
    expect(wrapper.text()).toContain('420 left');
  });
});
