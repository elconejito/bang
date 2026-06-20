<script setup>
import { ref, computed, onMounted } from 'vue';
import { Plus, Trash2 } from 'lucide-vue-next';
import dayjs from 'dayjs';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import AddSessionLineModal from '@/components/training/AddSessionLineModal.vue';
import EditSessionLineModal from '@/components/training/EditSessionLineModal.vue';
import AddTargetModal from '@/components/training/AddTargetModal.vue';
import { useTrainingStore } from '@/stores/training';

const props = defineProps({
  trainingId: { type: Number, required: true },
});

const trainingStore = useTrainingStore();

const session = ref(null);
const loading = ref(true);
const editingLine = ref(null);
const addingLine = ref(false);
const addingTarget = ref(false);
const deletingTargetId = ref(null);

async function loadSession() {
  const { data } = await trainingStore.fetchOne(props.trainingId);
  session.value = data;
}

onMounted(async () => {
  await loadSession();
  loading.value = false;
});

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Training', to: { name: 'TrainingIndex' } },
  { label: session.value?.label ?? '…' },
]);

const linesWithDeduction = computed(() =>
  (session.value?.lines ?? []).filter((l) => l.deduct_ammo),
);

const linesWithFirearmCount = computed(() =>
  (session.value?.lines ?? []).filter((l) => l.add_firearm_count),
);

const linesWithSuppressorCount = computed(() =>
  (session.value?.lines ?? []).filter((l) => l.add_suppressor_count && l.suppressor),
);

async function onLineCreated() {
  addingLine.value = false;
  await loadSession();
}

async function onLineUpdated() {
  editingLine.value = null;
  await loadSession();
}

async function onLineDeleted() {
  editingLine.value = null;
  await loadSession();
}

function onTargetCreated(target) {
  addingTarget.value = false;
  session.value.targets.push(target);
  session.value.target_count = (session.value.target_count ?? 0) + 1;
}

async function deleteTarget(targetId) {
  deletingTargetId.value = targetId;
  try {
    await trainingStore.deleteTarget(props.trainingId, targetId);
    session.value.targets = session.value.targets.filter((t) => t.id !== targetId);
    session.value.target_count = Math.max(0, (session.value.target_count ?? 1) - 1);
  } finally {
    deletingTargetId.value = null;
  }
}
</script>

<template>
  <div class="max-w-[1280px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-5" />

    <div v-if="loading" class="text-sm text-muted py-12 text-center">Loading…</div>

    <template v-else-if="session">
      <!-- Header -->
      <div class="flex items-start gap-4 mb-6 flex-wrap">
        <div class="flex-1 min-w-0">
          <h1 class="font-display font-bold text-[28px] tracking-[-0.02em] leading-tight">{{ session.label }}</h1>
          <div class="text-[15px] text-[#6b7077] mt-1">
            {{ dayjs(session.session_date).format('ddd, MMM D, YYYY') }}
            <template v-if="session.range"> · {{ session.range.label }}</template>
          </div>
        </div>
        <router-link
          :to="{ name: 'TrainingEdit', params: { training_id: session.id } }"
          class="inline-flex items-center gap-1.5 bg-white text-[#1a1c1f] font-semibold text-[14px] px-[14px] py-2 rounded border border-[#c2c6ca] hover:bg-[#f5f6f7] transition-colors"
        >
          <svg class="w-[15px] h-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
          Edit
        </router-link>
      </div>

      <!-- Two-col layout -->
      <div class="grid grid-cols-[320px_1fr] gap-6 items-start">

        <!-- Left -->
        <div class="flex flex-col gap-4">
          <!-- Stat grid -->
          <div class="grid grid-cols-2 gap-2">
            <div class="bg-white border border-[#e2e4e6] rounded-sm px-3 py-3">
              <div class="font-mono text-[10px] text-muted tracking-[0.06em] mb-1">ROUNDS FIRED</div>
              <div class="font-display font-bold text-[22px] leading-none">{{ session.total_rounds.toLocaleString() }}</div>
            </div>
            <div class="bg-white border border-[#e2e4e6] rounded-sm px-3 py-3">
              <div class="font-mono text-[10px] text-muted tracking-[0.06em] mb-1">FIREARMS</div>
              <div class="font-display font-bold text-[22px] leading-none">{{ session.firearms_count }}</div>
            </div>
            <div class="bg-white border border-[#e2e4e6] rounded-sm px-3 py-3">
              <div class="font-mono text-[10px] text-muted tracking-[0.06em] mb-1">TARGETS</div>
              <div class="font-display font-bold text-[22px] leading-none">{{ session.target_count }}</div>
            </div>
            <div class="bg-white border border-[#e2e4e6] rounded-sm px-3 py-3">
              <div class="font-mono text-[10px] text-muted tracking-[0.06em] mb-1">LINES</div>
              <div class="font-display font-bold text-[22px] leading-none">{{ session.lines.length }}</div>
            </div>
          </div>

          <!-- Applied to your data -->
          <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-[#eef0f1] font-display font-semibold text-[15px]">Applied to your data</div>

            <!-- Ammo deducted -->
            <div v-if="linesWithDeduction.length" class="px-4 py-3 border-b border-[#f1f2f3]">
              <div class="font-mono text-[10px] text-muted tracking-[0.06em] mb-2">AMMO DEDUCTED</div>
              <div v-for="line in linesWithDeduction" :key="line.id" class="flex items-center justify-between text-[13px] py-0.5">
                <span class="text-[#3a3e44]">{{ line.ammunition?.label ?? '—' }}</span>
                <span class="font-mono text-muted">−{{ line.rounds }}</span>
              </div>
            </div>

            <!-- Firearm counts -->
            <div v-if="linesWithFirearmCount.length" class="px-4 py-3 border-b border-[#f1f2f3]">
              <div class="font-mono text-[10px] text-muted tracking-[0.06em] mb-2">FIREARM COUNTS</div>
              <div v-for="line in linesWithFirearmCount" :key="line.id" class="flex items-center justify-between text-[13px] py-0.5">
                <span class="text-[#3a3e44]">{{ line.firearm?.label ?? '—' }}</span>
                <span class="font-mono text-[#2f7d57]">+{{ line.rounds }}</span>
              </div>
            </div>

            <!-- Suppressor counts -->
            <div v-if="linesWithSuppressorCount.length" class="px-4 py-3">
              <div class="font-mono text-[10px] text-muted tracking-[0.06em] mb-2">SUPPRESSOR COUNTS</div>
              <div v-for="line in linesWithSuppressorCount" :key="line.id" class="flex items-center justify-between text-[13px] py-0.5">
                <span class="text-[#3a3e44]">{{ line.suppressor?.label ?? '—' }}</span>
                <span class="font-mono text-[#2f7d57]">+{{ line.rounds }}</span>
              </div>
            </div>

            <div v-if="!linesWithDeduction.length && !linesWithFirearmCount.length && !linesWithSuppressorCount.length" class="px-4 py-4 text-[13px] text-muted">
              No effects applied.
            </div>
          </div>

          <!-- Notes -->
          <div v-if="session.description" class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-[#eef0f1] font-display font-semibold text-[15px]">Notes</div>
            <div class="px-4 py-3 text-[14px] text-[#3a3e44] whitespace-pre-wrap">{{ session.description }}</div>
          </div>
        </div>

        <!-- Right -->
        <div class="flex flex-col gap-4">
          <!-- Shooting lines -->
          <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
            <div class="flex items-center gap-3 px-[18px] py-4 border-b border-[#eef0f1]">
              <span class="font-display font-semibold text-[18px]">Shooting lines</span>
              <span class="font-mono text-[11px] text-muted tracking-[0.04em]">{{ session.lines.length }} LINE{{ session.lines.length !== 1 ? 'S' : '' }}</span>
              <button
                class="ml-auto inline-flex items-center gap-1 text-[13px] font-medium text-brass hover:text-[#b08a2e] transition-colors"
                @click="addingLine = true"
              >
                <svg class="w-[14px] h-[14px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Add line
              </button>
            </div>

            <div v-if="!session.lines.length" class="px-[18px] py-8 text-center text-muted text-[14px]">
              No shooting lines logged.
            </div>

            <div
              v-for="line in session.lines"
              :key="line.id"
              class="px-[18px] py-4 border-b border-[#eef0f1] last:border-b-0"
            >
              <div class="flex items-start justify-between gap-3">
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2 flex-wrap mb-1">
                    <span class="font-semibold text-[15px]">{{ line.firearm?.label ?? '—' }}</span>
                    <span v-if="line.suppressor" class="font-mono text-[10px] border border-[#9ccbb1] rounded-sm px-[6px] py-[1px] text-[#2f7d57] bg-[#e7f1eb]">SUPPRESSED · {{ line.suppressor.label }}</span>
                  </div>
                  <div class="text-[13px] text-[#6b7077]">
                    {{ line.ammunition?.label ?? '—' }}
                  </div>
                </div>
                <div class="flex items-center gap-3">
                  <span class="font-mono text-[13px] text-ink-900 font-semibold whitespace-nowrap">{{ line.rounds.toLocaleString() }} RDS</span>
                  <button
                    class="p-1 text-muted hover:text-ink-900 transition-colors"
                    title="Edit line"
                    @click="editingLine = line"
                  >
                    <svg class="w-[15px] h-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Targets -->
          <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
            <div class="flex items-center gap-3 px-[18px] py-4 border-b border-[#eef0f1]">
              <span class="font-display font-semibold text-[18px]">Targets</span>
              <span v-if="session.target_count" class="font-mono text-[11px] tracking-[0.04em] text-muted">
                {{ session.target_count }} TARGET{{ session.target_count !== 1 ? 'S' : '' }}
              </span>
              <button
                class="ml-auto inline-flex items-center gap-1 text-[13px] font-semibold text-brass-800 hover:text-brass-900 transition-colors"
                @click="addingTarget = true"
              >
                <Plus class="h-[14px] w-[14px]" />
                Add target
              </button>
            </div>

            <div v-if="!session.targets?.length" class="px-[18px] py-10 text-center text-muted text-[14px]">
              No targets logged for this session.
            </div>

            <div v-else class="grid grid-cols-3 gap-3 p-[18px]">
              <div
                v-for="target in session.targets"
                :key="target.id"
                class="group relative overflow-hidden rounded border border-[#e2e4e6] bg-ink-50"
              >
                <img
                  :src="target.medium_url"
                  :alt="target.label || `Target at ${target.distance} yds`"
                  class="w-full object-cover"
                  style="aspect-ratio: 4/3;"
                />
                <!-- Overlay -->
                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-[rgba(20,22,26,0.72)] to-transparent px-2.5 pb-2 pt-6">
                  <div class="text-white">
                    <div class="font-mono text-[11px] tracking-[0.04em] opacity-90">{{ target.distance }} yds · {{ target.group_size }}"</div>
                    <div v-if="target.label" class="text-[12px] font-medium leading-tight mt-0.5">{{ target.label }}</div>
                  </div>
                </div>
                <!-- Delete -->
                <button
                  class="absolute right-1.5 top-1.5 flex h-6 w-6 items-center justify-center rounded bg-[rgba(20,22,26,0.6)] text-white opacity-0 group-hover:opacity-100 transition-opacity hover:bg-[rgba(192,57,43,0.85)]"
                  :disabled="deletingTargetId === target.id"
                  @click="deleteTarget(target.id)"
                >
                  <Trash2 class="h-[13px] w-[13px]" />
                </button>
              </div>
            </div>
          </div>
        </div>

      </div>
    </template>

    <AddSessionLineModal
      v-if="addingLine"
      :training-id="trainingId"
      @close="addingLine = false"
      @created="onLineCreated"
    />

    <EditSessionLineModal
      v-if="editingLine"
      :line="editingLine"
      :training-id="trainingId"
      @close="editingLine = null"
      @updated="onLineUpdated"
      @deleted="onLineDeleted"
    />

    <AddTargetModal
      v-if="addingTarget"
      :training-id="trainingId"
      @close="addingTarget = false"
      @created="onTargetCreated"
    />
  </div>
</template>
