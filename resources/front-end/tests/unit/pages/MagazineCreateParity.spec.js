import { afterEach, expect, it, vi } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';
import { createPinia } from 'pinia';
import { axiosInstance } from '@/plugins/axios';
import MagazinesCreate from '@/pages/magazines/MagazinesCreate.vue';
import MagazineBatchCreate from '@/pages/magazines/MagazineBatchCreate.vue';

vi.mock('vue-router', () => ({
  useRoute: () => ({ query: {} }),
  useRouter: () => ({ push: vi.fn(), back: vi.fn() }),
}));

afterEach(() => vi.restoreAllMocks());

it.each([
  ['Add Magazine', MagazinesCreate, '/magazines'],
  ['Add Several Magazines', MagazineBatchCreate, '/magazine-batches'],
])('%s submits the shared catalog, storage and purchase fields', async (_, component, endpoint) => {
  const options = {
    '/locations': [{ id: 8, label: 'Cabinet' }],
    '/orders': [{ id: 11, order_ref: 'Invoice 11' }],
  };
  vi.spyOn(axiosInstance, 'get').mockImplementation(async (path) => ({
    data: { data: options[path] ?? [] },
  }));
  const post = vi.spyOn(axiosInstance, 'post').mockResolvedValue({ data: { data: { id: 1 } } });
  const page = mount(component, {
    global: { plugins: [createPinia()], stubs: { AppBreadcrumb: true, LoadingState: true } },
  });
  await flushPromises();

  function field(label, selector = 'input') {
    return page
      .findAll('label')
      .find((node) => node.text().startsWith(label))
      .get(selector);
  }

  await field('Manufacturer').setValue('Example');
  await field('Model name').setValue('Model A');
  await field('Model #').setValue('A-12');
  await field('Capacity').setValue('12');
  await field('Storage location', 'select').setValue('8');
  await field('Purchase order', 'select').setValue('11');
  await page.get('input[step="0.01"]').setValue('0');
  await page.get('form').trigger('submit');
  await flushPromises();

  expect(post).toHaveBeenCalledWith(
    endpoint,
    expect.objectContaining({
      manufacturer: 'Example',
      model_name: 'Model A',
      model_number: 'A-12',
      capacity: 12,
      location_id: 8,
      order_id: 11,
      cost: 0,
    })
  );
});
