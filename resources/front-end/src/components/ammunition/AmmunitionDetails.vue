<template>
  <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
    <div>
      <h3 class="mb-3 text-base font-semibold text-gray-900">Details</h3>
      <dl class="space-y-2 text-sm">
        <div class="grid grid-cols-3 gap-2">
          <dt class="font-medium text-gray-500">Manufacturer</dt>
          <dd class="col-span-2">{{ ammunition.manufacturer }}</dd>
        </div>
        <div class="grid grid-cols-3 gap-2">
          <dt class="font-medium text-gray-500">Label</dt>
          <dd class="col-span-2">{{ ammunition.label }}</dd>
        </div>
        <div class="grid grid-cols-3 gap-2">
          <dt class="font-medium text-gray-500">Purpose</dt>
          <dd class="col-span-2">{{ purposeLabel }}</dd>
        </div>

        <!-- Shotgun -->
        <template v-if="caliber.caliber_type_id === 3">
          <div class="grid grid-cols-3 gap-2">
            <dt class="font-medium text-gray-500">Shell Type</dt>
            <dd class="col-span-2">{{ shellTypeLabel }}</dd>
          </div>
          <div class="grid grid-cols-3 gap-2">
            <dt class="font-medium text-gray-500">Weight (oz)</dt>
            <dd class="col-span-2">{{ ammunition.weight }}</dd>
          </div>
          <div class="grid grid-cols-3 gap-2">
            <dt class="font-medium text-gray-500">Shell Length</dt>
            <dd class="col-span-2">{{ shellLengthLabel }}</dd>
          </div>
          <div class="grid grid-cols-3 gap-2">
            <dt class="font-medium text-gray-500">Shot Material</dt>
            <dd class="col-span-2">{{ shotMaterialLabel }}</dd>
          </div>
        </template>

        <!-- Not a Shotgun -->
        <template v-else>
          <div class="grid grid-cols-3 gap-2">
            <dt class="font-medium text-gray-500">Bullet Type</dt>
            <dd class="col-span-2">{{ bulletTypeLabel }}</dd>
          </div>
          <div class="grid grid-cols-3 gap-2">
            <dt class="font-medium text-gray-500">Weight (gr)</dt>
            <dd class="col-span-2">{{ ammunition.weight }}</dd>
          </div>
          <div class="grid grid-cols-3 gap-2">
            <dt class="font-medium text-gray-500">Case Material</dt>
            <dd class="col-span-2">{{ ammunitionCasingLabel }}</dd>
          </div>
          <div class="grid grid-cols-3 gap-2">
            <dt class="font-medium text-gray-500">Condition</dt>
            <dd class="col-span-2">{{ ammunitionConditionLabel }}</dd>
          </div>
          <div class="grid grid-cols-3 gap-2">
            <dt class="font-medium text-gray-500">Primer Type</dt>
            <dd class="col-span-2">{{ primerTypeLabel }}</dd>
          </div>
        </template>
      </dl>
    </div>

    <div>
      <h3 class="mb-3 text-base font-semibold text-gray-900">Notes</h3>
      <AmmunitionNotes :ammunition="ammunition" :caliber="caliber" />
    </div>
  </div>
</template>

<script setup>
import { toRef } from 'vue'
import { useAmmunitionHelper } from '@/composables/useAmmunitionHelper'
import AmmunitionNotes from '@/components/ammunition/AmmunitionNotes.vue'

const props = defineProps({
  ammunition: {
    type: Object,
    required: true,
  },
  caliber: {
    type: Object,
    required: true,
  },
})

const {
  purposeLabel,
  shellTypeLabel,
  shellLengthLabel,
  shotMaterialLabel,
  bulletTypeLabel,
  ammunitionCasingLabel,
  ammunitionConditionLabel,
  primerTypeLabel,
} = useAmmunitionHelper(toRef(props, 'ammunition'))
</script>
