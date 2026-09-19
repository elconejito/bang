import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';
import TrainingForm from '@/components/training/TrainingForm.vue';
import EditTrainingForm from '@/components/training/EditTrainingForm.vue';

const { create, update } = vi.hoisted(() => ({
  create: vi.fn(),
  update: vi.fn(),
}));
vi.mock('@/stores/training', () => ({ useTrainingStore: () => ({ create, update }) }));
vi.mock('@/stores/ranges', () => ({ useRangesStore: () => ({
  fetchAll: vi.fn().mockResolvedValue({ data: [] }),
}) }));
vi.mock('@/stores/firearms', () => ({ useFirearmsStore: () => ({
  fetchAll: vi.fn().mockResolvedValue({ data: [{ id: 1, label: 'Owned firearm' }] }),
}) }));
vi.mock('@/stores/ammunition', () => ({ useAmmunitionStore: () => ({
  fetchAll: vi.fn().mockResolvedValue({ data: [{ id: 2, label: 'Ammo', inventory: 100 }] }),
}) }));
vi.mock('@/stores/suppressors', () => ({ useSuppressorsStore: () => ({
  fetchAll: vi.fn().mockResolvedValue({ data: [] }),
}) }));

const global = { stubs: { LoadingState: true, RouterLink: true, ReferenceItemModal: true } };

describe('Training forms', () => {
  beforeEach(() => {
    create.mockReset().mockResolvedValue({ data: { id: 1 } });
    update.mockReset().mockResolvedValue({ data: { id: 1 } });
  });

  it.each([false, true])('records ammo usage with a selected firearm: %s', async (withFirearm) => {
    const wrapper = mount(TrainingForm, { global });
    await flushPromises();
    expect(wrapper.text()).toContain('Firearm · optional');
    expect(wrapper.find('textarea').exists()).toBe(false);
    await wrapper.get('input[type="text"]').setValue('Range day');
    if (withFirearm) await wrapper.findAll('select')[1].setValue('1');
    await wrapper.findAll('select')[2].setValue('2');
    await wrapper.get('input[type="number"]').setValue(50);
    await wrapper.get('form').trigger('submit');
    await flushPromises();
    expect(create).toHaveBeenCalledWith(expect.objectContaining({
      lines: [expect.objectContaining({
        firearm_id: withFirearm ? 1 : null,
        ammunition_id: 2,
        rounds: 50,
        deduct_ammo: true,
        add_firearm_count: withFirearm,
      })],
    }));
    expect(create.mock.calls[0][0]).not.toHaveProperty('description');
    expect(wrapper.emitted('complete')).toBeTruthy();
  });

  it('removes the legacy notes field from editing without clearing existing descriptions', async () => {
    const wrapper = mount(EditTrainingForm, {
      global,
      props: { session: { id: 1, label: 'Range day', session_date: '2026-09-19', description: 'Legacy' } },
    });
    await flushPromises();
    expect(wrapper.find('textarea').exists()).toBe(false);
    await wrapper.get('form').trigger('submit');
    expect(update.mock.calls[0][1]).not.toHaveProperty('description');
  });
});
