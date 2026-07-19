import { describe, expect, it, vi } from 'vitest';
import { mount } from '@vue/test-utils';

vi.mock('@/stores/auth', () => ({
  useAuthStore: () => ({
    pictureUploadsEnabled: false,
    pictureStorage: {
      driver: 's3',
      aws_configured: false,
      uploads_enabled: false,
      notice: 'AWS photo storage is not configured. Photo uploads are unavailable.',
    },
  }),
}));

vi.mock('@/stores/training', () => ({
  useTrainingStore: () => ({ addTarget: vi.fn() }),
}));

import AddTargetModal from '@/components/training/AddTargetModal.vue';

describe('AddTargetModal storage availability', () => {
  it('shows the AWS notice and disables image selection when S3 is incomplete', () => {
    const wrapper = mount(AddTargetModal, {
      props: { trainingId: 1 },
      global: {
        stubs: {
          Teleport: true,
          ActionButton: true,
          FormError: true,
        },
      },
    });

    expect(wrapper.text()).toContain(
      'AWS photo storage is not configured. Photo uploads are unavailable.'
    );
    expect(wrapper.get('input[type="file"]').attributes('disabled')).toBeDefined();
    expect(wrapper.get('.modal-scrim').exists()).toBe(true);
    expect(wrapper.get('.modal-shell').exists()).toBe(true);
  });
});
