<script setup>
import { computed } from 'vue';
import FirearmFormCard from '@/components/firearms/FirearmFormCard.vue';
import AccessoryFormCard from '@/components/accessories/AccessoryFormCard.vue';
import MagazineForm from '@/components/magazines/MagazineForm.vue';

const props = defineProps({ type: { type: String, required: true } });
const emit = defineEmits(['complete', 'cancel']);
const isFirearm = computed(() => props.type === 'firearm');
const isMagazine = computed(() => props.type === 'magazine');
const accessoryType = computed(() => (props.type === 'misc-accessory' ? 'misc' : props.type));
</script>
<template>
  <div
    class="fixed inset-0 z-40 flex items-start justify-center overflow-y-auto bg-black/40 p-4 sm:p-8"
    role="dialog"
    aria-modal="true"
  >
    <div class="w-full max-w-2xl rounded bg-[#f8f9f9] shadow-xl">
      <div class="flex items-center justify-between border-b border-line bg-white px-5 py-4">
        <h2 class="font-display text-xl font-semibold">Add {{ type.replace('-', ' ') }}</h2>
        <button type="button" aria-label="Close" class="text-muted" @click="emit('cancel')">
          ✕
        </button>
      </div>
      <FirearmFormCard
        v-if="isFirearm"
        :order-context="true"
        @complete="emit('complete', $event)"
        @cancel="emit('cancel')"
      />
      <MagazineForm
        v-else-if="isMagazine"
        :order-context="true"
        @complete="emit('complete', $event)"
        @cancel="emit('cancel')"
      />
      <AccessoryFormCard
        v-else
        :type="accessoryType"
        :order-context="true"
        @complete="emit('complete', $event)"
        @cancel="emit('cancel')"
      />
    </div>
  </div>
</template>
