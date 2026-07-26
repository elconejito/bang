<script setup>
import { reactive, ref, onMounted, computed } from 'vue';
import { LoaderCircle } from 'lucide-vue-next';
import { useRouter } from 'vue-router';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import FormError from '@/components/FormError.vue';
import { useLocationsStore } from '@/stores/locations';

const props = defineProps({
  locationId: { type: Number, default: null },
});

const router = useRouter();
const locationsStore = useLocationsStore();

const isEdit = computed(() => !!props.locationId);

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Storage Locations', to: { name: 'LocationIndex' } },
  ...(isEdit.value
    ? [
        {
          label: form.label || '…',
          to: { name: 'LocationsShow', params: { location_id: props.locationId } },
        },
        { label: 'Edit' },
      ]
    : [{ label: 'Add Location' }]),
]);

const form = reactive({ label: '', description: '', parent_location_id: '' });
const locations = ref([]);
const saving = ref(false);
const error = ref(null);
const parentOptions = computed(() => {
  if (!isEdit.value) {
    return locations.value;
  }

  const excludedIds = new Set([props.locationId]);
  let foundDescendant = true;

  while (foundDescendant) {
    foundDescendant = false;
    for (const location of locations.value) {
      if (
        location.parent_location_id &&
        excludedIds.has(location.parent_location_id) &&
        !excludedIds.has(location.id)
      ) {
        excludedIds.add(location.id);
        foundDescendant = true;
      }
    }
  }

  return locations.value.filter((location) => !excludedIds.has(location.id));
});

onMounted(async () => {
  const { data: allLocations } = await locationsStore.fetchAll();
  locations.value = allLocations;

  if (isEdit.value) {
    const { data } = await locationsStore.fetchOne(props.locationId);
    form.label = data.label;
    form.description = data.description ?? '';
    form.parent_location_id = data.parent_location_id ?? '';
  }
});

async function submit() {
  if (!form.label.trim()) return;
  saving.value = true;
  error.value = null;
  try {
    if (isEdit.value) {
      await locationsStore.update(props.locationId, {
        ...form,
        parent_location_id: form.parent_location_id || null,
      });
      router.push({ name: 'LocationsShow', params: { location_id: props.locationId } });
    } else {
      const { data } = await locationsStore.create({
        ...form,
        parent_location_id: form.parent_location_id || null,
      });
      router.push({ name: 'LocationsShow', params: { location_id: data.id } });
    }
  } catch (err) {
    error.value = err;
  } finally {
    saving.value = false;
  }
}
</script>

<template>
  <div class="max-w-[640px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-4" />
    <h1 class="font-display font-bold text-[28px] tracking-[-0.02em] mb-6">
      {{ isEdit ? 'Edit Location' : 'Add Location' }}
    </h1>

    <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
      <div class="px-6 py-5 flex flex-col gap-4">
        <div>
          <label class="mb-1.5 block text-[13px] font-semibold text-[#3a3e44]">
            Inside location
            <span class="font-normal text-[#8a9098]">(optional)</span>
          </label>
          <select
            v-model="form.parent_location_id"
            class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[14px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
          >
            <option value="">No parent location</option>
            <option v-for="location in parentOptions" :key="location.id" :value="location.id">
              {{ location.full_label ?? location.label }}
            </option>
          </select>
          <p class="mt-1.5 text-[12px] text-[#8a9098]">
            Choose where this location sits, such as Gun Safe › Top Shelf.
          </p>
        </div>

        <div>
          <label class="block text-[13px] font-semibold text-[#3a3e44] mb-1.5"
            >Name <span class="text-[#b4452f]">*</span></label
          >
          <input
            v-model="form.label"
            type="text"
            placeholder="Gun safe, gun room…"
            class="w-full border border-[#c2c6ca] rounded px-3 py-[9px] text-[14px] bg-white focus:outline-none focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
          />
        </div>

        <div>
          <label class="block text-[13px] font-semibold text-[#3a3e44] mb-1.5"
            >Description <span class="font-normal text-[#8a9098]">(optional)</span></label
          >
          <textarea
            v-model="form.description"
            rows="3"
            placeholder="Notes about this location…"
            class="w-full border border-[#c2c6ca] rounded px-3 py-[9px] text-[14px] bg-white focus:outline-none focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6] resize-none"
          />
        </div>

        <FormError v-if="error" :error="error" />
      </div>

      <div
        class="flex items-center justify-end gap-2.5 px-6 py-3.5 border-t border-[#eef0f1] bg-[#fafbfb]"
      >
        <button
          class="font-semibold text-[14px] bg-white text-[#3a3e44] px-4 py-2 border border-[#c2c6ca] rounded hover:bg-[#f5f6f7] transition-colors"
          @click="router.back()"
        >
          Cancel
        </button>
        <button
          :disabled="saving || !form.label.trim()"
          class="inline-flex items-center justify-center gap-2 font-semibold text-[14px] bg-brass text-[#1a1c1f] px-4 py-2 border border-[#b08a2e] rounded hover:bg-[#b8902f] disabled:opacity-50 transition-colors"
          @click="submit"
        >
          <LoaderCircle v-if="saving" class="h-4 w-4 animate-spin" />
          {{ saving ? 'Saving…' : isEdit ? 'Save Changes' : 'Add Location' }}
        </button>
      </div>
    </div>
  </div>
</template>
