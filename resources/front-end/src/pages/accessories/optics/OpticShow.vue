<script setup>
import { ref, computed, onMounted } from 'vue';
import { ArrowLeftRight, Camera, ChevronRight, Plus } from 'lucide-vue-next';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import AccessoryEventTimeline from '@/components/history/AccessoryEventTimeline.vue';
import MoveAccessoryModal from '@/components/accessories/MoveAccessoryModal.vue';
import NotesPanel from '@/components/notes/NotesPanel.vue';
import { useOpticsStore } from '@/stores/optics';
import dayjs from 'dayjs';

const OPTIC_TYPE_LABELS = {
  red_dot: 'Red dot',
  prism: 'Prism',
  lpvo: 'LPVO',
  variable: 'Variable',
};

const props = defineProps({
  opticId: { type: Number, required: true },
});

const opticsStore = useOpticsStore();
const optic = ref(null);
const loading = ref(true);
const showMoveModal = ref(false);
const historyKey = ref(0);

onMounted(async () => {
  const { data } = await opticsStore.fetchOne(props.opticId);
  optic.value = data;
  loading.value = false;
});

async function onMove(firearmId) {
  await opticsStore.update(props.opticId, { firearm_id: firearmId });
  const { data } = await opticsStore.fetchOne(props.opticId);
  optic.value = data;
  showMoveModal.value = false;
  historyKey.value += 1; // remount the timeline so the new mount entry shows
}

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
  { label: 'Optics', to: { name: 'AccessoriesOptics' } },
  { label: optic.value?.label ?? '…' },
]);
</script>

<template>
  <div class="max-w-[1280px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-5" />
    <LoadingState v-if="loading" message="Loading optic…" />
    <template v-else-if="optic">
      <div class="flex items-center gap-4 mb-6 flex-wrap">
        <div class="flex-1 min-w-0">
          <h1 class="font-display font-bold text-[28px] tracking-[-0.02em] mb-1">
            {{ optic.label }}
          </h1>
          <div class="text-[15px] text-[#6b7077]">
            {{ optic.manufacturer }} · Optic<template v-if="optic.optic_type">
              · {{ OPTIC_TYPE_LABELS[optic.optic_type] ?? optic.optic_type }}</template
            >
          </div>
        </div>
        <div class="flex items-center gap-2.5">
          <router-link
            :to="{ name: 'OpticEdit', params: { optic_id: optic.id } }"
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
          <button
            class="inline-flex items-center gap-1.5 bg-brass text-[#1a1c1f] font-semibold text-[14px] px-[15px] py-2 rounded border border-[#b08a2e] hover:bg-[#b8902f] transition-colors"
            @click="showMoveModal = true"
          >
            <ArrowLeftRight class="w-[15px] h-[15px]" />
            Move
          </button>
        </div>
      </div>
      <div class="grid grid-cols-[344px_1fr] gap-6 items-start">
        <div class="flex flex-col gap-4">
          <!-- Photo card -->
          <div class="overflow-hidden rounded border border-line bg-surface">
            <router-link
              :to="{ name: 'OpticGallery', params: { optic_id: opticId } }"
              class="block"
            >
              <div class="relative aspect-[5/3] w-full bg-ink-100">
                <img
                  v-if="optic.primary_photo_url"
                  :src="optic.primary_photo_url"
                  :alt="optic.label"
                  class="h-full w-full object-cover"
                />
                <div v-else class="flex h-full w-full items-center justify-center">
                  <Camera class="h-10 w-10 text-ink-300" />
                </div>
                <span
                  class="absolute bottom-2.5 right-2.5 inline-flex items-center gap-1.5 rounded bg-[rgba(26,28,31,0.82)] px-[10px] py-1 text-[12px] font-medium text-white"
                >
                  <Camera class="h-[13px] w-[13px]" />
                  {{ optic.pictures_count ? `${optic.pictures_count} photos` : 'Add photos' }}
                </span>
              </div>
            </router-link>
            <div v-if="optic.pictures_count > 1" class="grid grid-cols-4 gap-1.5 p-1.5">
              <router-link
                v-for="(url, i) in optic.thumbnail_urls"
                :key="i"
                :to="{ name: 'OpticGallery', params: { optic_id: opticId } }"
                class="h-[54px] rounded border border-line bg-ink-50 block overflow-hidden"
              >
                <img :src="url" class="h-full w-full object-cover" alt="" />
              </router-link>
              <router-link
                v-for="n in Math.max(0, 3 - optic.thumbnail_urls.length)"
                :key="`ph-${n}`"
                :to="{ name: 'OpticGallery', params: { optic_id: opticId } }"
                class="h-[54px] rounded border border-line bg-ink-50 block"
              />
              <router-link
                :to="{ name: 'OpticGallery', params: { optic_id: opticId } }"
                class="flex h-[54px] items-center justify-center rounded border border-dashed border-[#c2c6ca] bg-[#fafbfb] text-ink-400 transition-colors hover:bg-ink-50"
              >
                <Plus class="h-4 w-4" />
              </router-link>
            </div>
          </div>
          <component
            :is="optic.firearm_id ? 'router-link' : 'div'"
            :to="
              optic.firearm_id
                ? { name: 'FirearmsShow', params: { firearm_id: optic.firearm_id } }
                : undefined
            "
            :class="
              optic.firearm_id
                ? 'bg-[#e7f1eb] border-[#9ccbb1] hover:bg-[#e0ede6]'
                : 'bg-[#f5f6f7] border-[#c2c6ca]'
            "
            class="border rounded-sm px-4 py-[13px] flex items-center gap-3 transition-colors"
          >
            <div
              :class="
                optic.firearm_id
                  ? 'border-[#9ccbb1] text-[#2f7d57]'
                  : 'border-[#c2c6ca] text-[#5b6066]'
              "
              class="w-9 h-9 rounded-sm bg-white border flex items-center justify-center flex-none"
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
                :class="optic.firearm_id ? 'text-[#2f7d57]' : 'text-[#5b6066]'"
                class="font-mono text-[10px] tracking-[0.06em]"
              >
                {{ optic.firearm_id ? 'MOUNTED ON' : 'UNMOUNTED' }}
              </div>
              <div class="text-[16px] font-semibold truncate">
                {{
                  optic.firearm
                    ? (optic.firearm.label ?? optic.firearm.manufacturer)
                    : (optic.location?.label ?? 'No location')
                }}
              </div>
              <div v-if="optic.firearm" class="text-[12px] text-[#5b7466] truncate">
                <template v-if="optic.firearm.label && optic.firearm.manufacturer">{{
                  optic.firearm.manufacturer
                }}</template>
                <template v-if="optic.mounted_since"
                  >{{ optic.firearm.label && optic.firearm.manufacturer ? ' · ' : '' }}since
                  {{ dayjs(optic.mounted_since).format('MMM D') }}</template
                >
              </div>
            </div>
            <ChevronRight v-if="optic.firearm_id" class="w-4 h-4 flex-none text-[#7fae93]" />
          </component>
          <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-[#eef0f1] font-display font-semibold text-[16px]">
              Specs
            </div>
            <div class="px-4 py-1.5">
              <div
                v-if="optic.optic_type"
                class="flex items-center justify-between py-[9px] border-b border-[#f1f2f3]"
              >
                <span class="text-[14px] text-[#6b7077]">Type</span
                ><span class="text-[14px]">{{
                  OPTIC_TYPE_LABELS[optic.optic_type] ?? optic.optic_type
                }}</span>
              </div>
              <div
                v-if="optic.battery_type"
                class="flex items-center justify-between py-[9px] border-b border-[#f1f2f3]"
              >
                <span class="text-[14px] text-[#6b7077]">Battery</span
                ><span class="text-[14px]">{{ optic.battery_type }}</span>
              </div>
              <div
                v-if="optic.serial"
                class="flex items-center justify-between py-[9px] border-b border-[#f1f2f3]"
              >
                <span class="text-[14px] text-[#6b7077]">Serial #</span
                ><span class="font-mono text-[14px]">{{ optic.serial }}</span>
              </div>
              <div v-if="optic.purchase_date" class="flex items-center justify-between py-[9px]">
                <span class="text-[14px] text-[#6b7077]">Purchased</span
                ><span class="text-[14px]"
                  >{{ dayjs(optic.purchase_date).format('MMM YYYY')
                  }}<template v-if="optic.purchase_price">
                    ·
                    <span class="font-mono"
                      >${{ Number(optic.purchase_price).toLocaleString() }}</span
                    ></template
                  ></span
                >
              </div>
            </div>
          </div>

          <NotesPanel entity-type="optics" :entity-id="opticId" />
        </div>
        <AccessoryEventTimeline
          :key="historyKey"
          entity-type="optics"
          :entity-id="opticId"
          history-label="MOUNTS · MAINTENANCE"
          :manual-event-types="[
            { value: 'BATTERY_REPLACE', label: 'Battery replacement' },
            { value: 'REPAIR', label: 'Repair / Service' },
          ]"
        />
      </div>
    </template>

    <MoveAccessoryModal
      v-if="showMoveModal && optic"
      :current-firearm-id="optic.firearm_id"
      :accessory-label="optic.label"
      @move="onMove"
      @close="showMoveModal = false"
    />
  </div>
</template>
