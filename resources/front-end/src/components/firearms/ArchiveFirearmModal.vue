<script setup>
import { computed, ref } from 'vue';
import { Archive, LoaderCircle, X } from 'lucide-vue-next';

const props = defineProps({
  firearm: { type: Object, required: true },
  saving: { type: Boolean, default: false },
  error: { type: String, default: null },
});

const emit = defineEmits(['archive', 'close']);
const reasons = [
  ['sold', 'Sold'],
  ['transferred', 'Transferred'],
  ['repair', 'Repair'],
  ['broken', 'Broken'],
  ['retired', 'Retired'],
  ['lost', 'Lost'],
  ['stolen', 'Stolen'],
  ['destroyed', 'Destroyed'],
  ['other', 'Other'],
];
const reason = ref('');
const description = ref('');
const selectedAccessories = ref([]);
const mountedAccessories = computed(() => props.firearm.mounted_accessories ?? []);
const allSelected = computed(
  () =>
    mountedAccessories.value.length > 0 &&
    selectedAccessories.value.length === mountedAccessories.value.length
);

function accessoryToken(accessory) {
  return `${accessoryType(accessory)}:${accessory.id}`;
}

function accessoryType(accessory) {
  const value = String(accessory.type ?? '').toLowerCase();
  return value === 'misc' ? 'misc_accessory' : value;
}

function toggleAll() {
  selectedAccessories.value = allSelected.value ? [] : mountedAccessories.value.map(accessoryToken);
}

function submit() {
  if (!reason.value || props.saving) return;
  emit('archive', {
    reason: reason.value,
    description: description.value.trim() || null,
    unmount_accessories: mountedAccessories.value
      .filter((item) => selectedAccessories.value.includes(accessoryToken(item)))
      .map((item) => ({ type: accessoryType(item), id: item.id })),
  });
}
</script>

<template>
  <div class="modal-scrim z-40 p-4 sm:p-12" @click.self="emit('close')">
    <div
      class="modal-shell w-[520px] max-w-full"
      role="dialog"
      aria-modal="true"
      aria-labelledby="archive-firearm-title"
    >
      <div class="flex items-center justify-between gap-3 border-b border-[#eef0f1] px-[18px] py-4">
        <div>
          <h2 id="archive-firearm-title" class="font-display text-[18px] font-semibold">
            Archive {{ firearm.label }}
          </h2>
          <p class="mt-0.5 text-[13px] text-muted">
            The firearm will leave active lists and selectors.
          </p>
        </div>
        <button
          type="button"
          class="p-0.5 text-[#8a9098] transition-colors hover:text-[#1a1c1f]"
          aria-label="Close archive dialog"
          @click="emit('close')"
        >
          <X class="h-[18px] w-[18px]" />
        </button>
      </div>

      <div class="flex flex-col gap-5 px-[18px] py-5">
        <div>
          <label for="archive-reason" class="mb-1.5 block text-[13px] font-semibold text-[#3a3e44]"
            >Reason</label
          >
          <select
            id="archive-reason"
            v-model="reason"
            class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] text-[14px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
          >
            <option value="" disabled>Select a reason</option>
            <option v-for="[value, label] in reasons" :key="value" :value="value">
              {{ label }}
            </option>
          </select>
        </div>

        <div>
          <label
            for="archive-description"
            class="mb-1.5 block text-[13px] font-semibold text-[#3a3e44]"
            >Details <span class="font-normal text-muted">(optional)</span></label
          >
          <textarea
            id="archive-description"
            v-model="description"
            rows="3"
            maxlength="2000"
            placeholder="Add sale, transfer, repair, or other relevant details…"
            class="w-full resize-y rounded border border-[#c2c6ca] px-3 py-2.5 text-[14px] focus:border-brass focus:outline-none focus:ring-[3px] focus:ring-[#f4ecd6]"
          />
        </div>

        <fieldset v-if="mountedAccessories.length" class="rounded border border-[#e2e4e6]">
          <div
            class="flex items-center justify-between gap-3 border-b border-[#eef0f1] px-3.5 py-3"
          >
            <div>
              <legend class="text-[13px] font-semibold text-[#3a3e44]">Unmount accessories</legend>
              <p class="mt-0.5 text-[12px] text-muted">
                Checked items will be unmounted while archiving.
              </p>
            </div>
            <button
              type="button"
              class="text-[12px] font-semibold text-[#7d6320]"
              @click="toggleAll"
            >
              {{ allSelected ? 'Clear all' : 'Select all' }}
            </button>
          </div>
          <label
            v-for="accessory in mountedAccessories"
            :key="`${accessory.type}-${accessory.id}`"
            class="flex cursor-pointer items-center gap-3 border-b border-[#f1f2f3] px-3.5 py-2.5 last:border-b-0"
          >
            <input
              v-model="selectedAccessories"
              type="checkbox"
              :value="accessoryToken(accessory)"
              class="h-4 w-4 rounded border-[#aeb3b8] text-[#7d6320] focus:ring-[#d9c170]"
            />
            <span class="min-w-0">
              <span class="block truncate text-[14px] font-medium">{{ accessory.label }}</span>
              <span class="block text-[12px] text-muted">{{ accessory.type }}</span>
            </span>
          </label>
        </fieldset>

        <p
          v-if="error"
          class="rounded border border-[#e8b8ad] bg-[#fff3f0] px-3 py-2 text-[13px] text-[#a33d29]"
          role="alert"
        >
          {{ error }}
        </p>
      </div>

      <div
        class="flex items-center justify-end gap-2.5 border-t border-[#eef0f1] bg-[#fafbfb] px-[18px] py-3.5"
      >
        <button
          type="button"
          class="rounded border border-[#c2c6ca] bg-white px-4 py-2 text-[14px] font-semibold text-[#3a3e44] transition-colors hover:bg-[#f5f6f7]"
          @click="emit('close')"
        >
          Cancel
        </button>
        <button
          type="button"
          :disabled="saving || !reason"
          class="inline-flex items-center justify-center gap-2 rounded border border-[#765f22] bg-[#7d6320] px-4 py-2 text-[14px] font-semibold text-white transition-colors hover:bg-[#665118] disabled:opacity-50"
          @click="submit"
        >
          <LoaderCircle v-if="saving" class="h-4 w-4 animate-spin" />
          <Archive v-else class="h-4 w-4" />
          {{ saving ? 'Archiving…' : 'Archive firearm' }}
        </button>
      </div>
    </div>
  </div>
</template>
