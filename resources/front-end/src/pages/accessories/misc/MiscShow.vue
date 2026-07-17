<script setup>
import { ref, computed, onMounted } from 'vue';
import { Camera, Plus } from 'lucide-vue-next';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import AccessoryEventTimeline from '@/components/history/AccessoryEventTimeline.vue';
import NotesPanel from '@/components/notes/NotesPanel.vue';
import { useMiscAccessoriesStore } from '@/stores/miscAccessories';
import dayjs from 'dayjs';

const props = defineProps({
  miscId: { type: Number, required: true },
});

const miscStore = useMiscAccessoriesStore();
const misc = ref(null);
const loading = ref(true);

onMounted(async () => {
  const { data } = await miscStore.fetchOne(props.miscId);
  misc.value = data;
  loading.value = false;
});

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
  { label: 'Misc', to: { name: 'AccessoriesMisc' } },
  { label: misc.value?.label ?? '…' },
]);
</script>

<template>
  <div class="max-w-[1280px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-5" />
    <LoadingState v-if="loading" message="Loading accessory…" />
    <template v-else-if="misc">
      <div class="flex items-center gap-4 mb-6 flex-wrap">
        <div class="flex-1 min-w-0">
          <h1 class="font-display font-bold text-[28px] tracking-[-0.02em] mb-1">
            {{ misc.label }}
          </h1>
          <div class="text-[15px] text-[#6b7077]">
            {{ misc.manufacturer }}<template v-if="misc.sub_type"> · {{ misc.sub_type }}</template>
          </div>
        </div>
        <router-link :to="{ name: 'MiscEdit', params: { misc_id: misc.id } }" class="detail-action">
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
      <div class="grid grid-cols-[344px_1fr] gap-6 items-start">
        <div class="flex flex-col gap-4">
          <!-- Photo card -->
          <div class="overflow-hidden rounded border border-line bg-surface">
            <router-link :to="{ name: 'MiscGallery', params: { misc_id: miscId } }" class="block">
              <div class="relative aspect-[5/3] w-full bg-ink-100">
                <img
                  v-if="misc.primary_photo_url"
                  :src="misc.primary_photo_url"
                  :alt="misc.label"
                  class="h-full w-full object-cover"
                />
                <div v-else class="flex h-full w-full items-center justify-center">
                  <Camera class="h-10 w-10 text-ink-300" />
                </div>
                <span
                  class="absolute bottom-2.5 right-2.5 inline-flex items-center gap-1.5 rounded bg-[rgba(26,28,31,0.82)] px-[10px] py-1 text-[12px] font-medium text-white"
                >
                  <Camera class="h-[13px] w-[13px]" />
                  {{ misc.pictures_count ? `${misc.pictures_count} photos` : 'Add photos' }}
                </span>
              </div>
            </router-link>
            <div v-if="misc.pictures_count > 1" class="grid grid-cols-4 gap-1.5 p-1.5">
              <router-link
                v-for="(url, i) in misc.thumbnail_urls"
                :key="i"
                :to="{ name: 'MiscGallery', params: { misc_id: miscId } }"
                class="h-[54px] rounded border border-line bg-ink-50 block overflow-hidden"
              >
                <img :src="url" class="h-full w-full object-cover" alt="" />
              </router-link>
              <router-link
                v-for="n in Math.max(0, 3 - misc.thumbnail_urls.length)"
                :key="`ph-${n}`"
                :to="{ name: 'MiscGallery', params: { misc_id: miscId } }"
                class="h-[54px] rounded border border-line bg-ink-50 block"
              />
              <router-link
                :to="{ name: 'MiscGallery', params: { misc_id: miscId } }"
                class="flex h-[54px] items-center justify-center rounded border border-dashed border-[#c2c6ca] bg-[#fafbfb] text-ink-400 transition-colors hover:bg-ink-50"
              >
                <Plus class="h-4 w-4" />
              </router-link>
            </div>
          </div>
          <div
            :class="
              misc.firearm_id ? 'bg-[#e7f1eb] border-[#9ccbb1]' : 'bg-[#f5f6f7] border-[#c2c6ca]'
            "
            class="border rounded-sm p-[13px_16px] flex items-center gap-3"
          >
            <div
              :class="
                misc.firearm_id
                  ? 'border-[#9ccbb1] text-[#2f7d57]'
                  : 'border-[#c2c6ca] text-[#5b6066]'
              "
              class="w-9 h-9 rounded-sm bg-white border flex items-center justify-center"
            >
              <svg
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
            </div>
            <div class="flex-1 min-w-0">
              <div
                :class="misc.firearm_id ? 'text-[#2f7d57]' : 'text-[#5b6066]'"
                class="font-mono text-[10px] tracking-[0.06em]"
              >
                {{ misc.firearm_id ? 'MOUNTED ON' : 'UNMOUNTED' }}
              </div>
              <div class="text-[16px] font-semibold">
                {{
                  misc.firearm
                    ? (misc.firearm.label ?? misc.firearm.manufacturer)
                    : (misc.location?.label ?? 'No location')
                }}
              </div>
            </div>
          </div>
          <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-[#eef0f1] font-display font-semibold text-[16px]">
              Details
            </div>
            <div class="px-4 py-1.5">
              <div
                v-if="misc.sub_type"
                class="flex items-center justify-between py-[9px] border-b border-[#f1f2f3]"
              >
                <span class="text-[14px] text-[#6b7077]">Type</span
                ><span class="text-[14px] capitalize">{{ misc.sub_type }}</span>
              </div>
              <div
                v-if="misc.serial"
                class="flex items-center justify-between py-[9px] border-b border-[#f1f2f3]"
              >
                <span class="text-[14px] text-[#6b7077]">Serial #</span
                ><span class="font-mono text-[14px]">{{ misc.serial }}</span>
              </div>
              <div v-if="misc.purchase_date" class="flex items-center justify-between py-[9px]">
                <span class="text-[14px] text-[#6b7077]">Purchased</span
                ><span class="text-[14px]"
                  >{{ dayjs(misc.purchase_date).format('MMM YYYY')
                  }}<template v-if="misc.purchase_price">
                    ·
                    <span class="font-mono"
                      >${{ Number(misc.purchase_price).toLocaleString() }}</span
                    ></template
                  ></span
                >
              </div>
            </div>
          </div>

          <NotesPanel entity-type="misc-accessories" :entity-id="miscId" />
        </div>
        <AccessoryEventTimeline
          entity-type="misc-accessories"
          :entity-id="miscId"
          history-label="MOUNTS · MAINTENANCE"
          :manual-event-types="[{ value: 'REPAIR', label: 'Repair / Service' }]"
        />
      </div>
    </template>
  </div>
</template>
