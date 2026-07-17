<script setup>
import { ref, computed, onMounted } from 'vue';
import { ArrowLeftRight, Camera, Check, ChevronRight, Clock, Plus } from 'lucide-vue-next';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import AccessoryEventTimeline from '@/components/history/AccessoryEventTimeline.vue';
import MoveAccessoryModal from '@/components/accessories/MoveAccessoryModal.vue';
import NotesPanel from '@/components/notes/NotesPanel.vue';
import { useSuppressorsStore } from '@/stores/suppressors';
import { useNumbers } from '@/composables/useNumbers';
import dayjs from 'dayjs';

const props = defineProps({
  suppressorId: { type: Number, required: true },
});

const suppressorsStore = useSuppressorsStore();
const { formatQuantity } = useNumbers();

const suppressor = ref(null);
const loading = ref(true);
const showMoveModal = ref(false);
const historyKey = ref(0);

onMounted(async () => {
  const { data } = await suppressorsStore.fetchOne(props.suppressorId);
  suppressor.value = data;
  loading.value = false;
});

async function onMove(firearmId) {
  await suppressorsStore.update(props.suppressorId, { firearm_id: firearmId });
  const { data } = await suppressorsStore.fetchOne(props.suppressorId);
  suppressor.value = data;
  showMoveModal.value = false;
  historyKey.value += 1; // remount the timeline so the new mount entry shows
}

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
  { label: 'Suppressors', to: { name: 'AccessoriesSuppressors' } },
  { label: suppressor.value?.label ?? '…' },
]);
</script>

<template>
  <div class="max-w-[1280px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-5" />

    <LoadingState v-if="loading" message="Loading suppressor…" />

    <template v-else-if="suppressor">
      <!-- Header -->
      <div class="flex items-center gap-4 mb-6 flex-wrap">
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2.5 mb-1">
            <h1 class="font-display font-bold text-[28px] tracking-[-0.02em]">
              {{ suppressor.label }}
            </h1>
            <span
              v-if="suppressor.is_nfa"
              class="font-mono text-[11px] text-white bg-[#1a1c1f] rounded-sm px-[7px] py-[2px]"
              >NFA</span
            >
          </div>
          <div class="text-[15px] text-[#6b7077]">
            {{ suppressor.manufacturer }} · Suppressor
            <template v-if="suppressor.caliber"> · {{ suppressor.caliber.label }}</template>
          </div>
        </div>
        <div class="flex items-center gap-2.5 ml-auto">
          <router-link
            :to="{ name: 'SuppressorEdit', params: { suppressor_id: suppressor.id } }"
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

      <!-- Two-col layout -->
      <div class="grid grid-cols-[344px_1fr] gap-6 items-start">
        <!-- Left rail -->
        <div class="flex flex-col gap-4">
          <!-- Photo card -->
          <div class="overflow-hidden rounded border border-line bg-surface">
            <router-link
              :to="{ name: 'SuppressorGallery', params: { suppressor_id: suppressorId } }"
              class="block"
            >
              <div class="relative aspect-[5/3] w-full bg-ink-100">
                <img
                  v-if="suppressor.primary_photo_url"
                  :src="suppressor.primary_photo_url"
                  :alt="suppressor.label"
                  class="h-full w-full object-cover"
                />
                <div v-else class="flex h-full w-full items-center justify-center">
                  <Camera class="h-10 w-10 text-ink-300" />
                </div>
                <span
                  class="absolute bottom-2.5 right-2.5 inline-flex items-center gap-1.5 rounded bg-[rgba(26,28,31,0.82)] px-[10px] py-1 text-[12px] font-medium text-white"
                >
                  <Camera class="h-[13px] w-[13px]" />
                  {{
                    suppressor.pictures_count ? `${suppressor.pictures_count} photos` : 'Add photos'
                  }}
                </span>
              </div>
            </router-link>
            <div v-if="suppressor.pictures_count > 1" class="grid grid-cols-4 gap-1.5 p-1.5">
              <router-link
                v-for="(url, i) in suppressor.thumbnail_urls"
                :key="i"
                :to="{ name: 'SuppressorGallery', params: { suppressor_id: suppressorId } }"
                class="h-[54px] rounded border border-line bg-ink-50 block overflow-hidden"
              >
                <img :src="url" class="h-full w-full object-cover" alt="" />
              </router-link>
              <router-link
                v-for="n in Math.max(0, 3 - suppressor.thumbnail_urls.length)"
                :key="`ph-${n}`"
                :to="{ name: 'SuppressorGallery', params: { suppressor_id: suppressorId } }"
                class="h-[54px] rounded border border-line bg-ink-50 block"
              />
              <router-link
                :to="{ name: 'SuppressorGallery', params: { suppressor_id: suppressorId } }"
                class="flex h-[54px] items-center justify-center rounded border border-dashed border-[#c2c6ca] bg-[#fafbfb] text-ink-400 transition-colors hover:bg-ink-50"
              >
                <Plus class="h-4 w-4" />
              </router-link>
            </div>
          </div>

          <!-- Mounted status -->
          <component
            :is="suppressor.firearm_id ? 'router-link' : 'div'"
            :to="
              suppressor.firearm_id
                ? { name: 'FirearmsShow', params: { firearm_id: suppressor.firearm_id } }
                : undefined
            "
            :class="
              suppressor.firearm_id
                ? 'bg-[#e7f1eb] border-[#9ccbb1] hover:bg-[#e0ede6]'
                : 'bg-[#f5f6f7] border-[#c2c6ca]'
            "
            class="border rounded-sm px-4 py-[13px] flex items-center gap-3 transition-colors"
          >
            <div
              :class="
                suppressor.firearm_id
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
                :class="suppressor.firearm_id ? 'text-[#2f7d57]' : 'text-[#5b6066]'"
                class="font-mono text-[10px] tracking-[0.06em]"
              >
                {{ suppressor.firearm_id ? 'MOUNTED ON' : 'UNMOUNTED' }}
              </div>
              <div class="text-[16px] font-semibold truncate">
                {{
                  suppressor.firearm
                    ? (suppressor.firearm.label ?? suppressor.firearm.manufacturer)
                    : (suppressor.location?.label ?? 'No location')
                }}
              </div>
              <div v-if="suppressor.firearm" class="text-[12px] text-[#5b7466] truncate">
                <template v-if="suppressor.firearm.label && suppressor.firearm.manufacturer">{{
                  suppressor.firearm.manufacturer
                }}</template>
                <template v-if="suppressor.mounted_since"
                  >{{
                    suppressor.firearm.label && suppressor.firearm.manufacturer ? ' · ' : ''
                  }}since {{ dayjs(suppressor.mounted_since).format('MMM D') }}</template
                >
              </div>
            </div>
            <ChevronRight v-if="suppressor.firearm_id" class="w-4 h-4 flex-none text-[#7fae93]" />
          </component>

          <!-- Rounds through can -->
          <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
            <div
              class="flex items-baseline justify-between border-b border-[#eef0f1] bg-[#fafbfb] px-4 py-[14px]"
            >
              <span class="text-[14px] text-[#6b7077]">Rounds through can</span>
              <span class="font-mono text-[28px] font-medium leading-none">{{
                formatQuantity(suppressor.rounds_fired ?? 0)
              }}</span>
            </div>
            <div class="px-4 py-1.5">
              <div
                v-if="suppressor.caliber"
                class="flex items-center justify-between py-[9px] border-b border-[#f1f2f3]"
              >
                <span class="text-[14px] text-[#6b7077]">Caliber rating</span>
                <span
                  class="rounded border border-[#c2c6ca] bg-[#f5f6f7] px-[9px] py-px text-[12px] text-[#3a3e44]"
                  >{{ suppressor.caliber.label }}</span
                >
              </div>
              <div
                v-if="
                  suppressor.last_cleaned_rounds !== null &&
                  suppressor.last_cleaned_rounds !== undefined
                "
                class="flex items-center justify-between py-[9px]"
              >
                <span class="text-[14px] text-[#6b7077]">Last cleaned</span>
                <span class="text-[14px]"
                  >at {{ formatQuantity(suppressor.last_cleaned_rounds) }} rds</span
                >
              </div>
            </div>
          </div>

          <!-- Specs -->
          <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-[#eef0f1] font-display font-semibold text-[16px]">
              Specs
            </div>
            <div class="px-4 py-1.5">
              <div
                v-if="suppressor.mount_type"
                class="flex items-center justify-between py-[9px] border-b border-[#f1f2f3]"
              >
                <span class="text-[14px] text-[#6b7077]">Mount type</span>
                <span class="text-[14px]">{{ suppressor.mount_type }}</span>
              </div>
              <div
                v-if="suppressor.length"
                class="flex items-center justify-between py-[9px] border-b border-[#f1f2f3]"
              >
                <span class="text-[14px] text-[#6b7077]">Length</span>
                <span class="text-[14px]">{{ Number(suppressor.length) }}″</span>
              </div>
              <div
                v-if="suppressor.weight"
                class="flex items-center justify-between py-[9px] border-b border-[#f1f2f3]"
              >
                <span class="text-[14px] text-[#6b7077]">Weight</span>
                <span class="text-[14px]">{{ Number(suppressor.weight) }} oz</span>
              </div>
              <div
                v-if="!suppressor.is_nfa && suppressor.serial"
                class="flex items-center justify-between py-[9px] border-b border-[#f1f2f3]"
              >
                <span class="text-[14px] text-[#6b7077]">Serial #</span>
                <span class="font-mono text-[14px]">{{ suppressor.serial }}</span>
              </div>
              <div
                v-if="suppressor.purchase_date"
                class="flex items-center justify-between py-[9px]"
              >
                <span class="text-[14px] text-[#6b7077]">Purchased</span>
                <span class="text-[14px]">
                  {{ dayjs(suppressor.purchase_date).format('MMM YYYY')
                  }}<template v-if="suppressor.purchase_price">
                    ·
                    <span class="font-mono"
                      >${{ Number(suppressor.purchase_price).toLocaleString() }}</span
                    ></template
                  >
                </span>
              </div>
            </div>
          </div>

          <!-- NFA record -->
          <div
            v-if="suppressor.is_nfa"
            class="bg-white border border-[#ddd4ea] rounded-sm overflow-hidden"
          >
            <div class="flex items-center gap-2 border-b border-[#eee9f3] bg-[#f7f4fa] px-4 py-3">
              <svg
                class="h-[15px] w-[15px] text-[#6b5a8c]"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect width="18" height="11" x="3" y="11" rx="2" />
                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
              </svg>
              <span class="font-display font-semibold text-[16px] text-[#4a3d63]">NFA record</span>
            </div>
            <div class="px-4 py-1.5">
              <div
                v-if="suppressor.serial"
                class="flex items-center justify-between border-b border-[#f1f2f3] py-[9px]"
              >
                <span class="text-[14px] text-[#6b7077]">Serial #</span>
                <span class="font-mono text-[14px]">{{ suppressor.serial }}</span>
              </div>
              <div class="flex items-center justify-between border-b border-[#f1f2f3] py-[9px]">
                <span class="text-[14px] text-[#6b7077]">Tax stamp</span>
                <span
                  v-if="suppressor.nfa_approved_date"
                  class="inline-flex items-center gap-[5px] text-[13px] font-semibold text-[#2f7d57]"
                >
                  <Check class="h-[13px] w-[13px]" />Approved
                </span>
                <span v-else class="inline-flex items-center gap-[5px] text-[13px] text-[#8a9098]">
                  <Clock class="h-[13px] w-[13px]" />Pending
                </span>
              </div>
              <div
                v-if="suppressor.nfa_approved_date || suppressor.nfa_form_type"
                class="flex items-center justify-between border-b border-[#f1f2f3] py-[9px]"
              >
                <span class="text-[14px] text-[#6b7077]">{{
                  suppressor.nfa_form_type ? suppressor.nfa_form_type + ' cleared' : 'Form cleared'
                }}</span>
                <span class="text-[14px]">{{
                  suppressor.nfa_approved_date
                    ? dayjs(suppressor.nfa_approved_date).format('MMM YYYY')
                    : '—'
                }}</span>
              </div>
              <div v-if="suppressor.nfa_trust" class="flex items-center justify-between py-[9px]">
                <span class="text-[14px] text-[#6b7077]">Trust</span>
                <span class="text-[14px]">{{ suppressor.nfa_trust }}</span>
              </div>
            </div>
          </div>

          <NotesPanel entity-type="suppressors" :entity-id="suppressorId" />
        </div>

        <!-- Right: History -->
        <AccessoryEventTimeline
          :key="historyKey"
          entity-type="suppressors"
          :entity-id="suppressorId"
          history-label="MOUNTS · ROUNDS · MAINTENANCE"
          :manual-event-types="[
            { value: 'CLEAN', label: 'Cleaning' },
            { value: 'REPAIR', label: 'Repair / Service' },
          ]"
        />
      </div>
    </template>

    <MoveAccessoryModal
      v-if="showMoveModal && suppressor"
      :current-firearm-id="suppressor.firearm_id"
      :accessory-label="suppressor.label"
      @move="onMove"
      @close="showMoveModal = false"
    />
  </div>
</template>
