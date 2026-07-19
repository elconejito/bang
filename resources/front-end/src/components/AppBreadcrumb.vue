<template>
  <nav aria-label="Breadcrumb" class="flex items-center gap-1 text-[13px] text-muted">
    <router-link
      to="/"
      aria-label="Home"
      class="flex items-center transition-colors hover:text-ink-700"
    >
      <Home class="h-3.5 w-3.5" />
    </router-link>
    <template v-for="(crumb, i) in visibleCrumbs" :key="i">
      <ChevronRight class="h-3 w-3 shrink-0 text-ink-300" />
      <router-link v-if="crumb.to" :to="crumb.to" class="transition-colors hover:text-ink-700">{{
        crumb.label
      }}</router-link>
      <span v-else class="font-medium text-ink-700">{{ crumb.label }}</span>
    </template>
  </nav>
</template>

<script setup>
import { computed } from 'vue';
import { Home, ChevronRight } from 'lucide-vue-next';

const props = defineProps({
  /**
   * @type {{ label: string, to?: import('vue-router').RouteLocationRaw }[]}
   */
  crumbs: {
    type: Array,
    default: () => [],
  },
});

const visibleCrumbs = computed(() => {
  const [firstCrumb, ...remainingCrumbs] = props.crumbs;

  return firstCrumb?.label === 'Home' && firstCrumb.to === '/' ? remainingCrumbs : props.crumbs;
});
</script>
