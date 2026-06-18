<script setup>
import { computed } from 'vue';
import dayjs from 'dayjs';

const props = defineProps({
  session: { type: Object, required: true },
});

const date = computed(() => dayjs(props.session.session_date));
</script>

<template>
  <router-link
    :to="{ name: 'TrainingShow', params: { training_id: session.id } }"
    class="flex items-stretch gap-0 bg-white border border-[#e2e4e6] rounded-sm hover:border-[#b0b5ba] transition-colors group"
  >
    <!-- Date rail -->
    <div class="flex flex-col items-center justify-center px-4 py-3 border-r border-[#eef0f1] text-center min-w-[60px] bg-[#fafbfb]">
      <span class="font-mono text-[10px] text-muted tracking-[0.08em] uppercase">{{ date.format('ddd') }}</span>
      <span class="font-display font-bold text-[26px] leading-none text-ink-900 my-0.5">{{ date.format('D') }}</span>
      <span class="font-mono text-[10px] text-muted tracking-[0.08em] uppercase">{{ date.format('MMM') }}</span>
    </div>

    <!-- Content -->
    <div class="flex-1 min-w-0 px-4 py-3">
      <div class="flex items-start justify-between gap-3 mb-1">
        <span class="font-semibold text-[15px] text-ink-900 group-hover:text-brass transition-colors leading-tight">{{ session.label }}</span>
        <span class="font-mono text-[12px] text-muted whitespace-nowrap shrink-0">
          {{ session.total_rounds.toLocaleString() }} RDS
          <template v-if="session.target_count"> · {{ session.target_count }} TGT</template>
        </span>
      </div>

      <div v-if="session.location" class="text-[13px] text-[#6b7077] mb-2">{{ session.location.label }}</div>

      <!-- Firearm chips -->
      <div v-if="session.firearms_used?.length" class="flex flex-wrap gap-1.5 mt-1.5">
        <span
          v-for="fw in session.firearms_used"
          :key="fw.firearm?.id"
          class="font-mono text-[11px] border border-[#c2c6ca] rounded-sm px-[7px] py-[2px] text-[#5b6066] bg-[#f7f8f8]"
        >
          {{ fw.firearm?.label ?? 'Unknown' }} · {{ fw.rounds.toLocaleString() }}
        </span>
        <span
          v-if="session.has_suppressor"
          class="font-mono text-[11px] border border-[#9ccbb1] rounded-sm px-[7px] py-[2px] text-[#2f7d57] bg-[#e7f1eb]"
        >
          SUPPRESSED
        </span>
      </div>
    </div>
  </router-link>
</template>
