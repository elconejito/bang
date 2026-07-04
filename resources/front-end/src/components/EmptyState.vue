<template>
  <div class="rounded-sm border border-line bg-surface px-6 py-12 text-center">
    <div
      class="mx-auto flex h-12 w-12 items-center justify-center rounded-full border border-brass-300 bg-brass-100 text-brass-800"
    >
      <PackageOpen class="h-6 w-6" />
    </div>

    <h2 class="mt-4 font-display text-[17px] font-semibold text-ink-900">
      {{ title }}
    </h2>

    <p v-if="message" class="mx-auto mt-1 max-w-[420px] text-[14px] leading-5 text-muted">
      {{ message }}
    </p>

    <router-link
      v-if="actionTo && actionLabel"
      :to="actionTo"
      class="mt-5 inline-flex items-center justify-center gap-2 rounded border border-[#b08a2e] bg-brass px-4 py-2 text-[14px] font-semibold text-ink-900 transition-colors hover:bg-brass-600"
    >
      <Plus class="h-4 w-4" />
      {{ actionLabel }}
    </router-link>
  </div>
</template>

<script setup>
import { PackageOpen, Plus } from 'lucide-vue-next';

defineProps({
  title: { type: String, required: true },
  message: { type: String, default: '' },
  actionLabel: { type: String, default: '' },
  actionTo: { type: [String, Object], default: null },
});
</script>
