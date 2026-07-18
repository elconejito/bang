<script setup>
import { computed } from 'vue';
import dayjs from 'dayjs';
import { Home, MapPin, Pencil } from 'lucide-vue-next';
import { useRouter } from 'vue-router';

const router = useRouter();

const props = defineProps({
  session: { type: Object, required: true },
});

const date = computed(() => dayjs(props.session.session_date));
const locationLabel = computed(
  () => props.session.range?.label ?? props.session.location?.label ?? ''
);
const targetCount = computed(() => props.session.target_count ?? props.session.targets_count ?? 0);
</script>

<template>
  <div
    class="group flex cursor-pointer overflow-hidden rounded border border-line bg-white transition-all hover:border-[#c2c6ca] hover:shadow-[0_1px_2px_rgba(20,22,26,0.05),0_6px_16px_rgba(20,22,26,0.06)]"
    role="link"
    tabindex="0"
    @click="router.push({ name: 'TrainingShow', params: { training_id: session.id } })"
    @keydown.enter="router.push({ name: 'TrainingShow', params: { training_id: session.id } })"
    @keydown.space.prevent="
      router.push({ name: 'TrainingShow', params: { training_id: session.id } })
    "
  >
    <!-- Date rail -->
    <div
      class="flex min-w-[70px] flex-col items-center justify-center border-r border-[#eef0f1] bg-[#fafbfb] px-4 py-3 text-center"
    >
      <span class="font-mono text-[10px] uppercase tracking-[0.05em] text-muted">{{
        date.format('ddd')
      }}</span>
      <span class="my-0.5 font-mono text-[24px] font-semibold leading-none text-ink-900">{{
        date.format('D')
      }}</span>
      <span class="font-mono text-[10px] uppercase text-muted">{{ date.format('MMM') }}</span>
    </div>

    <!-- Content -->
    <div class="min-w-0 flex-1 px-4 py-3">
      <div class="flex items-start justify-between gap-4">
        <div class="min-w-0 flex-1">
          <div
            class="font-display text-[18px] font-semibold leading-[1.15] text-ink-900 transition-colors group-hover:text-brass-800"
          >
            {{ session.label }}
          </div>
          <div
            v-if="locationLabel"
            class="mt-0.5 inline-flex items-center gap-1.5 text-[13px] text-[#6b7077]"
          >
            <MapPin class="h-3.5 w-3.5 text-muted" />
            {{ locationLabel }}
          </div>
        </div>

        <div class="flex shrink-0 items-start gap-3 text-right sm:gap-5">
          <div class="hidden sm:block">
            <div class="font-mono text-[20px] font-medium leading-none">
              {{ session.total_rounds.toLocaleString() }}
            </div>
            <div class="mt-1 font-mono text-[9px] tracking-[0.05em] text-muted">ROUNDS</div>
          </div>
          <div class="hidden sm:block">
            <div
              class="font-mono text-[20px] font-medium leading-none"
              :class="targetCount ? 'text-ink-900' : 'text-ink-300'"
            >
              {{ targetCount || '-' }}
            </div>
            <div class="mt-1 font-mono text-[9px] tracking-[0.05em] text-muted">TARGETS</div>
          </div>
          <button
            type="button"
            class="inline-flex items-center gap-1.5 rounded border border-line bg-white px-2.5 py-1.5 text-[13px] font-semibold text-ink-700 transition-colors hover:bg-ink-50"
            :aria-label="`Edit ${session.label}`"
            @click.stop="router.push({ name: 'TrainingEdit', params: { training_id: session.id } })"
          >
            <Pencil class="h-[13px] w-[13px]" />Edit
          </button>
        </div>
      </div>

      <!-- Firearm chips -->
      <div
        v-if="session.firearms_used?.length || session.has_suppressor"
        class="mt-3 flex flex-wrap gap-1.5"
      >
        <span
          v-for="fw in session.firearms_used"
          :key="fw.firearm?.id"
          class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-line bg-[#fafbfb] px-[10px] py-[3px] text-[13px] text-ink-700"
        >
          <Home class="h-[13px] w-[13px] text-[#6b7077]" />
          {{ fw.firearm?.label ?? 'Unknown' }}
          <span class="font-mono text-[12px] text-muted">{{ fw.rounds.toLocaleString() }}</span>
        </span>
        <span
          v-if="session.has_suppressor"
          class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-special-border bg-special-bg px-[7px] py-[2px] font-mono text-[10px] text-special"
        >
          SUPPRESSED
        </span>
      </div>
    </div>
  </div>
</template>
