<template>
  <component
    :is="formComponent"
    :mode="mode"
    :item="item"
    @close="$emit('close')"
    @saved="$emit('saved', $event)"
    @deleted="$emit('deleted', $event)"
  />
</template>

<script setup>
import { computed } from 'vue';
import CaliberForm from '@/components/reference/forms/CaliberForm.vue';
import PurposeForm from '@/components/reference/forms/PurposeForm.vue';
import ColorForm from '@/components/reference/forms/ColorForm.vue';
import StorageLocationForm from '@/components/reference/forms/StorageLocationForm.vue';
import StoreForm from '@/components/reference/forms/StoreForm.vue';
import RangeForm from '@/components/reference/forms/RangeForm.vue';

/**
 * Thin dispatcher that renders the right Add/Edit form for a reference type. Used
 * both from the Manage Lists page and in-context (e.g. the Firearm form's caliber
 * picker). Each form renders its own ReferenceModalShell, so this component only
 * routes by `type` and forwards events — no per-type field conditionals.
 */
const FORMS = {
  caliber: CaliberForm,
  purpose: PurposeForm,
  color: ColorForm,
  location: StorageLocationForm,
  store: StoreForm,
  range: RangeForm,
};

const props = defineProps({
  /** @type {'caliber'|'purpose'|'location'|'store'|'range'} */
  type: { type: String, required: true },
  /** @type {'add' | 'edit'} */
  mode: { type: String, default: 'add' },
  /** Existing item when editing. */
  item: { type: Object, default: null },
});

defineEmits(['close', 'saved', 'deleted']);

const formComponent = computed(() => FORMS[props.type]);
</script>
