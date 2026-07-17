<script setup>
import { ref, computed, onMounted } from 'vue';
import { Camera, Plus } from 'lucide-vue-next';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import NotesPanel from '@/components/notes/NotesPanel.vue';
import { useLocationsStore } from '@/stores/locations';

const props = defineProps({
  locationId: { type: Number, required: true },
});

const locationsStore = useLocationsStore();
const location = ref(null);
const loading = ref(true);

onMounted(async () => {
  const { data } = await locationsStore.fetchOne(props.locationId);
  location.value = data;
  loading.value = false;
});

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Manage Lists', to: { name: 'ReferenceData', params: { list: 'location' } } },
  { label: location.value?.label ?? '…' },
]);

const CONTENT_SECTIONS = [
  { key: 'firearms', label: 'Firearms', routeName: 'FirearmsShow', paramKey: 'firearm_id' },
  {
    key: 'suppressors',
    label: 'Suppressors',
    routeName: 'SuppressorShow',
    paramKey: 'suppressor_id',
  },
  { key: 'optics', label: 'Optics', routeName: 'OpticShow', paramKey: 'optic_id' },
  { key: 'lights', label: 'Lights', routeName: 'LightShow', paramKey: 'light_id' },
  { key: 'misc_accessories', label: 'Misc', routeName: 'MiscShow', paramKey: 'misc_id' },
  { key: 'magazines', label: 'Magazines', routeName: 'MagazinesShow', paramKey: 'magazine_id' },
];

function itemTitle(item, section) {
  if (section.key === 'magazines') {
    return item.id_marking || item.label || item.model_name || 'Magazine';
  }

  return item.label;
}

function itemSubtitle(item, section) {
  if (section.key === 'magazines') {
    const identity = [item.manufacturer, item.model_name].filter(Boolean).join(' ');
    const rounds = `${item.loaded_rounds ?? 0} / ${item.capacity ?? 0} rounds`;
    return [identity, rounds].filter(Boolean).join(' · ');
  }

  return item.manufacturer;
}

const totalItems = computed(() => {
  if (!location.value?.contents) return 0;
  return CONTENT_SECTIONS.reduce(
    (sum, s) => sum + (location.value.contents[s.key]?.length ?? 0),
    0
  );
});
</script>

<template>
  <div class="max-w-[1280px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-5" />

    <LoadingState v-if="loading" message="Loading location…" />

    <template v-else-if="location">
      <!-- Header -->
      <div class="flex items-center gap-4 mb-6 flex-wrap">
        <div class="flex-1 min-w-0">
          <h1 class="font-display font-bold text-[28px] tracking-[-0.02em] mb-1">
            {{ location.label }}
          </h1>
          <div class="text-[15px] text-[#6b7077]">Storage Location</div>
        </div>
        <router-link
          :to="{ name: 'LocationsEdit', params: { location_id: locationId } }"
          class="detail-action"
        >
          <svg
            class="w-[15px] h-[15px]"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="M12 20h9" />
            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
          </svg>
          Edit
        </router-link>
      </div>

      <!-- Two-col layout -->
      <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[344px_1fr]">
        <!-- Left rail -->
        <div class="flex flex-col gap-4">
          <!-- Photo card -->
          <div class="overflow-hidden rounded border border-line bg-surface">
            <router-link
              :to="{ name: 'LocationGallery', params: { location_id: locationId } }"
              class="block"
            >
              <div class="relative aspect-[5/3] w-full bg-ink-100">
                <img
                  v-if="location.primary_photo_url"
                  :src="location.primary_photo_url"
                  :alt="location.label"
                  class="h-full w-full object-cover"
                />
                <div v-else class="flex h-full w-full items-center justify-center">
                  <Camera class="h-10 w-10 text-ink-300" />
                </div>
                <span
                  class="absolute bottom-2.5 right-2.5 inline-flex items-center gap-1.5 rounded bg-[rgba(26,28,31,0.82)] px-[10px] py-1 text-[12px] font-medium text-white"
                >
                  <Camera class="h-[13px] w-[13px]" />
                  {{ location.pictures_count ? `${location.pictures_count} photos` : 'Add photos' }}
                </span>
              </div>
            </router-link>
            <div v-if="location.pictures_count > 1" class="grid grid-cols-4 gap-1.5 p-1.5">
              <router-link
                v-for="(url, i) in location.thumbnail_urls"
                :key="i"
                :to="{ name: 'LocationGallery', params: { location_id: locationId } }"
                class="h-[54px] rounded border border-line bg-ink-50 block overflow-hidden"
              >
                <img :src="url" class="h-full w-full object-cover" alt="" />
              </router-link>
              <router-link
                v-for="n in Math.max(0, 3 - location.thumbnail_urls.length)"
                :key="`ph-${n}`"
                :to="{ name: 'LocationGallery', params: { location_id: locationId } }"
                class="h-[54px] rounded border border-line bg-ink-50 block"
              />
              <router-link
                :to="{ name: 'LocationGallery', params: { location_id: locationId } }"
                class="flex h-[54px] items-center justify-center rounded border border-dashed border-[#c2c6ca] bg-[#fafbfb] text-ink-400 transition-colors hover:bg-ink-50"
              >
                <Plus class="h-4 w-4" />
              </router-link>
            </div>
          </div>

          <!-- Description -->
          <div
            v-if="location.description"
            class="bg-white border border-[#e2e4e6] rounded-sm p-4 text-[14px] text-[#3a3e44] leading-relaxed"
          >
            {{ location.description }}
          </div>

          <NotesPanel entity-type="locations" :entity-id="locationId" />
        </div>

        <!-- Right: Contents -->
        <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
          <div class="flex items-center gap-3 px-[18px] py-4 border-b border-[#eef0f1]">
            <span class="font-display font-semibold text-[18px]">Contents</span>
            <span class="font-mono text-[11px] text-muted tracking-[0.04em]">
              {{ totalItems }} ITEM{{ totalItems !== 1 ? 'S' : '' }}
            </span>
          </div>

          <div v-if="totalItems === 0" class="px-[18px] py-12 text-center text-muted text-[14px]">
            Nothing stored here yet.
          </div>

          <div v-else class="divide-y divide-[#f1f2f3]">
            <template v-for="section in CONTENT_SECTIONS" :key="section.key">
              <div v-if="location.contents[section.key]?.length" class="px-[18px] py-3">
                <div class="flex items-baseline gap-2 mb-2">
                  <span class="font-semibold text-[13px] text-[#3a3e44]">{{ section.label }}</span>
                  <span class="font-mono text-[10px] text-muted tracking-[0.04em]">
                    {{ location.contents[section.key].length }}
                  </span>
                </div>
                <div class="flex flex-col gap-1">
                  <router-link
                    v-for="item in location.contents[section.key]"
                    :key="item.id"
                    :to="{ name: section.routeName, params: { [section.paramKey]: item.id } }"
                    class="flex items-center justify-between rounded px-3 py-2 text-[14px] hover:bg-[#f5f6f7] transition-colors"
                  >
                    <div class="min-w-0">
                      <span class="font-medium">{{ itemTitle(item, section) }}</span>
                      <span class="ml-2 text-[#8a9098]">{{ itemSubtitle(item, section) }}</span>
                    </div>
                    <svg
                      class="w-[14px] h-[14px] text-[#c2c6ca]"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    >
                      <path d="m9 18 6-6-6-6" />
                    </svg>
                  </router-link>
                </div>
              </div>
            </template>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
