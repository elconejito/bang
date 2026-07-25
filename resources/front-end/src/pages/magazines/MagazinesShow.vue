<script setup>
import { ref, computed, onMounted } from 'vue';
import { Camera, Plus } from 'lucide-vue-next';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import ArchivedBanner from '@/components/archive/ArchivedBanner.vue';
import EntityLifecycleActions from '@/components/archive/EntityLifecycleActions.vue';
import AccessoryEventTimeline from '@/components/history/AccessoryEventTimeline.vue';
import NotesPanel from '@/components/notes/NotesPanel.vue';
import { useMagazinesStore } from '@/stores/magazines';

const props = defineProps({
  magazineId: { type: Number, required: true },
});

const magazinesStore = useMagazinesStore();

const magazine = ref(null);
const loading = ref(true);
const historyKey = ref(0);

onMounted(async () => {
  const { data } = await magazinesStore.fetchOne(props.magazineId);
  magazine.value = data;
  loading.value = false;
});

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
  { label: 'Magazines', to: { name: 'MagazinesIndex' } },
  { label: magazine.value?.model_name ?? magazine.value?.label ?? '…' },
]);

const statusConfig = computed(() => {
  const state = magazine.value?.display_status ?? magazine.value?.status;
  if (state === 'in_gun') {
    return { label: 'In firearm', mono: 'IN FIREARM', green: true };
  }
  if (state === 'loaded') {
    return { label: 'Loaded spare', mono: 'LOADED SPARE', green: false, brass: true };
  }
  return { label: 'Empty', mono: 'EMPTY', green: false, brass: false };
});

const caliberLabel = computed(
  () => magazine.value?.calibers?.map((c) => c.label).join(' / ') ?? null
);
const ammunitionLabel = computed(() =>
  magazine.value?.loaded_ammunition
    ? [magazine.value.loaded_ammunition.manufacturer, magazine.value.loaded_ammunition.label]
        .filter(Boolean)
        .join(' ')
    : '—'
);
const locationLabel = computed(() => {
  if (magazine.value?.current_firearm) {
    return [magazine.value.current_firearm.manufacturer, magazine.value.current_firearm.label]
      .filter(Boolean)
      .join(' ');
  }

  return magazine.value?.location?.full_label ?? magazine.value?.location?.label ?? 'Unassigned';
});
</script>

<template>
  <div class="max-w-[1280px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-5" />

    <LoadingState v-if="loading" message="Loading magazine…" />

    <template v-else-if="magazine">
      <ArchivedBanner
        v-if="magazine.lifecycle_status === 'archived'"
        :reason="magazine.archive_reason"
        :description="magazine.archive_description"
        :archived-at="magazine.archived_at"
      />
      <!-- Header -->
      <div class="flex items-center gap-4 mb-6 flex-wrap">
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2.5 mb-1">
            <h1 class="font-display font-bold text-[28px] tracking-[-0.02em]">
              {{ magazine.model_name ?? magazine.label }}
            </h1>
            <span
              class="font-mono text-[11px] text-[#8a9098] border border-[#d6d9dc] rounded-sm px-[7px] py-[2px]"
            >
              MAGAZINE
            </span>
          </div>
          <div class="text-[15px] text-[#6b7077]">
            {{ magazine.manufacturer }}
            <template v-if="caliberLabel"> · {{ caliberLabel }}</template>
            · {{ magazine.capacity }} rd
          </div>
        </div>
        <div class="flex items-center gap-2.5 ml-auto">
          <router-link
            v-if="magazine.lifecycle_status !== 'archived'"
            :to="{ name: 'MagazinesEdit', params: { magazine_id: magazine.id } }"
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
          <EntityLifecycleActions
            :entity-id="magazine.id"
            :entity-label="magazine.model_name ?? magazine.label"
            :status="magazine.lifecycle_status"
            :archive-action="magazinesStore.archive"
            :unarchive-action="magazinesStore.unarchive"
            :destroy-action="magazinesStore.destroy"
            :return-route="{ name: 'MagazinesIndex' }"
            effect-text="This magazine will be ejected without changing its loaded ammunition or round count."
            @updated="magazine = $event"
            @activity-changed="historyKey += 1"
          />
        </div>
      </div>

      <!-- Two-col layout -->
      <div class="grid grid-cols-[344px_1fr] gap-6 items-start">
        <!-- Left rail -->
        <div class="flex flex-col gap-4">
          <!-- Photo card -->
          <div class="overflow-hidden rounded border border-line bg-surface">
            <router-link
              :to="{ name: 'MagazineGallery', params: { magazine_id: magazineId } }"
              class="block"
            >
              <div class="relative aspect-[5/3] w-full bg-ink-100">
                <img
                  v-if="magazine.primary_photo_url"
                  :src="magazine.primary_photo_url"
                  :alt="magazine.label ?? magazine.model_name"
                  class="h-full w-full object-cover"
                />
                <div v-else class="flex h-full w-full items-center justify-center">
                  <Camera class="h-10 w-10 text-ink-300" />
                </div>
                <span
                  class="absolute bottom-2.5 right-2.5 inline-flex items-center gap-1.5 rounded bg-[rgba(26,28,31,0.82)] px-[10px] py-1 text-[12px] font-medium text-white"
                >
                  <Camera class="h-[13px] w-[13px]" />
                  {{ magazine.pictures_count ? `${magazine.pictures_count} photos` : 'Add photos' }}
                </span>
              </div>
            </router-link>
            <div v-if="magazine.pictures_count > 1" class="grid grid-cols-4 gap-1.5 p-1.5">
              <router-link
                v-for="(url, i) in magazine.thumbnail_urls"
                :key="i"
                :to="{ name: 'MagazineGallery', params: { magazine_id: magazineId } }"
                class="h-[54px] rounded border border-line bg-ink-50 block overflow-hidden"
              >
                <img :src="url" class="h-full w-full object-cover" alt="" />
              </router-link>
              <router-link
                v-for="n in Math.max(0, 3 - magazine.thumbnail_urls.length)"
                :key="`ph-${n}`"
                :to="{ name: 'MagazineGallery', params: { magazine_id: magazineId } }"
                class="h-[54px] rounded border border-line bg-ink-50 block"
              />
              <router-link
                :to="{ name: 'MagazineGallery', params: { magazine_id: magazineId } }"
                class="flex h-[54px] items-center justify-center rounded border border-dashed border-[#c2c6ca] bg-[#fafbfb] text-ink-400 transition-colors hover:bg-ink-50"
              >
                <Plus class="h-4 w-4" />
              </router-link>
            </div>
          </div>

          <!-- Status card -->
          <div
            :class="
              statusConfig.green
                ? 'bg-[#e7f1eb] border-[#9ccbb1]'
                : statusConfig.brass
                  ? 'bg-[#fdf6e7] border-[#e3d3a3]'
                  : 'bg-[#f5f6f7] border-[#c2c6ca]'
            "
            class="border rounded-sm p-[13px_16px] flex items-center gap-3"
          >
            <div
              :class="
                statusConfig.green
                  ? 'border-[#9ccbb1] text-[#2f7d57]'
                  : statusConfig.brass
                    ? 'border-[#e3d3a3] text-[#7d6320]'
                    : 'border-[#c2c6ca] text-[#5b6066]'
              "
              class="w-9 h-9 rounded-sm bg-white border flex items-center justify-center flex-none"
            >
              <!-- in_gun -->
              <svg
                v-if="statusConfig.green"
                class="w-[18px] h-[18px]"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M20 6 9 17l-5-5" />
              </svg>
              <!-- loaded -->
              <svg
                v-else-if="statusConfig.brass"
                class="w-[18px] h-[18px]"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <circle cx="12" cy="12" r="10" />
                <circle cx="12" cy="12" r="4" />
              </svg>
              <!-- empty -->
              <svg
                v-else
                class="w-[18px] h-[18px]"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <circle cx="12" cy="12" r="10" />
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div
                :class="
                  statusConfig.green
                    ? 'text-[#2f7d57]'
                    : statusConfig.brass
                      ? 'text-[#7d6320]'
                      : 'text-[#5b6066]'
                "
                class="font-mono text-[10px] tracking-[0.06em]"
              >
                {{ statusConfig.mono }}
              </div>
              <div class="text-[16px] font-semibold">{{ statusConfig.label }}</div>
            </div>
          </div>

          <!-- Specs -->
          <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-[#eef0f1] font-display font-semibold text-[16px]">
              Specs
            </div>
            <div class="px-4 py-1.5">
              <div class="flex items-center justify-between border-b border-[#f1f2f3] py-[9px]">
                <span class="text-[14px] text-[#6b7077]">ID marking</span>
                <span class="font-mono text-[14px]">{{ magazine.id_marking || '—' }}</span>
              </div>
              <div class="flex items-center justify-between border-b border-[#f1f2f3] py-[9px]">
                <span class="text-[14px] text-[#6b7077]">Capacity</span>
                <span class="text-[14px]">{{ magazine.capacity }} rounds</span>
              </div>
              <div
                v-if="magazine.serial_number"
                class="flex items-center justify-between border-b border-[#f1f2f3] py-[9px]"
              >
                <span class="text-[14px] text-[#6b7077]">Serial #</span>
                <span class="font-mono text-[14px]">{{ magazine.serial_number }}</span>
              </div>
              <div class="flex items-center justify-between border-b border-[#f1f2f3] py-[9px]">
                <span class="text-[14px] text-[#6b7077]">Loaded with</span>
                <router-link
                  v-if="magazine.loaded_ammunition"
                  :to="{
                    name: 'AmmoShow',
                    params: { ammunition_id: magazine.loaded_ammunition.id },
                  }"
                  class="text-[14px] font-medium text-brass-800 hover:underline"
                  >{{ ammunitionLabel }}</router-link
                >
                <span v-else class="text-[14px]">—</span>
              </div>
              <div class="flex items-center justify-between border-b border-[#f1f2f3] py-[9px]">
                <span class="text-[14px] text-[#6b7077]">Rounds loaded</span>
                <span class="font-mono text-[14px]"
                  >{{ magazine.loaded_rounds }} / {{ magazine.capacity }}</span
                >
              </div>
              <div class="flex items-center justify-between py-[9px]">
                <span class="text-[14px] text-[#6b7077]">Location</span>
                <span class="text-right text-[14px]">{{ locationLabel }}</span>
              </div>
            </div>
          </div>

          <!-- Compatible firearms -->
          <div
            v-if="magazine.firearms?.length"
            class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden"
          >
            <div class="px-4 py-3 border-b border-[#eef0f1] font-display font-semibold text-[16px]">
              Compatible with
            </div>
            <div class="px-4 py-1.5">
              <div
                v-for="(firearm, i) in magazine.firearms"
                :key="firearm.id"
                class="flex items-center justify-between py-[9px]"
                :class="i < magazine.firearms.length - 1 ? 'border-b border-[#f1f2f3]' : ''"
              >
                <span class="text-[14px]">{{ firearm.manufacturer }} {{ firearm.label }}</span>
                <router-link
                  :to="{ name: 'FirearmsShow', params: { firearm_id: firearm.id } }"
                  class="text-[13px] text-brass font-semibold hover:underline"
                >
                  View ›
                </router-link>
              </div>
            </div>
          </div>

          <NotesPanel entity-type="magazines" :entity-id="magazineId" />
        </div>

        <!-- Right: History -->
        <AccessoryEventTimeline
          :key="historyKey"
          entity-type="magazines"
          :entity-id="magazineId"
          :allow-logging="magazine.lifecycle_status !== 'archived'"
          history-label="LOADS · MOVES"
          :manual-event-types="[{ value: 'REPAIR', label: 'Repair / Service' }]"
        />
      </div>
    </template>
  </div>
</template>
