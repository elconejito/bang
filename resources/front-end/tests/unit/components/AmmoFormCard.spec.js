import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';

const { create, get, update } = vi.hoisted(() => ({
  create: vi.fn(),
  get: vi.fn(),
  update: vi.fn(),
}));

vi.mock('@/plugins/axios', () => ({ axiosInstance: { get } }));
vi.mock('@/stores/ammunition', () => ({
  useAmmunitionStore: () => ({ create, update }),
}));

import AmmoFormCard from '@/components/ammunition/AmmoFormCard.vue';

const responses = {
  '/calibers': [
    { id: 1, label: '9mm', caliber_type_id: 10 },
    { id: 2, label: '12G', caliber_type_id: 20 },
    { id: 3, label: '.22 LR', caliber_type_id: 30 },
  ],
  '/purpose': [],
  '/bullet-type': [],
  '/ammunition-casing': [],
  '/primer-type': [{ id: 70, label: 'Boxer' }],
  '/ammunition-condition': [],
  '/caliber-type': [
    { id: 10, label: 'Centerfire' },
    { id: 20, label: 'Shotgun' },
    { id: 30, label: 'Rimfire' },
  ],
  '/shell-length': [{ id: 30, label: '2¾ in' }],
  '/shell-type': [{ id: 40, label: 'Buckshot' }],
  '/shot-material': [{ id: 50, label: 'Lead' }],
  '/shot-weight': [
    { id: 60, label: '7/8 oz' },
    { id: 61, label: '1 oz' },
    { id: 62, label: '1 1/8 oz' },
  ],
};

function mountForm(props = {}) {
  return mount(AmmoFormCard, {
    props,
    global: { stubs: { ReferenceItemModal: true } },
  });
}

async function ready(props = {}) {
  const wrapper = mountForm(props);
  await flushPromises();
  return wrapper;
}

describe('AmmoFormCard shotgun fields', () => {
  beforeEach(() => {
    get.mockReset().mockImplementation((url) =>
      Promise.resolve({
        data: { data: responses[url] },
      })
    );
    create.mockReset().mockResolvedValue({ data: { id: 70 } });
    update.mockReset().mockResolvedValue({ data: { id: 70 } });
  });

  it('shows and submits shotgun details for a shotgun caliber', async () => {
    const wrapper = await ready();

    expect(wrapper.find('[data-testid="ammo-shell-length"]').exists()).toBe(false);
    await wrapper.get('[data-testid="ammo-caliber"]').setValue('2');

    expect(wrapper.text()).toContain('SHOTGUN DETAILS');
    expect(wrapper.get('[data-testid="ammo-shell-length"]').text()).toContain('2¾ in');
    expect(wrapper.get('[data-testid="ammo-shell-type"]').text()).toContain('Buckshot');
    expect(wrapper.get('[data-testid="ammo-shot-material"]').text()).toContain('Lead');
    expect(wrapper.get('[data-testid="ammo-shot-weight"]').text()).toContain('7/8 oz');
    expect(wrapper.get('[data-testid="ammo-shot-weight"]').text()).toContain('1 1/8 oz');
    expect(wrapper.find('[data-testid="ammo-bullet-weight"]').exists()).toBe(false);
    expect(wrapper.find('[data-testid="ammo-bullet-type"]').exists()).toBe(false);
    expect(wrapper.find('[data-testid="ammo-casing"]').exists()).toBe(false);
    expect(wrapper.find('[data-testid="ammo-primer-type"]').exists()).toBe(false);

    await wrapper.get('[data-testid="ammo-shot-weight"]').setValue('60');
    await wrapper.get('[data-testid="ammo-shell-length"]').setValue('30');
    await wrapper.get('[data-testid="ammo-shell-type"]').setValue('40');
    await wrapper.get('[data-testid="ammo-shot-material"]').setValue('50');
    await wrapper
      .findAll('button')
      .find((button) => button.text().includes('Add load'))
      .trigger('click');

    expect(create).toHaveBeenCalledWith(
      expect.objectContaining({
        caliber_id: 2,
        shell_length_id: 30,
        shell_type_id: 40,
        shot_material_id: 50,
        shot_weight_id: 60,
        weight: null,
        bullet_type_id: null,
        ammunition_casing_id: null,
        primer_type_id: null,
      })
    );
  });

  it('clears shotgun details when switching to a non-shotgun caliber', async () => {
    const wrapper = await ready();

    await wrapper.get('[data-testid="ammo-caliber"]').setValue('2');
    await wrapper.get('[data-testid="ammo-shell-length"]').setValue('30');
    await wrapper.get('[data-testid="ammo-shell-type"]').setValue('40');
    await wrapper.get('[data-testid="ammo-shot-material"]').setValue('50');
    await wrapper.get('[data-testid="ammo-shot-weight"]').setValue('60');
    await wrapper.get('[data-testid="ammo-caliber"]').setValue('1');

    expect(wrapper.find('[data-testid="ammo-shell-length"]').exists()).toBe(false);
    await wrapper
      .findAll('button')
      .find((button) => button.text().includes('Add load'))
      .trigger('click');

    expect(create).toHaveBeenCalledWith(
      expect.objectContaining({
        caliber_id: 1,
        shell_length_id: null,
        shell_type_id: null,
        shot_material_id: null,
        shot_weight_id: null,
      })
    );
  });

  it('clears cartridge primer data when switching to a shotgun caliber', async () => {
    const wrapper = await ready();

    await wrapper.get('[data-testid="ammo-caliber"]').setValue('1');
    await wrapper.get('[data-testid="ammo-primer-type"]').setValue('70');
    await wrapper.get('[data-testid="ammo-caliber"]').setValue('2');

    expect(wrapper.find('[data-testid="ammo-primer-type"]').exists()).toBe(false);

    await wrapper
      .findAll('button')
      .find((button) => button.text().includes('Add load'))
      .trigger('click');

    expect(create).toHaveBeenCalledWith(
      expect.objectContaining({
        caliber_id: 2,
        primer_type_id: null,
      })
    );
  });

  it('hides and clears the primer system for rimfire ammunition', async () => {
    const wrapper = await ready();

    await wrapper.get('[data-testid="ammo-caliber"]').setValue('1');
    expect(wrapper.text()).toContain('Primer system');
    await wrapper.get('[data-testid="ammo-primer-type"]').setValue('70');
    await wrapper.get('[data-testid="ammo-caliber"]').setValue('3');

    expect(wrapper.find('[data-testid="ammo-primer-type"]').exists()).toBe(false);

    await wrapper
      .findAll('button')
      .find((button) => button.text().includes('Add load'))
      .trigger('click');

    expect(create).toHaveBeenCalledWith(
      expect.objectContaining({
        caliber_id: 3,
        primer_type_id: null,
      })
    );
  });

  it('loads existing shotgun details when editing', async () => {
    const wrapper = await ready({
      ammo: {
        id: 70,
        caliber_id: 2,
        manufacturer: 'Federal',
        label: 'FliteControl',
        shell_length_id: 30,
        shell_type_id: 40,
        shot_material_id: 50,
        shot_weight_id: 60,
      },
    });

    expect(wrapper.get('[data-testid="ammo-shell-length"]').element.value).toBe('30');
    expect(wrapper.get('[data-testid="ammo-shell-type"]').element.value).toBe('40');
    expect(wrapper.get('[data-testid="ammo-shot-material"]').element.value).toBe('50');
    expect(wrapper.get('[data-testid="ammo-shot-weight"]').element.value).toBe('60');
    expect(wrapper.get('[data-testid="ammo-caliber"]').attributes('disabled')).toBeUndefined();

    await wrapper
      .findAll('button')
      .find((button) => button.text().includes('Save changes'))
      .trigger('click');

    expect(update).toHaveBeenCalledWith(
      70,
      expect.objectContaining({
        shell_length_id: 30,
        shell_type_id: 40,
        shot_material_id: 50,
        shot_weight_id: 60,
      })
    );
  });

  it('allows an existing load to change caliber', async () => {
    const wrapper = await ready({
      ammo: {
        id: 70,
        caliber_id: 2,
        manufacturer: 'Federal',
        label: 'FliteControl',
        shell_length_id: 30,
        shell_type_id: 40,
        shot_material_id: 50,
        shot_weight_id: 60,
      },
    });

    await wrapper.get('[data-testid="ammo-caliber"]').setValue('1');
    await wrapper
      .findAll('button')
      .find((button) => button.text().includes('Save changes'))
      .trigger('click');

    expect(update).toHaveBeenCalledWith(
      70,
      expect.objectContaining({
        caliber_id: 1,
        shell_length_id: null,
        shell_type_id: null,
        shot_material_id: null,
        shot_weight_id: null,
      })
    );
  });
});
