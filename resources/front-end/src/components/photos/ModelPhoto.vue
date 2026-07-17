<script setup>
import { computed } from 'vue';
import {
  Aperture,
  Box,
  Crosshair,
  Image as ImageIcon,
  Lightbulb,
  MapPin,
  Package,
  ScanLine,
  Store,
  Target,
  Warehouse,
} from 'lucide-vue-next';

const props = defineProps({
  src: { type: String, default: null },
  alt: { type: String, default: '' },
  modelType: { type: String, default: 'picture' },
  family: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'gallery', 'compact', 'expanded'].includes(value),
  },
});

const familyClasses = {
  primary: 'aspect-[5/3] object-cover',
  gallery: 'aspect-[4/3] object-cover',
  compact: 'aspect-square object-cover',
  expanded: 'max-h-[85vh] max-w-[90vw] object-contain',
};

const placeholderIcons = {
  ammunition: Package,
  firearm: Crosshair,
  light: Lightbulb,
  location: Warehouse,
  locations: Warehouse,
  magazine: Box,
  magazines: Box,
  misc: ImageIcon,
  miscaccessorie: ImageIcon,
  miscaccessories: ImageIcon,
  optic: Aperture,
  optics: Aperture,
  range: MapPin,
  ranges: MapPin,
  store: Store,
  stores: Store,
  suppressor: ScanLine,
  suppressors: ScanLine,
  target: Target,
};

const normalizedType = computed(() => props.modelType.toLowerCase().replace(/[_-]/g, ''));
const placeholderIcon = computed(() => placeholderIcons[normalizedType.value] ?? ImageIcon);
const placeholderLabel = computed(() => `No ${props.modelType.replace(/[_-]/g, ' ')} photo`);
</script>

<template>
  <img
    v-if="src"
    :src="src"
    :alt="alt"
    class="block h-full w-full"
    :class="familyClasses[family]"
  />
  <div
    v-else
    class="flex h-full w-full flex-col items-center justify-center gap-2 bg-ink-100 text-ink-300"
    :class="familyClasses[family]"
    role="img"
    :aria-label="placeholderLabel"
  >
    <component :is="placeholderIcon" class="h-9 w-9" aria-hidden="true" />
    <span class="sr-only">{{ placeholderLabel }}</span>
  </div>
</template>
